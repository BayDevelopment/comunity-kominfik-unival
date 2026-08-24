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
            $table->string('background_image')->nullable(); // path di storage
            $table->enum('orientation', ['landscape', 'portrait'])->default('landscape');
            $table->unsignedInteger('width')->default(1122);  // px, default A4 landscape @96dpi kira-kira
            $table->unsignedInteger('height')->default(793);
            $table->json('elements')->nullable(); // posisi & style tiap elemen teks/QR di atas canvas
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
