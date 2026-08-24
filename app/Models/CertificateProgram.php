<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CertificateProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'year',
        'description',
        'is_active',
    ];

    protected $casts = [
        'year'      => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (CertificateProgram $program) {
            if (empty($program->slug)) {
                $program->slug = Str::slug($program->name . '-' . ($program->year ?? now()->year));
            }
        });
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
