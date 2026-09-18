<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->foreignId('sesi_kegiatan_id')->nullable()->after('kegiatan_id')->constrained('sesi_kegiatan');
            $table->string('status_verifikasi', 32)->default('pending')->after('status_kehadiran');
            $table->foreignId('pemeriksa_id')->nullable()->after('status_verifikasi')->constrained('users')->nullOnDelete();
            $table->dateTime('diperiksa_pada')->nullable()->after('pemeriksa_id');
        });
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropForeign(['sesi_kegiatan_id']);
            $table->dropForeign(['pemeriksa_id']);
            $table->dropColumn(['sesi_kegiatan_id', 'status_verifikasi', 'pemeriksa_id', 'diperiksa_pada']);
        });
    }
};
