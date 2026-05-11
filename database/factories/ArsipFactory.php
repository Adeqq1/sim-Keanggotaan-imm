<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Arsip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Arsip>
 */
class ArsipFactory extends Factory
{
    protected $model = Arsip::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anggota_id' => Anggota::factory(),
            'nomor_dokumen' => fake()->unique()->numerify('DOC-####'),
            'judul_dokumen' => fake()->sentence(3),
            'kategori_arsip' => fake()->randomElement(['surat', 'laporan', 'foto', 'lainnya']),
            'file_arsip' => 'arsip/dummy.pdf',
            'tanggal_unggah' => now()->toDateString(),
        ];
    }
}
