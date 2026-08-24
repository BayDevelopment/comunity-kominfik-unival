<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CertificateTemplat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certificate_templates';

    protected $fillable = [
        'name',
        'slug',
        'background_image',
        'orientation',
        'width',
        'height',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'width'     => 'integer',
        'height'    => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (CertificateTemplat $template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name) . '-' . Str::lower(Str::random(5));
            }
        });
    }

    /* ================= RELATIONS ================= */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'certificate_template_id');
    }

    /* ================= SCOPES ================= */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /* ================= ACCESSORS ================= */

    public function getBackgroundUrlAttribute(): ?string
    {
        return $this->background_image
            ? asset('storage/' . $this->background_image)
            : null;
    }
}