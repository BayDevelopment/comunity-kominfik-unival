<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('certificate_signatures');
    }

    public function down(): void
    {
        Schema::create('certificate_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_template_id')->constrained('certificate_templates')->cascadeOnDelete();
            $table->string('signer_name');
            $table->string('signer_title')->nullable();
            $table->string('signature_image')->nullable();
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
};
