<?php

namespace App\Observers;

use App\Events\PendaftaranAnggotaUpdated;
use App\Models\PendaftaranAnggota;

class PendaftaranAnggotaObserver
{
    public function created(PendaftaranAnggota $pendaftaran): void
    {
        broadcast(new PendaftaranAnggotaUpdated($this->toPayload($pendaftaran), 'created'));
    }

    public function updated(PendaftaranAnggota $pendaftaran): void
    {
        broadcast(new PendaftaranAnggotaUpdated($this->toPayload($pendaftaran), 'updated'));
    }

    public function deleted(PendaftaranAnggota $pendaftaran): void
    {
        broadcast(new PendaftaranAnggotaUpdated($this->toPayload($pendaftaran), 'deleted'));
    }

    private function toPayload(PendaftaranAnggota $pendaftaran): array
    {
        return [
            'id' => $pendaftaran->id,
            'nama' => $pendaftaran->nama,
            'nim_nis' => $pendaftaran->nim_nis,
            'asal_instansi' => $pendaftaran->asal_instansi,
            'jenjang' => $pendaftaran->jenjang,
            'jurusan_prodi' => $pendaftaran->jurusan_prodi,
            'angkatan' => $pendaftaran->angkatan,
            'email' => $pendaftaran->email,
            'no_telepon' => $pendaftaran->no_telepon,
            'status' => $pendaftaran->status,
            'catatan_admin' => $pendaftaran->catatan_admin,
            'diproses_oleh' => $pendaftaran->diproses_oleh,
            'tanggal_diproses' => $pendaftaran->tanggal_diproses,
            'created_at' => $pendaftaran->created_at,
            'updated_at' => $pendaftaran->updated_at,
        ];
    }
}
