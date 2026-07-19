<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranAnggota extends Model
{
    protected $fillable = [
        'nama',
        'nim_nis',
        'asal_instansi',
        'jenjang',
        'jurusan_prodi',
        'angkatan',
        'email',
        'no_telepon',
        'alamat',
        'alasan_bergabung',
        'file_cv',
        'foto',
        'status',
        'catatan_admin',
        'diproses_oleh',
        'tanggal_diproses',
    ];

    protected $casts = [
        'tanggal_diproses' => 'datetime',
    ];

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
