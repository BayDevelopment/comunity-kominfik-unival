<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'gambar',
        'kategori',
        'deskripsi',
        'syarat',
        'estimasi_waktu',
        'biaya',
        'status',
    ];

    protected $casts = [
        'biaya' => 'integer',
        'syarat' => 'array',
    ];


    protected $appends = ['gambar_url', 'biaya_formatted'];

    public function getBiayaFormattedAttribute()
    {
        if ($this->biaya) {
            return 'Rp ' . number_format($this->biaya, 0, ',', '.');
        }

        return null;
    }

    public function getGambarUrlAttribute()
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}
