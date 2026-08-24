<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CertificateTemplateController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth', 'verified'];
    }

    public function index(): Response
    {
        $templates = CertificateTemplat::latest()->get()->map(function ($template) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'slug' => $template->slug,
                'background_url' => $template->background_image ? Storage::url($template->background_image) : null,
                'orientation' => $template->orientation,
                'width' => $template->width,
                'height' => $template->height,
                'is_active' => $template->is_active,
                'created_at' => $template->created_at,
            ];
        });

        return Inertia::render('certificate-template/index', [
            'templates' => $templates,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('certificate-template/create');
    }

    public function store(Request $request): RedirectResponse
    {
        $messages = [
            'name.required' => 'Nama template wajib diisi.',
            'background_image.image' => 'File latar belakang harus berupa gambar.',
            'background_image.mimes' => 'Harap unggah gambar dengan format JPG, JPEG, atau PNG.',
            'background_image.max' => 'Ukuran gambar maksimal adalah 2MB.',
        ];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'orientation' => ['required', 'in:landscape,portrait'],
            'width' => ['required', 'integer', 'min:100'],
            'height' => ['required', 'integer', 'min:100'],
            'is_active' => ['boolean'],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'elements' => ['nullable', 'array'],
        ], $messages);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = Auth::id();

        if ($request->hasFile('background_image')) {
            $validated['background_image'] = $request->file('background_image')->store('certificate-backgrounds', 'public');
        }

        CertificateTemplat::create($validated);

        return redirect()->route('certificate-template.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Template sertifikat berhasil dibuat.',
                ],
            ]);
    }

    public function edit(CertificateTemplat $certificateTemplate): Response
    {
        $templateData = [
            'id' => $certificateTemplate->id,
            'name' => $certificateTemplate->name,
            'slug' => $certificateTemplate->slug,
            'background_url' => $certificateTemplate->background_image ? Storage::url($certificateTemplate->background_image) : null,
            'orientation' => $certificateTemplate->orientation,
            'width' => $certificateTemplate->width,
            'height' => $certificateTemplate->height,
            'elements' => $certificateTemplate->elements ?? [],
            'is_active' => $certificateTemplate->is_active,
        ];

        return Inertia::render('certificate-template/edit', [
            'template' => $templateData,
        ]);
    }

    public function update(Request $request, CertificateTemplat $certificateTemplate): RedirectResponse
    {
        $messages = [
            'name.required' => 'Nama template wajib diisi.',
            'background_image.image' => 'File latar belakang harus berupa gambar.',
            'background_image.mimes' => 'Harap unggah gambar dengan format JPG, JPEG, atau PNG.',
            'background_image.max' => 'Ukuran gambar maksimal adalah 2MB.',
        ];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'orientation' => ['required', 'in:landscape,portrait'],
            'width' => ['required', 'integer', 'min:100'],
            'height' => ['required', 'integer', 'min:100'],
            'is_active' => ['boolean'],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'elements' => ['nullable', 'array'],
        ], $messages);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('background_image')) {
            if ($certificateTemplate->background_image && Storage::disk('public')->exists($certificateTemplate->background_image)) {
                Storage::disk('public')->delete($certificateTemplate->background_image);
            }
            $validated['background_image'] = $request->file('background_image')->store('certificate-backgrounds', 'public');
        } else {
            unset($validated['background_image']);
        }

        $certificateTemplate->update($validated);

        return redirect()->route('certificate-template.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Template sertifikat berhasil diperbarui.',
                ],
            ]);
    }

    public function destroy(CertificateTemplat $certificateTemplate): RedirectResponse
    {
        if ($certificateTemplate->background_image && Storage::disk('public')->exists($certificateTemplate->background_image)) {
            Storage::disk('public')->delete($certificateTemplate->background_image);
        }

        $certificateTemplate->delete();

        return redirect()->route('certificate-template.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Template sertifikat berhasil dihapus.',
                ],
            ]);
    }
}
