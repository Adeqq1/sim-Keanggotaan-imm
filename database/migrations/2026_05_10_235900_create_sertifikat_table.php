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
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->string('nomor_sertifikat')->unique();
            $table->string('file_sertifikat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
