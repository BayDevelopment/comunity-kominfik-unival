<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranAnggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PendaftaranAnggotaController extends Controller
{
    private const FOTO_PATH = 'pendaftaran-anggota/foto';
    private const CV_PATH = 'pendaftaran-anggota/cv';

    public function index(Request $request): Response
    {
        $query = PendaftaranAnggota::query();

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim_nis', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->string('status')->trim()->value()) {
            $query->where('status', $status);
        }

        if ($jenjang = $request->string('jenjang')->trim()->value()) {
            $query->where('jenjang', $jenjang);
        }

        $pendaftarans = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('pendaftaran-anggota/index', [
            'pendaftarans' => $pendaftarans,
            'filters' => $request->only(['search', 'status', 'jenjang']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('pendaftaran-anggota/create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim_nis' => ['required', 'string', 'max:50'],
            'asal_instansi' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', Rule::in(['mahasiswa', 'sma', 'smk'])],
            'jurusan_prodi' => ['nullable', 'string', 'max:255'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pendaftaran_anggotas,email'],
            'no_telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'alasan_bergabung' => ['nullable', 'string', 'max:2000'],
            'file_cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:1024'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            'status' => ['sometimes', Rule::in(['pending', 'diterima', 'ditolak'])],
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'file_cv.mimes' => 'File CV harus berformat PDF, DOC, atau DOCX.',
            'file_cv.max' => 'Ukuran file CV maksimal 1MB.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 1MB.',
        ]);

        DB::transaction(function () use ($request, &$data) {
            if ($request->hasFile('foto')) {
                $data['foto'] = $this->storeUploadedFile($request->file('foto'), self::FOTO_PATH);
            }

            if ($request->hasFile('file_cv')) {
                $data['file_cv'] = $this->storeUploadedFile($request->file('file_cv'), self::CV_PATH);
            }

            // Karena ini input admin, kalau admin langsung set status selain pending,
            // catat siapa & kapan yang memproses.
            if (($data['status'] ?? 'pending') !== 'pending') {
                $data['diproses_oleh'] = $request->user()->id;
                $data['tanggal_diproses'] = now();
            }

            PendaftaranAnggota::create($data);
        });

        return redirect()
            ->route('pendaftaran-anggota.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Pendaftaran anggota berhasil dibuat.',
                ],
            ]);
    }

    public function show(PendaftaranAnggota $pendaftaranAnggota): Response
    {
        return Inertia::render('pendaftaran-anggota/view', [
            'pendaftaran' => $pendaftaranAnggota->load('diprosesOleh'),
        ]);
    }

    public function edit(PendaftaranAnggota $pendaftaranAnggota): Response
    {
        return Inertia::render('pendaftaran-anggota/edit', [
            'pendaftaran' => $pendaftaranAnggota,
        ]);
    }

    public function update(Request $request, PendaftaranAnggota $pendaftaranAnggota): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim_nis' => ['required', 'string', 'max:50'],
            'asal_instansi' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', Rule::in(['mahasiswa', 'sma', 'smk'])],
            'jurusan_prodi' => ['nullable', 'string', 'max:255'],
            'angkatan' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('pendaftaran_anggotas', 'email')->ignore($pendaftaranAnggota->id)],
            'no_telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'alasan_bergabung' => ['nullable', 'string', 'max:2000'],
            'file_cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:1024'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            'status' => ['sometimes', Rule::in(['pending', 'diterima', 'ditolak'])],
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'file_cv.mimes' => 'File CV harus berformat PDF, DOC, atau DOCX.',
            'file_cv.max' => 'Ukuran file CV maksimal 1MB.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 1MB.',
        ]);

        // ⬇️ TAMBAHKAN INI
        if (!$request->hasFile('foto')) {
            unset($data['foto']);
        }

        if (!$request->hasFile('file_cv')) {
            unset($data['file_cv']);
        }
        // ⬆️ SAMPAI SINI

        DB::transaction(function () use ($request, $pendaftaranAnggota, &$data) {
            if ($request->hasFile('foto')) {
                $this->deleteFileIfExists($pendaftaranAnggota->foto);
                $data['foto'] = $this->storeUploadedFile($request->file('foto'), self::FOTO_PATH);
            }

            if ($request->hasFile('file_cv')) {
                $this->deleteFileIfExists($pendaftaranAnggota->file_cv);
                $data['file_cv'] = $this->storeUploadedFile($request->file('file_cv'), self::CV_PATH);
            }

            if (isset($data['status']) && $data['status'] !== $pendaftaranAnggota->status) {
                $data['diproses_oleh'] = $request->user()->id;
                $data['tanggal_diproses'] = now();
            }

            $pendaftaranAnggota->update($data);
        });

        return redirect()
            ->route('pendaftaran-anggota.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Data pendaftaran berhasil diperbarui.',
                ],
            ]);
    }

    public function destroy(PendaftaranAnggota $pendaftaranAnggota): RedirectResponse
    {
        DB::transaction(function () use ($pendaftaranAnggota) {
            $this->deleteFileIfExists($pendaftaranAnggota->foto);
            $this->deleteFileIfExists($pendaftaranAnggota->file_cv);
            $pendaftaranAnggota->delete();
        });

        return redirect()
            ->route('pendaftaran-anggota.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Data pendaftaran berhasil dihapus.',
                ],
            ]);
    }

    public function terima(Request $request, PendaftaranAnggota $pendaftaranAnggota): RedirectResponse
    {
        $data = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        $pendaftaranAnggota->update([
            'status' => 'diterima',
            'catatan_admin' => $data['catatan_admin'] ?? null,
            'diproses_oleh' => $request->user()->id,
            'tanggal_diproses' => now(),
        ]);

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Pendaftar berhasil diterima.',
            ],
        ]);
    }

    public function tolak(Request $request, PendaftaranAnggota $pendaftaranAnggota): RedirectResponse
    {
        $data = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        $pendaftaranAnggota->update([
            'status' => 'ditolak',
            'catatan_admin' => $data['catatan_admin'] ?? null,
            'diproses_oleh' => $request->user()->id,
            'tanggal_diproses' => now(),
        ]);

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Pendaftar telah ditolak.',
            ],
        ]);
    }

    private function storeUploadedFile($file, string $path): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($path, $filename, 'public');
    }

    private function deleteFileIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
