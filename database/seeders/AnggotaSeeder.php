<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kader = User::where('role', 'kader')->first();

        if ($kader) {
            Anggota::create([
                'user_id' => $kader->id,
                'nia' => '1234567890',
                'nama_lengkap' => $kader->name,
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '2000-01-01',
                'alamat' => 'Jl. Malioboro No. 1, Yogyakarta',
                'no_telp' => '081234567890',
                'status_aktif' => true,
            ]);
        }
    }
}
