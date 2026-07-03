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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('gambar')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('klien')->nullable();
            $table->string('pic')->nullable();
            $table->string('teknologi')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'ditunda', 'dibatalkan'])->default('aktif');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->date('mulai')->nullable();
            $table->date('selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
