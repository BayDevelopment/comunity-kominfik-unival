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
        Schema::table('certificates', function (Blueprint $table) {
            // relasi ke program/kategori kegiatan
            $table->foreignId('certificate_program_id')
                ->nullable()
                ->after('certificate_template_id')
                ->constrained()
                ->nullOnDelete();

            // keterangan sertifikat, contoh: "telah menyelesaikan pelatihan 40 jam dengan predikat Baik"
            $table->text('description')->nullable()->after('course_name');

            // alasan & jejak pencabutan sertifikat
            $table->text('revoke_reason')->nullable()->after('status');
            $table->timestamp('revoked_at')->nullable()->after('revoke_reason');
            $table->foreignId('revoked_by')->nullable()->after('revoked_at')->constrained('users')->nullOnDelete();

            // tracking download
            $table->unsignedInteger('download_count')->default(0)->after('metadata');
            $table->timestamp('last_downloaded_at')->nullable()->after('download_count');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('certificate_program_id');
            $table->dropConstrainedForeignId('revoked_by');
            $table->dropColumn([
                'description',
                'revoke_reason',
                'revoked_at',
                'download_count',
                'last_downloaded_at',
            ]);
        });
    }
};
