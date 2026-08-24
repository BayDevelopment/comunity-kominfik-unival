<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CertificateController extends Controller
{
    /**
     * Daftar sertifikat.
     */
    public function index(Request $request): Response
    {
        $certificates = Certificate::query()
            ->with(['template'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('recipient_name', 'like', "%{$search}%")
                        ->orWhere('certificate_number', 'like', "%{$search}%")
                        ->orWhere('recipient_email', 'like', "%{$search}%")
                        ->orWhere('event_name', 'like', "%{$search}%")
                        ->orWhere('course_name', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('certificate/index', [
            'certificates' => $certificates,
            'stats' => [
                'total'     => Certificate::count(),
                'published' => Certificate::where('status', 'published')->count(),
                'draft'     => Certificate::where('status', 'draft')->count(),
                'revoked'   => Certificate::where('status', 'revoked')->count(),
            ],
            'filters' => [
                'search' => $request->search ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }

    /**
     * Form terbitkan sertifikat baru.
     */
    public function create(): Response
    {
        $templates = CertificateTemplat::active()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('certificate/create', [
            'templates' => $templates,
        ]);
    }

    /**
     * Simpan sertifikat baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'certificate_template_id' => ['required', 'exists:certificate_templates,id'],
            'recipient_name'          => ['required', 'string', 'max:255'],
            'recipient_email'         => ['nullable', 'email', 'max:255'],
            'event_name'              => ['nullable', 'string', 'max:255'],
            'course_name'             => ['nullable', 'string', 'max:255'],
            'description'             => ['nullable', 'string'],
            'signatory_name'          => ['nullable', 'string', 'max:255'],
            'signatory_role'          => ['nullable', 'string', 'max:255'],
            'issued_at'               => ['required', 'date'],
            'expired_at'              => ['nullable', 'date', 'after_or_equal:issued_at'],
            'signature_image'         => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'status'                  => ['required', 'in:draft,published'],
        ]);

        if ($request->hasFile('signature_image')) {
            $validated['signatory_signature_path'] = $request->file('signature_image')->store('signatures', 'public');
        }
        unset($validated['signature_image']);

        $validated['uuid']               = (string) Str::uuid();
        $validated['certificate_number'] = 'CERT-' . strtoupper(Str::random(8));
        $validated['verification_code']  = Str::random(32);
        $validated['user_id']            = Auth::id();

        $certificate = Certificate::create($validated);

        return redirect()
            ->route('certificate.show', $certificate->id)
            ->with('flash', [
                'toast' => [
                    'type'    => 'success',
                    'message' => 'Sertifikat berhasil diterbitkan.',
                ],
            ]);
    }

    /**
     * Detail sertifikat.
     */
    public function show(Certificate $certificate): Response
    {
        $certificate->load([
            'template',
            'revokedBy',
        ]);

        return Inertia::render('certificate/show', [
            'certificate' => $certificate,
        ]);
    }

    /**
     * Form edit sertifikat.
     */
    public function edit(Certificate $certificate): Response
    {
        $certificate->load(['template']);

        $templates = CertificateTemplat::active()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('certificate/edit', [
            'certificate' => $certificate,
            'templates'   => $templates,
        ]);
    }

    /**
     * Update sertifikat.
     */
    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'certificate_template_id' => ['required', 'exists:certificate_templates,id'],
            'recipient_name'          => ['required', 'string', 'max:255'],
            'recipient_email'         => ['nullable', 'email', 'max:255'],
            'event_name'              => ['nullable', 'string', 'max:255'],
            'course_name'             => ['nullable', 'string', 'max:255'],
            'description'             => ['nullable', 'string'],
            'signatory_name'          => ['nullable', 'string', 'max:255'],
            'signatory_role'          => ['nullable', 'string', 'max:255'],
            'issued_at'               => ['required', 'date'],
            'expired_at'              => ['nullable', 'date', 'after_or_equal:issued_at'],
            'signature_image'         => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'status'                  => ['required', 'in:draft,published,revoked'],
        ]);

        if ($request->hasFile('signature_image')) {
            if ($certificate->signatory_signature_path && Storage::disk('public')->exists($certificate->signatory_signature_path)) {
                Storage::disk('public')->delete($certificate->signatory_signature_path);
            }
            $validated['signatory_signature_path'] = $request->file('signature_image')->store('signatures', 'public');
        }
        unset($validated['signature_image']);

        $certificate->update($validated);

        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
            $certificate->update(['file_path' => null]);
        }

        return redirect()
            ->route('certificate.show', $certificate->id)
            ->with('flash', [
                'toast' => [
                    'type'    => 'success',
                    'message' => 'Sertifikat berhasil diperbarui.',
                ],
            ]);
    }

    /**
     * Hapus sertifikat.
     */
    public function destroy(Certificate $certificate)
    {
        if ($certificate->signatory_signature_path && Storage::disk('public')->exists($certificate->signatory_signature_path)) {
            Storage::disk('public')->delete($certificate->signatory_signature_path);
        }

        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return redirect()
            ->route('certificate.index')
            ->with('flash', [
                'toast' => [
                    'type'    => 'success',
                    'message' => 'Sertifikat berhasil dihapus.',
                ],
            ]);
    }

    /**
     * Cabut sertifikat.
     */
    public function revoke(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'revoke_reason' => ['required', 'string', 'max:500'],
        ]);

        $certificate->update([
            'status'        => 'revoked',
            'revoke_reason' => $validated['revoke_reason'],
            'revoked_at'    => now(),
            'revoked_by'    => Auth::id(),
        ]);

        return back()->with('flash', [
            'toast' => [
                'type'    => 'success',
                'message' => 'Sertifikat berhasil dicabut.',
            ],
        ]);
    }

    /**
     * Download PDF sertifikat.
     */
    public function download(Certificate $certificate)
    {
        $certificate->increment('download_count');
        $certificate->update(['last_downloaded_at' => now()]);

        $certificate->load(['template']);

        $template = $certificate->template;
        $width  = $template?->width ?? 842;
        $height = $template?->height ?? 595;

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'template'    => $template,
        ]);

        if ($template && $template->width && $template->height) {
            $pdf->setPaper([0, 0, $width, $height], $template->orientation ?? 'landscape');
        } else {
            $pdf->setPaper('a4', 'landscape');
        }

        $safeNumber = preg_replace('/[\/\\\\]+/', '-', $certificate->certificate_number);
        $filename   = "Sertifikat-{$safeNumber}.pdf";

        return $pdf->stream($filename);
    }
}