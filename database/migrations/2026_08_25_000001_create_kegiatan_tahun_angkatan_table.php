<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan_tahun_angkatan', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun_daftar');
            $table->timestamps();
            $table->unique(['kegiatan_id', 'tahun_daftar']);
            $table->index('tahun_daftar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_tahun_angkatan');
    }
};
