<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->unsignedSmallInteger('urutan');
            $table->string('nama_sesi');
            $table->dateTime('mulai_pada');
            $table->timestamps();
            $table->unique(['kegiatan_id', 'urutan']);
            $table->unique(['kegiatan_id', 'nama_sesi', 'mulai_pada']);
            $table->index(['kegiatan_id', 'mulai_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_kegiatan');
    }
};
