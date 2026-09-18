<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('presensi')->whereNull('sesi_kegiatan_id')->exists()) {
            throw new RuntimeException('Presensi tanpa sesi tidak dapat dimigrasikan.');
        }

        Schema::table('presensi', function (Blueprint $table) {
            $table->index('kegiatan_id', 'presensi_kegiatan_id_index');
            $table->dropUnique('presensi_kegiatan_id_anggota_id_unique');
            $table->unique(['sesi_kegiatan_id', 'anggota_id'], 'presensi_sesi_anggota_unique');
            $table->index(['kegiatan_id', 'anggota_id', 'status_kehadiran', 'status_verifikasi', 'sesi_kegiatan_id'], 'presensi_verified_count_index');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE presensi MODIFY sesi_kegiatan_id BIGINT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        $multiple = DB::table('presensi')
            ->select('kegiatan_id', 'anggota_id')
            ->groupBy('kegiatan_id', 'anggota_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($multiple) {
            throw new RuntimeException('Rollback ditolak karena presensi multi-sesi sudah tersimpan.');
        }

        Schema::table('presensi', function (Blueprint $table) {
            $table->dropUnique('presensi_sesi_anggota_unique');
            $table->dropIndex('presensi_verified_count_index');
            $table->dropIndex('presensi_kegiatan_id_index');
            $table->unique(['kegiatan_id', 'anggota_id']);
        });
    }
};
