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
        Schema::create('certificate_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_template_id')->constrained()->cascadeOnDelete();
            $table->string('signer_name');
            $table->string('signer_title')->nullable(); // contoh: "Ketua Panitia"
            $table->string('signature_image')->nullable(); // path gambar tanda tangan
            $table->unsignedInteger('position_x')->default(0);
            $table->unsignedInteger('position_y')->default(0);
            $table->unsignedInteger('order')->default(0); // urutan tampil kalau lebih dari 1 penandatangan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_signatures');
    }
};
