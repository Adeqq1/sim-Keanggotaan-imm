<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('kegiatan')
            ->where(function ($query): void {
                $query->where('jenis_pelaksanaan', 'belum_ditetapkan')
                    ->orWhereNull('minimum_sesi_terverifikasi');
            })
            ->update([
                'jenis_pelaksanaan' => 'satu_sesi',
                'minimum_sesi_terverifikasi' => 1,
                'updated_at' => now(),
            ]);

        DB::table('presensi')
            ->where('status_kehadiran', 'hadir')
            ->where('status_verifikasi', 'pending')
            ->whereNull('diperiksa_pada')
            ->update(['status_verifikasi' => 'legacy']);
    }

    public function down(): void
    {
        DB::table('presensi')
            ->where('status_verifikasi', 'legacy')
            ->update(['status_verifikasi' => 'pending']);
    }
};
