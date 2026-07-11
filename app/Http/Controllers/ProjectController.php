<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = Project::query()
            ->when(
                $request->filled('search'),
                fn($q) => $q->where(function ($query) use ($request) {
                    $search = $request->search;
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('klien', 'like', "%{$search}%")
                        ->orWhere('pic', 'like', "%{$search}%")
                        ->orWhere('teknologi', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('status', $request->status)
            )
            ->when(
                $request->filled('progress_min'),
                fn($q) => $q->where('progress', '>=', $request->progress_min)
            )
            ->when(
                $request->filled('progress_max'),
                fn($q) => $q->where('progress', '<=', $request->progress_max)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn($project) => [
                ...$project->toArray(),
                'gambar_url' => $project->gambar
                    ? asset('storage/' . $project->gambar)
                    : null,
                'gambar' => $project->gambar, // Tetap sertakan path asli untuk keperluan edit
            ]);

        return Inertia::render('project/index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status', 'progress_min', 'progress_max']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('project/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'       => ['required', 'string', 'max:255'],
            'gambar'     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'deskripsi'  => ['nullable', 'string', 'max:5000'],
            'klien'      => ['nullable', 'string', 'max:255'],
            'pic'        => ['nullable', 'string', 'max:255'],
            'teknologi'  => ['nullable', 'string', 'max:1000'],
            'status'     => ['required', 'in:aktif,selesai,ditunda,dibatalkan'],
            'progress'   => ['required', 'integer', 'min:0', 'max:100'],
            'mulai'      => ['nullable', 'date'],
            'selesai'    => ['nullable', 'date', 'after_or_equal:mulai'],
        ]);

        // Handle upload gambar dengan aman
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            // Validasi tambahan untuk keamanan
            if (!$file->isValid()) {
                return back()->withErrors(['gambar' => 'File gambar tidak valid.']);
            }

            // Sanitasi nama file
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $safeFilename = $filename . '_' . time() . '.' . $extension;

            $validated['gambar'] = $file->storeAs('project', $safeFilename, 'public');
        }

        Project::create($validated);

        return redirect()
            ->route('project')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Project berhasil ditambahkan.',
                ],
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return Inertia::render('project/view', [
            'project' => [
                ...$project->toArray(),
                'gambar_url' => $project->gambar
                    ? asset('storage/' . $project->gambar)
                    : null,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return Inertia::render('project/edit', [
            'project' => [
                ...$project->toArray(),
                'gambar' => $project->gambar
                    ? asset('storage/' . $project->gambar)
                    : null,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'gambar'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'deskripsi'     => ['nullable', 'string', 'max:5000'],
            'klien'         => ['nullable', 'string', 'max:255'],
            'pic'           => ['nullable', 'string', 'max:255'],
            'teknologi'     => ['nullable', 'string', 'max:1000'],
            'status'        => ['required', 'in:aktif,selesai,ditunda,dibatalkan'],
            'progress'      => ['required', 'integer', 'min:0', 'max:100'],
            'mulai'         => ['nullable', 'date'],
            'selesai'       => ['nullable', 'date', 'after_or_equal:mulai'],
            'hapus_gambar'  => ['nullable', 'boolean'],
        ]);

        // Handle upload gambar dengan aman
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');

            // Validasi tambahan untuk keamanan
            if (!$file->isValid()) {
                return back()->withErrors(['gambar' => 'File gambar tidak valid.']);
            }

            // Hapus gambar lama jika ada
            if ($project->gambar && Storage::disk('public')->exists($project->gambar)) {
                Storage::disk('public')->delete($project->gambar);
            }

            // Sanitasi nama file
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $safeFilename = $filename . '_' . time() . '.' . $extension;

            $validated['gambar'] = $file->storeAs('project', $safeFilename, 'public');
        } elseif ($request->boolean('hapus_gambar')) {
            // Hapus gambar jika diminta
            if ($project->gambar && Storage::disk('public')->exists($project->gambar)) {
                Storage::disk('public')->delete($project->gambar);
            }
            $validated['gambar'] = null;
        } else {
            // Pertahankan gambar yang ada
            unset($validated['gambar']);
        }

        unset($validated['hapus_gambar']);

        $project->update($validated);

        return redirect()
            ->route('project')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Project berhasil diperbarui.',
                ],
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        // Hapus gambar jika ada
        if ($project->gambar && Storage::disk('public')->exists($project->gambar)) {
            Storage::disk('public')->delete($project->gambar);
        }

        $project->delete();

        return redirect()
            ->route('project')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Project berhasil dihapus.',
                ],
            ]);
    }
}
