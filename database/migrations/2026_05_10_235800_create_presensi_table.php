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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->dateTime('waktu_hadir')->nullable();
            $table->enum('status_kehadiran', ['hadir', 'izin', 'alfa'])->default('alfa');
            $table->timestamps();

            $table->unique(['kegiatan_id', 'anggota_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
