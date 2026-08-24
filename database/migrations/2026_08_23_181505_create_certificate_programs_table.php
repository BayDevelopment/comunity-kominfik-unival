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
        Schema::create('certificate_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // "Coding Camp 2026", "Workshop Laravel"
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('year')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_programs');
    }
};
