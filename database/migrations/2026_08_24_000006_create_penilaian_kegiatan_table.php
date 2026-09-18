<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->enum('nilai', ['A', 'B', 'C', 'D']);
            $table->timestamps();
            $table->unique(['kegiatan_id', 'anggota_id'], 'penilaian_kegiatan_kegiatan_anggota_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_kegiatan');
    }
};
