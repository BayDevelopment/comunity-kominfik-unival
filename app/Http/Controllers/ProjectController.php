<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                $request->search,
                fn($q, $search) =>
                $q->where('nama', 'like', "%{$search}%")
            )
            ->when(
                $request->status,
                fn($q, $status) =>
                $q->where('status', $status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn($project) => [
                ...$project->toArray(),
                'gambar' => $project->gambar
                    ? asset('storage/' . $project->gambar)
                    : null,
            ]);

        return Inertia::render('project/index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status']),
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
            'nama'      => ['required', 'string', 'max:255'],
            'gambar'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'deskripsi' => ['nullable', 'string'],
            'klien'     => ['required', 'string', 'max:255'],
            'pic'       => ['required', 'string', 'max:255'],
            'teknologi' => ['nullable', 'string', 'max:255'],
            'status'    => ['required', 'in:aktif,selesai,ditunda,dibatalkan'],
            'progres'   => ['required', 'integer', 'min:0', 'max:100'],
            'mulai'     => ['required', 'date'],
            'selesai'   => ['nullable', 'date', 'after_or_equal:mulai'],
        ]);

        $validated['progress'] = $validated['progres'] ?? 0;
        unset($validated['progres']);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('project', 'public');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
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
