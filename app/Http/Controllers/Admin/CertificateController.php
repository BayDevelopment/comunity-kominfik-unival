<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateProgram;
use App\Models\CertificateTemplat; // Sesuaikan dengan nama model Anda
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            ->with('program')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('recipient_name', 'like', "%{$search}%")
                        ->orWhere('certificate_number', 'like', "%{$search}%")
                        ->orWhere('recipient_email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->program_id, function ($query, $programId) {
                $query->where('certificate_program_id', $programId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('certificate/index', [
            'certificates' => $certificates,
            'programs' => CertificateProgram::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
            'stats' => [
                'total' => Certificate::count(),
                'published' => Certificate::where('status', 'published')->count(),
                'draft' => Certificate::where('status', 'draft')->count(),
                'revoked' => Certificate::where('status', 'revoked')->count(),
            ],
            'filters' => [
                'search' => $request->search ?? '',
                'status' => $request->status ?? '',
                'program_id' => $request->program_id ?? '',
            ],
        ]);
    }

    /**
     * Form terbitkan sertifikat baru.
     */
    public function create(): Response
    {
        $templates = CertificateTemplat::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $programs = CertificateProgram::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('certificate/create', [
            'templates' => $templates,
            'programs' => $programs,
        ]);
    }

    /**
     * Simpan sertifikat baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'certificate_template_id' => ['required', 'exists:certificate_templates,id'],
            'certificate_program_id'  => ['nullable', 'exists:certificate_programs,id'],
            'recipient_name'          => ['required', 'string', 'max:255'],
            'recipient_email'         => ['nullable', 'email', 'max:255'],
            'event_name'              => ['nullable', 'string', 'max:255'],
            'course_name'             => ['nullable', 'string', 'max:255'],
            'description'             => ['nullable', 'string'],
            'issued_at'               => ['nullable', 'date'],
            'expired_at'              => ['nullable', 'date', 'after_or_equal:issued_at'],
            'signed_by'               => ['nullable', 'string', 'max:255'],
            'signatory_name'          => ['nullable', 'string', 'max:255'],
            'signature_image'         => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'status'                  => ['required', 'in:draft,published'],
        ]);

        if ($request->hasFile('signature_image')) {
            $validated['signatory_signature_path'] = $request->file('signature_image')->store('signatures', 'public');
        }

        $certificate = Certificate::create($validated);

        return redirect()
            ->route('certificate.show', $certificate)
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Sertifikat berhasil diterbitkan.',
                ],
            ]);
    }

    public function show(Certificate $certificate): Response
    {
        $certificate->load([
            'template',
            'program',
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
        $certificate->load([
            'template',
            'program',
        ]);

        $templates = CertificateTemplat::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $programs = CertificateProgram::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('certificate/edit', [
            'certificate' => $certificate,
            'templates'   => $templates,
            'programs'    => $programs,
        ]);
    }

    /**
     * Update sertifikat.
     */
    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'certificate_template_id' => ['required', 'exists:certificate_templates,id'],
            'certificate_program_id'  => ['nullable', 'exists:certificate_programs,id'],
            'recipient_name'          => ['required', 'string', 'max:255'],
            'recipient_email'         => ['nullable', 'email', 'max:255'],
            'event_name'              => ['nullable', 'string', 'max:255'],
            'course_name'             => ['nullable', 'string', 'max:255'],
            'description'             => ['nullable', 'string'],
            'issued_at'               => ['nullable', 'date'],
            'expired_at'              => ['nullable', 'date', 'after_or_equal:issued_at'],
            'signed_by'               => ['nullable', 'string', 'max:255'],
            'signatory_name'          => ['nullable', 'string', 'max:255'],
            'signature_image'         => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'status'                  => ['required', 'in:draft,published,revoked'],
        ]);

        if ($request->hasFile('signature_image')) {
            if ($certificate->signatory_signature_path && Storage::disk('public')->exists($certificate->signatory_signature_path)) {
                Storage::disk('public')->delete($certificate->signatory_signature_path);
            }
            $validated['signatory_signature_path'] = $request->file('signature_image')->store('signatures', 'public');
        }

        $certificate->update($validated);

        // Hapus file cache PDF lama agar ter-generate ulang dengan data terbaru
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
            $certificate->update(['file_path' => null]);
        }

        return redirect()
            ->route('certificate.show', $certificate)
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
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
                    'type' => 'success',
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

        $certificate->revoke(
            $validated['revoke_reason'],
            Auth::id()
        );

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Sertifikat berhasil dicabut.',
            ],
        ]);
    }

    /**
     * Download PDF sertifikat.
     */
    public function download(Certificate $certificate)
    {
        if (method_exists($certificate, 'recordDownload')) {
            $certificate->recordDownload();
        }

        $certificate->load(['template', 'program']);

        $template = $certificate->template;
        $width = $template?->width ?? 842;
        $height = $template?->height ?? 595;

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'template'    => $template,
        ]);

        if ($template && $template->width && $template->height) {
            $pdf->setPaper([0, 0, $width, $height], 'landscape');
        } else {
            $pdf->setPaper('a4', 'landscape');
        }

        $safeNumber = preg_replace('/[\/\\\\]+/', '-', $certificate->certificate_number);
        $filename   = "Sertifikat-{$safeNumber}.pdf";

        return $pdf->stream($filename);
    }
}