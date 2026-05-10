<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator SIM-IMM',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Buat 1 Kader contoh
        $kader = User::create([
            'name' => 'Kader IMM Contoh',
            'email' => 'kader@example.com',
            'password' => Hash::make('password'),
            'role' => 'kader',
        ]);
    }
}
