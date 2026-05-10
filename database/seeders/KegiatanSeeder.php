<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use Illuminate\Database\Seeder;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kegiatan::create([
            'nama_kegiatan' => 'Darul Arqam Dasar (DAD)',
            'deskripsi' => 'Perkaderan tingkat dasar IMM.',
            'tanggal_waktu' => now()->addDays(7),
            'lokasi' => 'Aula Universitas',
        ]);

        Kegiatan::create([
            'nama_kegiatan' => 'Kajian Rutin Mingguan',
            'deskripsi' => 'Kajian keislaman dan keorganisasian.',
            'tanggal_waktu' => now()->subDays(2),
            'lokasi' => 'Masjid Kampus',
        ]);
    }
}
