<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;

test('admin member index uses a twelve-card responsive grid', function () {
    $admin = User::factory()->admin()->create();
    foreach (range(1, 13) as $number) {
        Anggota::factory()->create([
            'nama_lengkap' => 'Anggota Grid '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
            'created_at' => now()->addSeconds($number),
        ]);
    }

    $firstPage = $this->actingAs($admin)->get(route('admin.anggota.index'));

    $firstPage->assertSuccessful()
        ->assertSee('row g-3 index-card-grid', false)
        ->assertSee('col-12 col-sm-6', false)
        ->assertDontSee('col-md-4', false)
        ->assertSee('Anggota Grid 13')
        ->assertDontSee('Anggota Grid 01');

    $this->actingAs($admin)
        ->get(route('admin.anggota.index', ['page' => 2]))
        ->assertSuccessful()
        ->assertSee('Anggota Grid 01');
});

test('activity index uses twelve cards per page', function () {
    $instruktur = User::factory()->instruktur()->create();

    foreach (range(1, 13) as $number) {
        Kegiatan::factory()->create([
            'nama_kegiatan' => 'Kegiatan Grid '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
            'created_at' => now()->addSeconds($number),
        ]);
    }

    $this->actingAs($instruktur)
        ->get(route('admin.kegiatan.index'))
        ->assertSuccessful()
        ->assertSee('Kegiatan Grid 13')
        ->assertDontSee('Kegiatan Grid 01');

    $this->actingAs($instruktur)
        ->get(route('admin.kegiatan.index', ['page' => 2]))
        ->assertSuccessful()
        ->assertSee('Kegiatan Grid 01');
});

test('kader history paginates records while keeping full attendance statistics', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);
    $kegiatans = Kegiatan::factory()->count(7)->create();

    foreach ($kegiatans as $index => $kegiatan) {
        Presensi::factory()->create([
            'anggota_id' => $anggota->id,
            'kegiatan_id' => $kegiatan->id,
            'status_kehadiran' => $index < 6 ? 'hadir' : 'izin',
        ]);
    }

    $response = $this->actingAs($user)->get(route('kader.riwayat.index'));

    $response->assertSuccessful()
        ->assertSee('row g-3 index-card-grid', false)
        ->assertSee('>6<', false)
        ->assertSee('>1<', false)
        ->assertSee('page=2');
});
