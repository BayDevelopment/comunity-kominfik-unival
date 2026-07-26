<?php

namespace App\Observers;

use App\Events\KerjasamaUpdated;
use App\Models\Kerjasama;

class KerjasamaObserver
{
    public function created(Kerjasama $kerjasama): void
    {
        broadcast(new KerjasamaUpdated($this->toPayload($kerjasama), 'created'));
    }

    public function updated(Kerjasama $kerjasama): void
    {
        broadcast(new KerjasamaUpdated($this->toPayload($kerjasama), 'updated'));
    }

    public function deleted(Kerjasama $kerjasama): void
    {
        broadcast(new KerjasamaUpdated($this->toPayload($kerjasama), 'deleted'));
    }

    private function toPayload(Kerjasama $kerjasama): array
    {
        return [
            'id' => $kerjasama->id,
            'jenis_instansi' => $kerjasama->jenis_instansi,
            'nama_instansi' => $kerjasama->nama_instansi,
            'nama_pic' => $kerjasama->nama_pic,
            'jabatan_pic' => $kerjasama->jabatan_pic,
            'email_pic' => $kerjasama->email_pic,
            'no_hp_pic' => $kerjasama->no_hp_pic,
            'jenis_kerjasama' => $kerjasama->jenis_kerjasama,
            'status' => $kerjasama->status,
            'catatan_admin' => $kerjasama->catatan_admin,
            'diproses_oleh' => $kerjasama->diproses_oleh,
            'tanggal_pengajuan' => $kerjasama->tanggal_pengajuan,
            'tanggal_diproses' => $kerjasama->tanggal_diproses,
            'created_at' => $kerjasama->created_at,
            'updated_at' => $kerjasama->updated_at,
        ];
    }
}
