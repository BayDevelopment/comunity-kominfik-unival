<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "certificates";

    protected $fillable = [
        'uuid',
        'certificate_template_id',
        'user_id',
        'certificate_number',
        'recipient_name',
        'recipient_email',
        'event_name',
        'course_name',
        'description',
        'signatory_name',
        'signatory_role',
        'signatory_signature_path',
        'issued_at',
        'expired_at',
        'status',
        'revoke_reason',
        'revoked_at',
        'revoked_by',
        'file_path',
        'verification_code',
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
        'download_count'     => 'integer',
    ];

    /**
     * Otomatis sertakan accessor ini saat dikonversi ke JSON (Inertia Props)
     */
    protected $appends = [
        'signatory_signature_url',
        'file_url',
        'verification_url',
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
     * Format Nomor: CERT/2026/08/0001
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

    /* ================= RELATIONS ================= */

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplat::class, 'certificate_template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    /* ================= SCOPES ================= */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /* ================= ACCESSORS ================= */

    public function isExpired(): bool
    {
        return $this->expired_at !== null && $this->expired_at->isPast();
    }

    // SESUDAH — arahkan ke halaman publik cek sertifikat yang benar
    public function getVerificationUrlAttribute(): string
    {
        return route('sertifikat.index') . '?code=' . $this->verification_code;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path
            ? Storage::url($this->file_path)
            : null;
    }

    public function getSignatorySignatureUrlAttribute(): ?string
    {
        return $this->signatory_signature_path
            ? Storage::url($this->signatory_signature_path)
            : null;
    }

    /* ================= HELPER METHODS ================= */

    /**
     * Cabut sertifikat dengan alasan & jejak siapa yang mencabut.
     */
    public function revoke(string $reason, ?int $revokedByUserId = null): void
    {
        $this->update([
            'status'        => 'revoked',
            'revoke_reason' => $reason,
            'revoked_at'    => now(),
            'revoked_by'    => $revokedByUserId ?? Auth::id(),
        ]);
    }

    /**
     * Catat setiap kali sertifikat diunduh.
     */
    public function recordDownload(): void
    {
        $this->timestamps = false;
        $this->increment('download_count');
        $this->last_downloaded_at = now();
        $this->save();
        $this->timestamps = true;
    }
}
