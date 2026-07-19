<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'gambar',
        'deskripsi',
        'klien',
        'pic',
        'teknologi',
        'status',
        'progress',
        'mulai',
        'selesai',
    ];

    protected $casts = [
        'mulai' => 'date',
        'selesai' => 'date',
        'progress' => 'integer',
    ];

    protected $appends = ['gambar_url'];

    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}
