<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kerjasama extends Model
{
    protected $table = 'tb_kerjasama';

    protected $fillable = [
        'jenis_instansi',
        'nama_instansi',
        'alamat',
        'nama_pic',
        'jabatan_pic',
        'email_pic',
        'no_hp_pic',
        'jenis_kerjasama',
        'deskripsi_kerjasama',
        'file_proposal',
        'file_mou',
        'status',
        'catatan_admin',
        'diproses_oleh',
        'tanggal_pengajuan',
        'tanggal_diproses',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_diproses' => 'datetime',
    ];

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
