<?php

namespace Database\Factories;

use App\Models\Kegiatan;
use App\Models\MateriKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MateriKegiatan>
 */
class MateriKegiatanFactory extends Factory
{
    protected $model = MateriKegiatan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kegiatan_id' => Kegiatan::factory(),
            'judul' => fake()->randomElement([
                'Modul Dasar Keorganisasian',
                'Panduan Administrasi Komisariat',
                'Bahan Kajian Keislaman',
            ]),
            'deskripsi' => fake('id_ID')->paragraph(),
            'file_materi' => 'materi_kegiatan/contoh.pdf',
        ];
    }
}
