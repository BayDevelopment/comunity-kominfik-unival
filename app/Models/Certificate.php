<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'certificate_template_id',
        'certificate_program_id',
        'user_id',
        'certificate_number',
        'recipient_name',
        'recipient_email',
        'event_name',
        'course_name',
        'description',            
        'issued_at',
        'expired_at',
        'file_path',
        'verification_code',
        'status',
        'signed_by',
        'signatory_name',            // Kolom baru
        'signatory_signature_path',  // Kolom baru
        'revoke_reason',          
        'revoked_at',             
        'revoked_by',             
        'metadata',
        'download_count',         
        'last_downloaded_at',     
    ];

    protected $casts = [
        'issued_at'          => 'date',
        'expired_at'         => 'date',
        'revoked_at'         => 'datetime',      
        'last_downloaded_at' => 'datetime',      
        'metadata'           => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Certificate $certificate) {
            $certificate->uuid ??= (string) Str::uuid();
            $certificate->verification_code ??= strtoupper(Str::random(10));
            $certificate->certificate_number ??= static::generateCertificateNumber();
            $certificate->issued_at ??= now();
        });
    }

    /**
     * Contoh: CERT/2026/08/0001
     */
    public static function generateCertificateNumber(): string
    {
        $prefix = 'CERT/' . now()->format('Y/m') . '/';

        $lastNumber = static::withTrashed()
            ->where('certificate_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('certificate_number');

        $nextSequence = 1;

        if ($lastNumber) {
            $lastSequence = (int) Str::afterLast($lastNumber, '/');
            $nextSequence = $lastSequence + 1;
        }

        return $prefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplat::class, 'certificate_template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    public function isExpired(): bool
    {
        return $this->expired_at !== null && $this->expired_at->isPast();
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('certificates.verify', $this->verification_code);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path
            ? asset('storage/' . $this->file_path)
            : null;
    }

    /**
     * Accessor untuk mendapatkan URL lengkap gambar tanda tangan ketua.
     */
    public function getSignatorySignatureUrlAttribute(): ?string
    {
        return $this->signatory_signature_path
            ? asset('storage/' . $this->signatory_signature_path)
            : null;
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(CertificateProgram::class, 'certificate_program_id');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    /**
     * Cabut sertifikat dengan alasan & jejak siapa yang mencabut.
     */
    public function revoke(string $reason, ?int $revokedByUserId = null): void
    {
        $this->update([
            'status'        => 'revoked',
            'revoke_reason' => $reason,
            'revoked_at'    => now(),
            'revoked_by'    => $revokedByUserId,
        ]);
    }

    /**
     * Catat setiap kali sertifikat diunduh.
     */
    public function recordDownload(): void
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }
}