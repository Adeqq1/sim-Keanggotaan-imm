<?php

namespace Database\Factories;

use App\Models\Kegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kegiatan>
 */
class KegiatanFactory extends Factory
{
    protected $model = Kegiatan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kegiatan' => fake()->sentence(3),
            'deskripsi' => fake()->paragraph(),
            'tanggal_waktu' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'lokasi' => fake()->address(),
        ];
    }

    /**
     * Indicate that the kegiatan is in the past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'tanggal_waktu' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
