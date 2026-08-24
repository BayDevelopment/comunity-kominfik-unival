<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CertificateVerificationController extends Controller
{
    /**
     * Halaman publik "Cek Sertifikat" (form pencarian).
     * Hasil pencarian (kalau ada) dibaca dari session flash yang di-set oleh search().
     */
    public function index(): Response
    {
        return Inertia::render('certificate/Check', [
            'searchResult' => session('certificate_search'),
        ]);
    }

    /**
     * Proses pencarian sertifikat lewat form Inertia biasa (useForm().post()).
     * Rate-limited lewat middleware 'throttle:5,1' di route.
     */
    public function search(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:255'],
            'recipient_name'  => ['required', 'string', 'max:255'],
        ]);

        // Bersihkan input email dan nama
        $email = trim(strtolower($validated['recipient_email']));
        $name  = trim($validated['recipient_name']);

        $certificate = Certificate::query()
            ->with(['program:id,name', 'template'])
            ->where('recipient_email', $email)
            ->where('recipient_name', 'like', '%' . $name . '%')
            ->whereIn('status', ['published', 'revoked'])
            ->first();

        if (! $certificate) {
            return back()
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Sertifikat tidak ditemukan. Periksa kembali nama dan email Anda.',
                    ],
                ])
                ->with('certificate_search', [
                    'found' => false,
                    'data'  => null,
                ]);
        }

        // Jika sertifikat dicabut
        if ($certificate->status === 'revoked') {
            return back()
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Sertifikat ini telah dicabut (revoked).',
                    ],
                ])
                ->with('certificate_search', [
                    'found' => true,
                    'data'  => [
                        'recipient_name'     => $certificate->recipient_name,
                        'certificate_number' => $certificate->certificate_number,
                        'program_name'       => $certificate->program?->name,
                        'description'        => $certificate->description,
                        'issued_at'          => $certificate->issued_at?->toDateString(),
                        'status'             => $certificate->status,
                        'is_expired'         => $certificate->isExpired(),
                        'verification_code'  => $certificate->verification_code,
                    ],
                ]);
        }

        return back()
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Sertifikat berhasil ditemukan!',
                ],
            ])
            ->with('certificate_search', [
                'found' => true,
                'data'  => [
                    'recipient_name'     => $certificate->recipient_name,
                    'certificate_number' => $certificate->certificate_number,
                    'program_name'       => $certificate->program?->name,
                    'description'        => $certificate->description,
                    'issued_at'          => $certificate->issued_at?->toDateString(),
                    'status'             => $certificate->status,
                    'is_expired'         => $certificate->isExpired(),
                    'verification_code'  => $certificate->verification_code,
                ],
            ]);
    }

    /**
     * Generate & unduh PDF sertifikat.
     */
    public function download(string $verificationCode)
    {
        $certificate = Certificate::query()
            ->with(['template', 'program'])
            ->where('verification_code', $verificationCode)
            ->where('status', 'published')
            ->firstOrFail();

        if ($certificate->isExpired()) {
            abort(410, 'Sertifikat sudah kedaluwarsa.');
        }

        if (method_exists($certificate, 'recordDownload')) {
            $certificate->recordDownload();
        }

        $template = $certificate->template;
        
        $width  = $template?->width ?? 842;
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

        return $pdf->download($filename);
    }
}