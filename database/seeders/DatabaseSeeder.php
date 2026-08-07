<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create();
        $kaders = User::factory()->count(5)->kader()->create();
        User::factory()->count(2)->instruktur()->create();

        $anggotas = $kaders->map(fn (User $kader) => Anggota::factory()->create([
            'user_id' => $kader->id,
            'nama_lengkap' => $kader->name,
        ]));

        $kegiatans = Kegiatan::factory()->past()->count(3)->create();

        Pendaftaran::factory()->count(3)->create();
        Pendaftaran::factory()->approved()->create([
            'user_id' => User::factory()->kader(),
        ]);
        Pendaftaran::factory()->instruktur()->rejected()->create();

        foreach ($kegiatans as $kegiatan) {
            foreach ($anggotas as $anggota) {
                Presensi::factory()->create([
                    'kegiatan_id' => $kegiatan->id,
                    'anggota_id' => $anggota->id,
                ]);
            }
        }

        foreach ($anggotas as $anggota) {
            Sertifikat::factory()->create([
                'kegiatan_id' => $kegiatans->random()->id,
                'anggota_id' => $anggota->id,
            ]);

            Arsip::factory()->create([
                'anggota_id' => $anggota->id,
            ]);
        }
    }
}
