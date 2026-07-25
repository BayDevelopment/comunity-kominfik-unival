<?php

namespace App\Http\Controllers;

use App\Mail\PendaftaranAnggotaDiterima;
use App\Mail\PendaftaranDiterima;
use App\Mail\PendaftaranDitolak;
use App\Models\Anggota;
use App\Models\PendaftaranAnggota;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
            'nama'             => ['required', 'string', 'max:255'],
            'nim_nis'          => ['required', 'string', 'max:50'],
            'asal_instansi'    => ['required', 'string', 'max:255'],
            'jenjang'          => ['required', Rule::in(['mahasiswa', 'sma', 'smk'])],
            'jurusan_prodi'    => ['nullable', 'string', 'max:255'],
            'angkatan'         => ['nullable', 'string', 'max:10'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:pendaftaran_anggotas,email',
                'unique:anggotas,email',
            ],
            'no_telepon' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s]+$/',
                'unique:anggotas,no_telepon',
                'unique:pendaftaran_anggotas,no_telepon', // tambahan: konsisten dg terima()
            ],
            'alamat'            => ['nullable', 'string', 'max:1000'],
            'alasan_bergabung'  => ['nullable', 'string', 'max:2000'],
            'file_cv'           => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:1024'],
            'foto'              => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            'status'            => ['sometimes', Rule::in(['pending', 'diterima', 'ditolak'])],
            'catatan_admin'     => ['nullable', 'string', 'max:2000'],
            // field tambahan khusus jika status langsung "diterima"
            'jabatan'           => ['required_if:status,diterima', 'nullable', 'string', 'max:255'],
            'divisi'            => ['required_if:status,diterima', 'nullable', 'string', 'max:255'],
            'tanggal_bergabung' => ['required_if:status,diterima', 'nullable', 'date'],
        ], [
            'email.unique'       => 'Email ini sudah terdaftar.',
            'no_telepon.unique'  => 'No. telepon ini sudah digunakan oleh anggota lain.',
            'file_cv.mimes'      => 'File CV harus berformat PDF, DOC, atau DOCX.',
            'file_cv.max'        => 'Ukuran file CV maksimal 1MB.',
            'foto.image'         => 'File foto harus berupa gambar.',
            'foto.max'           => 'Ukuran foto maksimal 1MB.',
        ]);

        $status = $data['status'] ?? 'pending';
        $anggota = null;

        $pendaftaran = DB::transaction(function () use ($request, &$data, $status, &$anggota) {
            if ($request->hasFile('foto')) {
                $data['foto'] = $this->storeUploadedFile($request->file('foto'), self::FOTO_PATH);
            }

            if ($request->hasFile('file_cv')) {
                $data['file_cv'] = $this->storeUploadedFile($request->file('file_cv'), self::CV_PATH);
            }

            if ($status !== 'pending') {
                $data['diproses_oleh']    = $request->user()->id;
                $data['tanggal_diproses'] = now();
            }

            // Simpan field khusus Anggota, JANGAN ikut disimpan ke pendaftaran_anggotas
            $jabatan           = $data['jabatan'] ?? null;
            $divisi            = $data['divisi'] ?? null;
            $tanggalBergabung  = $data['tanggal_bergabung'] ?? null;
            unset($data['jabatan'], $data['divisi'], $data['tanggal_bergabung']);

            $pendaftaran = PendaftaranAnggota::create($data);

            // FIX BUG #1: kalau admin set status "diterima" langsung,
            // pastikan record Anggota juga dibuat — sama seperti alur terima()
            if ($status === 'diterima') {
                $anggota = Anggota::create([
                    'nama'              => $pendaftaran->nama,
                    'foto'              => $pendaftaran->foto,
                    'email'             => $pendaftaran->email,
                    'no_telepon'        => $pendaftaran->no_telepon,
                    'jabatan'           => $jabatan,
                    'divisi'            => $divisi,
                    'alamat'            => $pendaftaran->alamat,
                    'tanggal_bergabung' => $tanggalBergabung,
                    'status'            => 'aktif',
                ]);
            }

            return $pendaftaran;
        });

        if ($status === 'diterima' && ! $anggota instanceof Anggota) {
            throw new \RuntimeException('Gagal membuat data anggota saat pendaftaran diterima.');
        }

        try {
            match ($status) {
                'diterima' => Mail::to($pendaftaran->email)->send(new PendaftaranDiterima($pendaftaran, $anggota)),
                'ditolak'  => Mail::to($pendaftaran->email)->send(new PendaftaranDitolak($pendaftaran)),
                default    => Mail::to($pendaftaran->email)->send(new PendaftaranAnggotaDiterima($pendaftaran)),
            };
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('pendaftaran-anggota.index')
            ->with('flash', [
                'toast' => [
                    'type'    => 'success',
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
            'nama'             => ['required', 'string', 'max:255'],
            'nim_nis'          => ['required', 'string', 'max:50'],
            'asal_instansi'    => ['required', 'string', 'max:255'],
            'jenjang'          => ['required', Rule::in(['mahasiswa', 'sma', 'smk'])],
            'jurusan_prodi'    => ['nullable', 'string', 'max:255'],
            'angkatan'         => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('pendaftaran_anggotas', 'email')->ignore($pendaftaranAnggota->id)],
            'no_telepon'       => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'alamat'           => ['nullable', 'string', 'max:1000'],
            'alasan_bergabung' => ['nullable', 'string', 'max:2000'],
            'file_cv'          => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:1024'],
            'foto'             => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            'status'           => ['sometimes', Rule::in(['pending', 'diterima', 'ditolak'])],
            'catatan_admin'    => ['nullable', 'string', 'max:2000'],
            'jabatan'          => ['required_if:status,diterima', 'nullable', 'string', 'max:255'],
            'divisi'           => ['required_if:status,diterima', 'nullable', 'string', 'max:255'],
            'tanggal_bergabung' => ['required_if:status,diterima', 'nullable', 'date'],
        ], [
            'email.unique'    => 'Email ini sudah terdaftar.',
            'file_cv.mimes'   => 'File CV harus berformat PDF, DOC, atau DOCX.',
            'file_cv.max'     => 'Ukuran file CV maksimal 1MB.',
            'foto.image'      => 'File foto harus berupa gambar.',
            'foto.max'        => 'Ukuran foto maksimal 1MB.',
        ]);

        if (!$request->hasFile('foto')) {
            unset($data['foto']);
        }
        if (!$request->hasFile('file_cv')) {
            unset($data['file_cv']);
        }

        $statusLama    = $pendaftaranAnggota->status;
        $statusBaru    = $data['status'] ?? $statusLama;
        $statusBerubah = $statusBaru !== $statusLama;

        // ❌ Guard "harus dari pending" DIHAPUS — status sekarang bisa diubah kapan saja

        $jabatan          = $data['jabatan'] ?? null;
        $divisi           = $data['divisi'] ?? null;
        $tanggalBergabung = $data['tanggal_bergabung'] ?? null;
        unset($data['jabatan'], $data['divisi'], $data['tanggal_bergabung']);

        $anggota = null;

        try {
            DB::transaction(function () use ($request, $pendaftaranAnggota, &$data, $statusBerubah, $statusBaru, $statusLama, $jabatan, $divisi, $tanggalBergabung, &$anggota) {
                if ($request->hasFile('foto')) {
                    $this->deleteFileIfExists($pendaftaranAnggota->foto);
                    $data['foto'] = $this->storeUploadedFile($request->file('foto'), self::FOTO_PATH);
                }

                if ($request->hasFile('file_cv')) {
                    $this->deleteFileIfExists($pendaftaranAnggota->file_cv);
                    $data['file_cv'] = $this->storeUploadedFile($request->file('file_cv'), self::CV_PATH);
                }

                if ($statusBerubah) {
                    $data['diproses_oleh']    = $request->user()->id;
                    $data['tanggal_diproses'] = now();
                }

                $pendaftaranAnggota->update($data);

                // Status berubah JADI "diterima" → buat record Anggota (kalau belum ada)
                if ($statusBerubah && $statusBaru === 'diterima') {
                    $anggota = Anggota::where('email', $pendaftaranAnggota->email)->first();

                    if (! $anggota) {
                        $errors = [];

                        if (Anggota::where('no_telepon', $pendaftaranAnggota->no_telepon)->exists()) {
                            $errors['no_telepon'] = "No. telepon \"{$pendaftaranAnggota->no_telepon}\" sudah dipakai anggota lain.";
                        }
                        if (!empty($errors)) {
                            throw ValidationException::withMessages($errors);
                        }

                        $anggota = Anggota::create([
                            'nama'              => $pendaftaranAnggota->nama,
                            'foto'              => $pendaftaranAnggota->foto,
                            'email'             => $pendaftaranAnggota->email,
                            'no_telepon'        => $pendaftaranAnggota->no_telepon,
                            'jabatan'           => $jabatan,
                            'divisi'            => $divisi,
                            'alamat'            => $pendaftaranAnggota->alamat,
                            'tanggal_bergabung' => $tanggalBergabung,
                            'status'            => 'aktif',
                        ]);
                    }
                }

                // Status BERUBAH DARI "diterima" ke status lain → nonaktifkan record Anggota terkait
                if ($statusBerubah && $statusLama === 'diterima' && $statusBaru !== 'diterima') {
                    Anggota::where('email', $pendaftaranAnggota->email)->update(['status' => 'nonaktif']);
                }
            });
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'email' => 'Email atau No. telepon sudah dipakai oleh anggota lain. Silakan cek kembali.',
                ]);
            }
            throw $e;
        }

        // Kirim email SETIAP KALI status berubah jadi diterima/ditolak
        if ($statusBerubah) {
            try {
                match ($statusBaru) {
                    'diterima' => Mail::to($pendaftaranAnggota->email)->send(new PendaftaranDiterima($pendaftaranAnggota, $anggota)),
                    'ditolak'  => Mail::to($pendaftaranAnggota->email)->send(new PendaftaranDitolak($pendaftaranAnggota)),
                    default    => null,
                };
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('pendaftaran-anggota.index')
            ->with('flash', [
                'toast' => [
                    'type'    => 'success',
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
        $validated = $request->validate([
            'jabatan' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'tanggal_bergabung' => 'required|date',
        ]);

        if ($pendaftaranAnggota->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses sebelumnya.');
        }

        // Cek duplikasi terhadap anggota yang sudah ada
        $errors = [];

        if (Anggota::where('email', $pendaftaranAnggota->email)->exists()) {
            $errors['email'] = "Email \"{$pendaftaranAnggota->email}\" sudah terdaftar sebagai anggota lain.";
        }

        if (Anggota::where('no_telepon', $pendaftaranAnggota->no_telepon)->exists()) {
            $errors['no_telepon'] = "No. telepon \"{$pendaftaranAnggota->no_telepon}\" sudah terdaftar sebagai anggota lain.";
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        // $anggota perlu ditangkap di luar closure supaya bisa dipakai untuk kirim email
        /** @var Anggota|null $anggota */
        $anggota = null;

        try {
            DB::transaction(function () use ($pendaftaranAnggota, $validated, $request, &$anggota) {
                $anggota = Anggota::create([
                    'nama' => $pendaftaranAnggota->nama,
                    'foto' => $pendaftaranAnggota->foto,
                    'email' => $pendaftaranAnggota->email,
                    'no_telepon' => $pendaftaranAnggota->no_telepon,
                    'jabatan' => $validated['jabatan'],
                    'divisi' => $validated['divisi'],
                    'alamat' => $pendaftaranAnggota->alamat,
                    'tanggal_bergabung' => $validated['tanggal_bergabung'],
                    'status' => 'aktif',
                ]);

                $pendaftaranAnggota->update([
                    'status' => 'diterima',
                    'diproses_oleh' => $request->user()->id,
                    'tanggal_diproses' => now(),
                ]);
            });
        } catch (QueryException $e) {
            // Jaga-jaga kalau ada race condition (dua request bersamaan lolos pengecekan di atas)
            if ($e->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'email' => 'Email atau No. telepon sudah dipakai oleh anggota lain. Silakan cek kembali.',
                ]);
            }
            throw $e;
        }

        if (! $anggota instanceof Anggota) {
            throw new \RuntimeException('Gagal membuat anggota.');
        }

        // Kirim email pemberitahuan diterima jadi anggota
        try {
            Mail::to($pendaftaranAnggota->email)->send(new PendaftaranDiterima($pendaftaranAnggota, $anggota));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Pendaftar berhasil diterima.',
            ],
        ]);
    }


    public function tolak(Request $request, PendaftaranAnggota $pendaftaranAnggota): RedirectResponse
    {
        Log::info('TOLAK: method dipanggil', ['id' => $pendaftaranAnggota->id]);

        $data = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        $pendaftaranAnggota->update([
            'status' => 'ditolak',
            'catatan_admin' => $data['catatan_admin'] ?? null,
            'diproses_oleh' => $request->user()->id,
            'tanggal_diproses' => now(),
        ]);

        Log::info('TOLAK: status berhasil diupdate', ['email' => $pendaftaranAnggota->email]);

        try {
            Mail::to($pendaftaranAnggota->email)->send(new PendaftaranDitolak($pendaftaranAnggota));
            Log::info('TOLAK: email berhasil dikirim tanpa exception');
        } catch (\Throwable $e) {
            Log::error('TOLAK: email GAGAL', ['error' => $e->getMessage()]);
        }

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => 'Pendaftar telah ditolak.',
            ],
        ]);
    }


    private function storeUploadedFile(UploadedFile $file, string $path): string
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
