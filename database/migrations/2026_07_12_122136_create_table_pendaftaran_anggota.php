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
        Schema::create('pendaftaran_anggotas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nim_nis');
            $table->string('asal_instansi');           // nama kampus/sekolah
            $table->enum('jenjang', ['mahasiswa', 'sma', 'smk']);
            $table->string('jurusan_prodi')->nullable();
            $table->string('angkatan')->nullable();
            $table->string('email')->unique();
            $table->string('no_telepon');
            $table->text('alamat')->nullable();
            $table->text('alasan_bergabung')->nullable();
            $table->string('file_cv')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('diproses_oleh')->nullable()
                ->constrained('users')->nullOnDelete();  // sesuaikan nama tabel admin/user kamu
            $table->timestamp('tanggal_diproses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_pendaftaran_anggota');
    }
};
