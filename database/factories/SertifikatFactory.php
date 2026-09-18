<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Sertifikat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sertifikat>
 */
class SertifikatFactory extends Factory
{
    protected $model = Sertifikat::class;

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
            'nomor_sertifikat' => 'CERT-'.fake()->unique()->numerify('####'),
            'file_sertifikat' => 'sertifikat/dummy.pdf',
            'tipe_sertifikat' => Sertifikat::SATU_SESI,
            'nilai_snapshot' => null,
        ];
    }

    public function legacy(): static
    {
        return $this->state(['tipe_sertifikat' => null, 'nilai_snapshot' => null]);
    }

    public function p0(): static
    {
        return $this->state(['tipe_sertifikat' => Sertifikat::SATU_SESI, 'nilai_snapshot' => null]);
    }
}
