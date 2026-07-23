<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Anggota::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter divisi
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        // Urutkan dari yang terbaru
        $query->orderBy('created_at', 'desc');

        $anggotas = $query->paginate(10)->withQueryString();

        return Inertia::render('anggota/index', [
            'anggotas' => $anggotas,
            'filters' => $request->only(['search', 'status', 'divisi'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('anggota/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->can('create-anggota')) {
            abort(403, 'Anda tidak memiliki akses!');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'email' => 'required|email|max:255|unique:anggotas,email',
            'no_telepon' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'jabatan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'tanggal_bergabung' => 'required|date|before_or_equal:today',
            'status' => 'required|in:aktif,tidak_aktif,cuti',
        ], [
            'email.unique' => 'Email sudah terdaftar!',
            'foto.max' => 'Ukuran foto maksimal 1MB!',
            'foto.mimes' => 'Format foto harus JPG, PNG!',
            'no_telepon.regex' => 'Format nomor telepon tidak valid!',
            'tanggal_bergabung.before_or_equal' => 'Tanggal bergabung tidak boleh di masa depan!',
        ]);

        // 3. UPLOAD FOTO
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
            $fotoPath = $file->storeAs('anggota/foto', $filename, 'public');
        }

        $anggota = Anggota::create([
            'nama' => $validated['nama'],
            'foto' => $fotoPath,
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telepon'],
            'jabatan' => $validated['jabatan'],
            'divisi' => $validated['divisi'],
            'alamat' => $validated['alamat'],
            'tanggal_bergabung' => $validated['tanggal_bergabung'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('anggota')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Anggota "' . $anggota->nama . '" berhasil ditambahkan!',
                ],
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Anggota $anggota): Response
    {
        return Inertia::render('anggota/view', [
            'anggota' => [
                ...$anggota->toArray(),
                'foto_url' => $anggota->foto
                    ? asset('storage/' . $anggota->foto)
                    : null,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anggota $anggota): Response
    {
        return Inertia::render('anggota/edit', [
            'anggota' => [
                ...$anggota->toArray(),
                'foto_url' => $anggota->foto
                    ? asset('storage/' . $anggota->foto)
                    : null,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Anggota $anggota): RedirectResponse
    {
        if (!Auth::user()->can('update-anggota')) {
            abort(403, 'Anda tidak memiliki akses!');
        }

        // VALIDASI
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'email' => 'required|email|max:255|unique:anggotas,email,' . $anggota->id,
            'no_telepon' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'jabatan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'tanggal_bergabung' => 'required|date|before_or_equal:today',
            'status' => 'required|in:aktif,tidak_aktif,cuti',
        ], [
            'email.unique' => 'Email sudah terdaftar!',
            'foto.max' => 'Ukuran foto maksimal 1MB!',
            'foto.mimes' => 'Format foto harus JPG, PNG!',
            'no_telepon.regex' => 'Format nomor telepon tidak valid!',
            'tanggal_bergabung.before_or_equal' => 'Tanggal bergabung tidak boleh di masa depan!',
        ]);

        // UPDATE FOTO (JIKA ADA FILE BARU)
        if ($request->hasFile('foto')) {
            if ($anggota->foto && Storage::disk('public')->exists($anggota->foto)) {
                Storage::disk('public')->delete($anggota->foto);
            }

            $file = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama) . '.' . $file->getClientOriginalExtension();
            $fotoPath = $file->storeAs('anggota/foto', $filename, 'public');
            $validated['foto'] = $fotoPath;
        } else {
            // ✅ TIDAK ada file baru → jangan sentuh kolom foto sama sekali
            unset($validated['foto']);
        }

        // UPDATE DATA
        $anggota->update($validated);

        // REDIRECT
        return redirect()
            ->route('anggota')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Anggota "' . $anggota->nama . '" berhasil diupdate!',
                ],
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anggota $anggota): RedirectResponse
    {
        if (!Auth::user()->can('delete-anggota')) {
            abort(403, 'Anda tidak memiliki akses!');
        }

        if ($anggota->foto && Storage::disk('public')->exists($anggota->foto)) {
            Storage::disk('public')->delete($anggota->foto);
        }

        $anggota->delete();

        return redirect()
            ->route('anggota')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Anggota "' . $anggota->nama . '" berhasil dihapus!',
                ],
            ]);
    }
}
