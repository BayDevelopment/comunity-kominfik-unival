<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodePendaftaran extends Model
{
    use HasFactory;

    /**
     * Nama tabel (opsional, Laravel otomatis menebak 'periode_pendaftarans'
     * dari nama kelas, jadi ini wajib karena nama tabel sebenarnya
     * 'periode_pendaftaran' tanpa 's').
     */
    protected $table = 'periode_pendaftaran';

    /**
     * Kolom yang boleh diisi lewat mass assignment.
     */
    protected $fillable = [
        'jenis',
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    /**
     * Casting tipe data.
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Tabel ini tidak punya kolom updated_at,
     * jadi timestamps otomatis Laravel dinonaktifkan.
     */
    public $timestamps = false;
}
