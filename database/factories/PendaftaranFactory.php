<?php

namespace Database\Factories;

use App\Models\Pendaftaran;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Pendaftaran>
 */
class PendaftaranFactory extends Factory
{
    protected $model = Pendaftaran::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'kader',
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->date('Y-m-d', '2005-01-01'),
            'no_telp' => fake()->phoneNumber(),
            'alamat' => fake()->address(),
            'tanggal_daftar' => now()->toDateString(),
            'status_validasi' => 'pending',
        ];
    }

    /**
     * Indicate that the pendaftaran is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_validasi' => 'disetujui',
            'catatan_admin' => 'Pendaftaran disetujui.',
        ]);
    }

    /**
     * Indicate that the pendaftaran is for an instruktur account.
     */
    public function instruktur(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'instruktur',
        ]);
    }

    /**
     * Indicate that the pendaftaran predates applicant-selected passwords.
     */
    public function legacyWithoutPassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => null,
        ]);
    }

    /**
     * Indicate that the pendaftaran is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_validasi' => 'ditolak',
            'catatan_admin' => 'Pendaftaran ditolak.',
        ]);
    }
}
