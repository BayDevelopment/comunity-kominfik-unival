<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Relasi Table
            $table->foreignId('certificate_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Detail Sertifikat & Penerima
            $table->string('certificate_number')->unique(); 
            $table->string('recipient_name');
            $table->string('recipient_email')->nullable();
            
            // Informasi Acara / Kursus & Keterangan
            $table->string('event_name')->nullable();
            $table->string('course_name')->nullable();
            $table->text('description')->nullable(); // Contoh: "Telah menyelesaikan pelatihan 40 jam..."

            // Informasi Penandatangan
            $table->string('signatory_name')->nullable();
            $table->string('signatory_role')->nullable(); // Contoh: "CEO", "Lead Instructor"
            $table->string('signatory_signature_path')->nullable(); // Path gambar tanda tangan

            // Masa Berlaku & Status
            $table->date('issued_at');
            $table->date('expired_at')->nullable();
            $table->enum('status', ['draft', 'published', 'revoked'])->default('draft');

            // Audit Pencabutan Sertifikat (jika status = revoked)
            $table->text('revoke_reason')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();

            // File & Verifikasi Publik
            $table->string('file_path')->nullable(); // Lokasi simpan PDF/PNG sertifikat
            $table->string('verification_code', 64)->unique(); // Untuk URL verifikasi / QR Code

            // Fleksibilitas Data Tambahan
            $table->json('metadata')->nullable();

            // Tracking Download
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexing Optimasi Query
            $table->index('recipient_email');
            $table->index(['status', 'issued_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};