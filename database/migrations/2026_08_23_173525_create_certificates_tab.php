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
            $table->foreignId('certificate_template_id')->constrained()->cascadeOnDelete();

            // relasi opsional ke user (kalau penerima adalah user terdaftar di sistem)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('certificate_number')->unique(); // contoh: CERT/2026/08/0001
            $table->string('recipient_name');
            $table->string('recipient_email')->nullable();
            $table->string('event_name')->nullable();
            $table->string('course_name')->nullable();

            $table->date('issued_at');
            $table->date('expired_at')->nullable();

            $table->string('file_path')->nullable();   // hasil generate PDF/PNG
            $table->string('verification_code')->unique(); // dipakai di URL publik /verify/{code}

            $table->enum('status', ['draft', 'published', 'revoked'])->default('draft');
            $table->string('signed_by')->nullable();

            $table->json('metadata')->nullable(); // skor, ranking, data tambahan bebas

            $table->timestamps();
            $table->softDeletes();

            $table->index(['recipient_email']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
