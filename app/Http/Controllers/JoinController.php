<?php

namespace App\Http\Controllers;

use App\Models\PeriodePendaftaran;
use App\Models\PendaftaranAnggota;
use App\Models\Kerjasama;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JoinController extends Controller
{
    public function join(): Response
    {
        // Ambil semua data periode tanpa terkecuali, diurutkan dari yang terbaru
        $periodes = PeriodePendaftaran::orderBy('created_at', 'desc')->get();

        return Inertia::render('Join', [
            'periodes' => $periodes,
        ]);
    }

    /**
     * GET /join/anggota
     * Halaman form pendaftaran anggota.
     */
    public function anggota(): Response
    {
        $periode = PeriodePendaftaran::where('jenis', 'anggota')
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('Anggota', [
            'periode' => $periode,
        ]);
    }

    /**
     * POST /join/anggota
     * Simpan data pendaftaran anggota.
     */
    public function storeAnggota(Request $request): RedirectResponse
    {
        // Honeypot anti-bot: field tersembunyi ("website") yang hanya bisa
        // terisi oleh bot otomatis, bukan manusia (lihat catatan di Vue).
        if ($request->filled('website')) {
            abort(422, 'Permintaan tidak valid.');
        }

        $periode = PeriodePendaftaran::where('jenis', 'anggota')
            ->orderBy('created_at', 'desc')
            ->first();

        // Validasi server-side: pastikan periode benar-benar sedang dibuka
        abort_if(! $periode, 403, 'Periode pendaftaran belum tersedia.');
        abort_if($periode->status !== 'active', 403, 'Pendaftaran sedang ditutup.');

        $today = now()->startOfDay();

        if ($periode->tanggal_mulai && $today->lt($periode->tanggal_mulai)) {
            abort(403, 'Pendaftaran belum dibuka.');
        }

        if ($periode->tanggal_selesai && $today->gt($periode->tanggal_selesai)) {
            abort(403, 'Pendaftaran sudah ditutup.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim_nis' => ['required', 'string', 'max:255'],
            'asal_instansi' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'in:mahasiswa,sma,smk'],
            'jurusan_prodi' => ['nullable', 'string', 'max:255'],
            'angkatan' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:pendaftaran_anggotas,email'],
            'no_telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'alasan_bergabung' => ['nullable', 'string', 'max:2000'],
            'file_cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:1024'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);

        if ($request->hasFile('file_cv')) {
            $validated['file_cv'] = $request->file('file_cv')->store('pendaftaran/cv', 'public');
        }

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('pendaftaran/foto', 'public');
        }

        PendaftaranAnggota::create($validated);

        return redirect()
            ->back()
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Pendaftaran anggota berhasil dikirim. Tim kami akan meninjau data kamu.',
                ],
            ]);
    }


    public function kerjasamaUniversity(): Response
    {
        $periode = PeriodePendaftaran::where('jenis', 'kerjasama')
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('Kerjasama', [
            'periode' => $periode,
        ]);
    }


    public function storeKerjasama(Request $request): RedirectResponse
    {

        if ($request->filled('website')) {
            abort(422, 'Permintaan tidak valid.');
        }

        $periode = PeriodePendaftaran::where('jenis', 'kerjasama')
            ->orderBy('created_at', 'desc')
            ->first();

        abort_if(! $periode, 403, 'Periode pengajuan kerjasama belum tersedia.');
        abort_if($periode->status !== 'active', 403, 'Pengajuan kerjasama sedang ditutup.');

        $today = now()->startOfDay();

        if ($periode->tanggal_mulai && $today->lt($periode->tanggal_mulai)) {
            abort(403, 'Pengajuan kerjasama belum dibuka.');
        }

        if ($periode->tanggal_selesai && $today->gt($periode->tanggal_selesai)) {
            abort(403, 'Pengajuan kerjasama sudah ditutup.');
        }

        $validated = $request->validate([
            'jenis_instansi' => ['required', 'in:kampus,sma,smk,perusahaan,lainnya'],
            'nama_instansi' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'nama_pic' => ['required', 'string', 'max:100'],
            'jabatan_pic' => ['nullable', 'string', 'max:100'],
            'email_pic' => ['required', 'email', 'max:100'],
            'no_hp_pic' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'jenis_kerjasama' => ['nullable', 'string', 'max:150'],
            'deskripsi_kerjasama' => ['nullable', 'string', 'max:2000'],
            'file_proposal' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'file_mou' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        if ($request->hasFile('file_proposal')) {
            $validated['file_proposal'] = $request->file('file_proposal')->store('kerjasama/proposal', 'public');
        }

        if ($request->hasFile('file_mou')) {
            $validated['file_mou'] = $request->file('file_mou')->store('kerjasama/mou', 'public');
        }

        $validated['status'] = 'pending';
        $validated['tanggal_pengajuan'] = now();

        Kerjasama::create($validated);

        return redirect()
            ->back()
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Pengajuan kerjasama berhasil dikirim. Tim kami akan meninjau pengajuan kamu.',
                ],
            ]);
    }
}
