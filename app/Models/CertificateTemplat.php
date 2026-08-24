<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CertificateTemplat extends Model
{
    protected $table = 'certificate_templates';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'background_image',
        'orientation',
        'width',
        'height',
        // 'elements',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        // 'elements'  => 'array',
        'is_active' => 'boolean',
        'width'     => 'integer',
        'height'    => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (CertificateTemplat $template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name) . '-' . Str::random(5);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(CertificateSignature::class)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getBackgroundUrlAttribute(): ?string
    {
        return $this->background_image
            ? asset('storage/' . $this->background_image)
            : null;
    }
}