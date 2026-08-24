<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\SesiKegiatan;
use App\Models\User;
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
            'sesi_kegiatan_id' => SesiKegiatan::factory(),
            'anggota_id' => Anggota::factory(),
            'status_kehadiran' => fake()->randomElement(['hadir', 'izin', 'alfa']),
            'waktu_hadir' => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Presensi $presensi): void {
            $presensi->kegiatan_id = SesiKegiatan::query()->findOrFail($presensi->sesi_kegiatan_id)->kegiatan_id;
        })->afterCreating(function (Presensi $presensi): void {
            if ($presensi->status_verifikasi === 'terverifikasi' && ! $presensi->pemeriksa_id) {
                $presensi->update(['pemeriksa_id' => User::factory()->instruktur()->create()->id]);
            }
        });
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

    public function terverifikasi(): static
    {
        return $this->hadir()->state(fn (array $attributes) => [
            'status_verifikasi' => 'terverifikasi',
            'diperiksa_pada' => now(),
        ]);
    }
}
