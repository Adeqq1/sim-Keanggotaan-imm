<?php

use App\Models\Anggota;
use App\Models\User;
use Database\Seeders\AnggotaSeeder;

test('standalone anggota seeder creates one member with a valid deterministic NIA', function () {
    User::factory()->kader()->create(['email' => 'kader@example.com']);

    $this->seed(AnggotaSeeder::class);
    $this->seed(AnggotaSeeder::class);

    $anggota = Anggota::sole();

    expect($anggota->nia)->toBe('24260001')
        ->and($anggota->nia)->toMatch('/^\d{8}$/');
});
