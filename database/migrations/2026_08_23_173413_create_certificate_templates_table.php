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
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('background_image')->nullable(); // Path gambar background di storage
            
            // Pengaturan Dimensi Dokumen
            $table->enum('orientation', ['landscape', 'portrait'])->default('landscape');
            $table->unsignedSmallInteger('width')->default(1122);  // A4 Landscape @96dpi (px)
            $table->unsignedSmallInteger('height')->default(793);

            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};