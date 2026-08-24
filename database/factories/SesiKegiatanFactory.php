<?php

namespace Database\Factories;

use App\Models\Kegiatan;
use App\Models\SesiKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SesiKegiatan> */
class SesiKegiatanFactory extends Factory
{
    protected $model = SesiKegiatan::class;

    public function definition(): array
    {
        $mulaiPada = fake()->dateTimeBetween('-30 days', '+30 days');

        return [
            'kegiatan_id' => Kegiatan::factory()->withoutDefaultSession(),
            'urutan' => 1,
            'nama_sesi' => 'Sesi 1',
            'mulai_pada' => $mulaiPada,
        ];
    }
}
