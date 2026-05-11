<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Presensi>
 */
class PresensiFactory extends Factory
{
    protected $model = Presensi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kegiatan_id' => Kegiatan::factory(),
            'anggota_id' => Anggota::factory(),
            'status_kehadiran' => fake()->randomElement(['hadir', 'izin', 'alfa']),
            'waktu_hadir' => now(),
        ];
    }

    /**
     * Indicate attendance status as present.
     */
    public function hadir(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_kehadiran' => 'hadir',
            'waktu_hadir' => now(),
        ]);
    }
}
