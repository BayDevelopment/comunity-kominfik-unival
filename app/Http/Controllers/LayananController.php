<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $layanans = Layanan::query()
            ->when(
                $request->filled('search'),
                fn($q) => $q->where(function ($query) use ($request) {
                    $search = $request->search;
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('status', $request->status)
            )
            ->when(
                $request->filled('kategori'),
                fn($q) => $q->where('kategori', $request->kategori)
            )
            ->when(
                $request->filled('biaya_min'),
                fn($q) => $q->where('biaya', '>=', $request->biaya_min)
            )
            ->when(
                $request->filled('biaya_max'),
                fn($q) => $q->where('biaya', '<=', $request->biaya_max)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn($layanan) => [
                ...$layanan->toArray(),
                'gambar_url' => $layanan->gambar
                    ? asset('storage/' . $layanan->gambar)
                    : null,
                'gambar' => $layanan->gambar, // Tetap sertakan path asli untuk keperluan edit
            ]);

        return Inertia::render('layanan/index', [
            'layanans' => $layanans,
            'filters' => $request->only(['search', 'status', 'kategori', 'biaya_min', 'biaya_max']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('layanan/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->can('create-layanan')) {
            abort(403, 'Anda tidak memiliki izin untuk menambah layanan.');
        }

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\_\.\&\(\)\:\/]+$/'
            ],
            'gambar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
            ],
            'kategori' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\_\.]+$/'
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:5000'
            ],
            'syarat' => [
                'nullable',
                'array',
            ],
            'syarat.*' => [
                'string',
                'max:255',
            ],
            'estimasi_waktu' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\_\.\,]+$/'
            ],
            'biaya' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[0-9,\.\s]+$/'
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif'])
            ],
        ]);

        // 2. SANITASI INPUT (Cegah XSS)
        $validated['nama'] = strip_tags(trim($validated['nama']));
        $validated['deskripsi'] = strip_tags(trim($validated['deskripsi'] ?? ''));

        if (!empty($validated['syarat'])) {
            $validated['syarat'] = array_map(
                fn($item) => strip_tags(trim($item)),
                $validated['syarat']
            );
        }

        $validated['kategori'] = strip_tags(trim($validated['kategori'] ?? ''));
        $validated['estimasi_waktu'] = strip_tags(trim($validated['estimasi_waktu'] ?? ''));

        if (!empty($validated['biaya'])) {
            $validated['biaya'] = preg_replace('/[^0-9]/', '', $validated['biaya']);
            $validated['biaya'] = (int) $validated['biaya'];
        }

        DB::beginTransaction();

        try {
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');

                if (!$file->isValid()) {
                    throw new \Exception('File gambar tidak valid atau rusak.');
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file->getPathname());
                finfo_close($finfo);

                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($mimeType, $allowedMimes)) {
                    throw new \Exception('Tipe file tidak diizinkan. Hanya JPG, PNG, dan WEBP.');
                }

                if ($file->getSize() > 2 * 1024 * 1024) {
                    throw new \Exception('Ukuran file melebihi batas maksimal 2MB.');
                }

                $extension = $file->getClientOriginalExtension();
                $safeFilename = Str::uuid() . '.' . $extension;
                $path = $file->storeAs('layanan', $safeFilename, 'public');

                if (!$path) {
                    throw new \Exception('Gagal mengupload gambar. Silakan coba lagi.');
                }

                $validated['gambar'] = $path;
            }

            // 5. SIMPAN KE DATABASE
            Layanan::create($validated);

            DB::commit();

            Log::info('Layanan berhasil ditambahkan', [
                'nama' => $validated['nama'],
                'user_id' => Auth::id() ?? 'guest',
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->route('layanan')
                ->with('flash', [
                    'toast' => [
                        'type' => 'success',
                        'message' => 'Data layanan berhasil ditambahkan.',
                    ],
                ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Terjadi kesalahan validasi data. Silakan periksa kembali input Anda.',
                    ],
                ])
                ->withErrors($e->errors());
        } catch (\Throwable $e) {
            DB::rollBack();

            if (!empty($validated['gambar']) && Storage::disk('public')->exists($validated['gambar'])) {
                Storage::disk('public')->delete($validated['gambar']);

                Log::warning('File gambar dihapus karena terjadi error', [
                    'file' => $validated['gambar'],
                    'error' => $e->getMessage()
                ]);
            }

            Log::error('Gagal menyimpan layanan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
                'ip' => $request->ip(),
                'data' => $request->except(['gambar']),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Gagal menyimpan layanan: ' . $e->getMessage(),
                    ],
                ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Layanan $layanan)
    {
        return Inertia::render('layanan/view', [
            'layanan' => array_merge($layanan->toArray(), [
                'gambar_url' => $layanan->gambar_url,
                'biaya_formatted' => $layanan->biaya_formatted,
            ]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Layanan $layanan)
    {
        return Inertia::render('layanan/edit', [
            'layanan' => $layanan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Layanan $layanan): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->can('update-layanan')) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah layanan.');
        }

        // 2. VALIDASI INPUT
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\_\.\&\(\)\:\/]+$/'
            ],
            'gambar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
            ],
            'hapus_gambar' => [
                'nullable',
                'boolean',
            ],
            'kategori' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\_\.]+$/'
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:5000'
            ],
            'syarat' => [
                'nullable',
                'array',
            ],
            'syarat.*' => [
                'string',
                'max:255',
            ],
            'estimasi_waktu' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\_\.\,]+$/'
            ],
            'biaya' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[0-9,\.\s]+$/'
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif'])
            ],
        ]);

        // 3. SANITASI INPUT (Cegah XSS)
        $validated['nama'] = strip_tags(trim($validated['nama']));
        $validated['deskripsi'] = strip_tags(trim($validated['deskripsi'] ?? ''));

        if (!empty($validated['syarat'])) {
            $validated['syarat'] = array_map(
                fn($item) => strip_tags(trim($item)),
                $validated['syarat']
            );
        }

        $validated['kategori'] = strip_tags(trim($validated['kategori'] ?? ''));
        $validated['estimasi_waktu'] = strip_tags(trim($validated['estimasi_waktu'] ?? ''));

        if (!empty($validated['biaya'])) {
            $validated['biaya'] = preg_replace('/[^0-9]/', '', $validated['biaya']);
            $validated['biaya'] = (int) $validated['biaya'];
        }

        $hapusGambar = $request->boolean('hapus_gambar');
        unset($validated['hapus_gambar']);

        $gambarLama = $layanan->gambar;
        $gambarBaruDiupload = false;

        DB::beginTransaction();

        try {
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');

                if (!$file->isValid()) {
                    throw new \Exception('File gambar tidak valid atau rusak.');
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file->getPathname());
                finfo_close($finfo);

                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($mimeType, $allowedMimes)) {
                    throw new \Exception('Tipe file tidak diizinkan. Hanya JPG, PNG, dan WEBP.');
                }

                if ($file->getSize() > 2 * 1024 * 1024) {
                    throw new \Exception('Ukuran file melebihi batas maksimal 2MB.');
                }

                $extension = $file->getClientOriginalExtension();
                $safeFilename = Str::uuid() . '.' . $extension;
                $path = $file->storeAs('layanan', $safeFilename, 'public');

                if (!$path) {
                    throw new \Exception('Gagal mengupload gambar. Silakan coba lagi.');
                }

                $validated['gambar'] = $path;
                $gambarBaruDiupload = true;
            } elseif ($hapusGambar) {
                $validated['gambar'] = null;
            } else {
                unset($validated['gambar']);
            }

            // 4. SIMPAN PERUBAHAN KE DATABASE
            $layanan->update($validated);

            // Hapus file gambar lama dari storage HANYA setelah update berhasil,
            // dan HANYA jika memang ada gambar baru yang menggantikannya atau user minta dihapus
            if (($gambarBaruDiupload || $hapusGambar) && $gambarLama && Storage::disk('public')->exists($gambarLama)) {
                Storage::disk('public')->delete($gambarLama);
            }

            DB::commit();

            Log::info('Layanan berhasil diperbarui', [
                'layanan_id' => $layanan->id,
                'nama' => $validated['nama'],
                'user_id' => Auth::id() ?? 'guest',
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->route('layanan')
                ->with('flash', [
                    'toast' => [
                        'type' => 'success',
                        'message' => 'Data layanan berhasil diperbarui.',
                    ],
                ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Terjadi kesalahan validasi data. Silakan periksa kembali input Anda.',
                    ],
                ])
                ->withErrors($e->errors());
        } catch (\Throwable $e) {
            DB::rollBack();

            // Kalau gambar baru sempat ter-upload tapi transaksi gagal,
            // hapus file barunya saja (bukan gambar lama, karena update dibatalkan)
            if ($gambarBaruDiupload && !empty($validated['gambar']) && Storage::disk('public')->exists($validated['gambar'])) {
                Storage::disk('public')->delete($validated['gambar']);

                Log::warning('File gambar baru dihapus karena terjadi error', [
                    'file' => $validated['gambar'],
                    'error' => $e->getMessage()
                ]);
            }

            Log::error('Gagal memperbarui layanan', [
                'layanan_id' => $layanan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
                'ip' => $request->ip(),
                'data' => $request->except(['gambar']),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Gagal memperbarui layanan: ' . $e->getMessage(),
                    ],
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Layanan $layanan): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->can('delete-layanan')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus layanan.');
        }

        DB::beginTransaction();

        try {
            $gambarPath = $layanan->gambar;
            $namaLayanan = $layanan->nama;

            $layanan->delete();

            DB::commit();

            if (!empty($gambarPath) && Storage::disk('public')->exists($gambarPath)) {
                Storage::disk('public')->delete($gambarPath);

                Log::info('File gambar layanan dihapus', [
                    'file' => $gambarPath,
                    'layanan' => $namaLayanan,
                    'user_id' => Auth::id() ?? 'guest',
                ]);
            }

            Log::info('Layanan berhasil dihapus', [
                'id' => $layanan->id,
                'nama' => $namaLayanan,
                'user_id' => Auth::id() ?? 'guest',
                'ip' => request()->ip(),
            ]);

            return redirect()
                ->route('layanan')
                ->with('flash', [
                    'toast' => [
                        'type' => 'success',
                        'message' => "Layanan '{$namaLayanan}' berhasil dihapus.",
                    ],
                ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal menghapus layanan', [
                'id' => $layanan->id,
                'nama' => $layanan->nama ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
                'ip' => request()->ip(),
            ]);

            return redirect()
                ->back()
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Gagal menghapus layanan: ' . $e->getMessage(),
                    ],
                ]);
        }
    }
}
