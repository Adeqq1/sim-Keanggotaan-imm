<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\PenilaianKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PenilaianKegiatan> */
class PenilaianKegiatanFactory extends Factory
{
    protected $model = PenilaianKegiatan::class;

    public function definition(): array
    {
        return [
            'kegiatan_id' => Kegiatan::factory(),
            'anggota_id' => Anggota::factory(),
            'nilai' => fake()->randomElement(array_keys(PenilaianKegiatan::NILAI_LABELS)),
        ];
    }
}
