<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateSignature extends Model
{
use HasFactory;

    protected $fillable = [
        'certificate_template_id',
        'signer_name',
        'signer_title',
        'signature_image',
        'position_x',
        'position_y',
        'order',
    ];

    protected $casts = [
        'position_x' => 'integer',
        'position_y' => 'integer',
        'order'      => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplat::class, 'certificate_template_id');
    }

    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature_image
            ? asset('storage/' . $this->signature_image)
            : null;
    }
}
