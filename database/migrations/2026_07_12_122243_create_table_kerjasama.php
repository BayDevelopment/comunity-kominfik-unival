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
        Schema::create('tb_kerjasama', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_instansi', ['kampus', 'sma', 'smk', 'perusahaan', 'lainnya']);
            $table->string('nama_instansi', 150);
            $table->text('alamat')->nullable();
            $table->string('nama_pic', 100);
            $table->string('jabatan_pic', 100)->nullable();
            $table->string('email_pic', 100);
            $table->string('no_hp_pic', 20);
            $table->string('jenis_kerjasama', 150)->nullable();
            $table->text('deskripsi_kerjasama')->nullable();
            $table->string('file_proposal')->nullable();
            $table->string('file_mou')->nullable();
            $table->enum('status', ['pending', 'diproses', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('diproses_oleh')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamp('tanggal_diproses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_kerjasama');
    }
};
