<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Anggota>
 */
class AnggotaFactory extends Factory
{
    protected $model = Anggota::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('id_ID');

        return [
            'user_id' => User::factory()->kader(),
            'nia' => '24'.fake()->numerify('##').fake()->unique()->numerify('####'),
            'nama_lengkap' => $faker->name(),
            'tempat_lahir' => $faker->city(),
            'tanggal_lahir' => fake()->date('Y-m-d', '2005-01-01'),
            'alamat' => $faker->address(),
            'no_telp' => $faker->phoneNumber(),
            'status_aktif' => true,
            'tahun_daftar' => now()->year,
        ];
    }

    /**
     * Indicate that the anggota is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_aktif' => false,
        ]);
    }

    /**
     * Indicate that the anggota belum memiliki NIA.
     */
    public function tanpaNia(): static
    {
        return $this->state(fn (array $attributes) => [
            'nia' => null,
        ]);
    }
}
