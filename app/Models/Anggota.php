<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'foto',
        'email',
        'no_telepon',
        'jabatan',
        'divisi',
        'alamat',
        'tanggal_bergabung',
        'status',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
    ];
}
