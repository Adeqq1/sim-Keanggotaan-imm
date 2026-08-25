<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('kegiatan')->orderBy('id')->chunkById(500, function ($kegiatans): void {
            foreach ($kegiatans as $kegiatan) {
                $sesiId = DB::table('sesi_kegiatan')->insertGetId([
                    'kegiatan_id' => $kegiatan->id,
                    'urutan' => 1,
                    'nama_sesi' => 'Sesi 1 (Data Lama)',
                    'mulai_pada' => $kegiatan->tanggal_waktu,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('presensi')->where('kegiatan_id', $kegiatan->id)->update([
                    'sesi_kegiatan_id' => $sesiId,
                    'status_verifikasi' => 'pending',
                    'pemeriksa_id' => null,
                    'diperiksa_pada' => null,
                ]);

            }
        });
    }

    public function down(): void {}
};
