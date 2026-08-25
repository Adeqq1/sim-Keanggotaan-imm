<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        $currentYear = now()->year;
        $legacyActivities = DB::table('kegiatan')->pluck('id');

        foreach ($legacyActivities as $kegiatanId) {
            $years = DB::table('presensi')
                ->join('anggota', 'anggota.id', '=', 'presensi.anggota_id')
                ->where('presensi.kegiatan_id', $kegiatanId)
                ->whereNotNull('anggota.tahun_daftar')
                ->distinct()
                ->pluck('anggota.tahun_daftar')
                ->all();

            $years = $years ?: range(2016, $currentYear);
            DB::table('kegiatan_tahun_angkatan')->insert(array_map(
                fn (int $year): array => [
                    'kegiatan_id' => $kegiatanId,
                    'tahun_daftar' => $year,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                array_unique(array_map('intval', $years)),
            ));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_tahun_angkatan');
    }
};
