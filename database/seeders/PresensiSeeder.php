<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use Illuminate\Database\Seeder;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anggotas = Anggota::all();
        $kegiatans = Kegiatan::all();

        foreach ($kegiatans as $kegiatan) {
            foreach ($anggotas as $anggota) {
                Presensi::create([
                    'kegiatan_id' => $kegiatan->id,
                    'anggota_id' => $anggota->id,
                    'waktu_hadir' => now(),
                    'status_kehadiran' => fake()->randomElement(['hadir', 'izin', 'alfa']),
                ]);
            }
        }
    }
}
