<?php

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Admin Member Search', function () {
    test('admin dapat mencari anggota berdasarkan nama lengkap', function () {
        $admin = User::factory()->admin()->create();
        Anggota::factory()->create([
            'nama_lengkap' => 'Ahmad Dahlan',
            'nia' => '24260001',
        ]);
        Anggota::factory()->create([
            'nama_lengkap' => 'Siti Walidah',
            'nia' => '24260002',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Ahmad']));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Dahlan');
        $response->assertDontSee('Siti Walidah');
    });

    test('admin dapat mencari anggota berdasarkan NIA', function () {
        $admin = User::factory()->admin()->create();
        Anggota::factory()->create([
            'nama_lengkap' => 'Ahmad Dahlan',
            'nia' => '24260001',
        ]);
        Anggota::factory()->create([
            'nama_lengkap' => 'Siti Walidah',
            'nia' => '24260002',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => '24260002']));

        $response->assertStatus(200);
        $response->assertSee('Siti Walidah');
        $response->assertDontSee('Ahmad Dahlan');
    });

    test('menampilkan empty state jika hasil pencarian tidak ditemukan', function () {
        $admin = User::factory()->admin()->create();
        Anggota::factory()->create([
            'nama_lengkap' => 'Ahmad Dahlan',
            'nia' => '24260001',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Walidah']));

        $response->assertStatus(200);
        $response->assertSee('Anggota dengan kata kunci "Walidah" tidak ditemukan.', false);
        $response->assertSee('Bersihkan Pencarian');
    });

    test('link pagination tetap membawa parameter pencarian', function () {
        $admin = User::factory()->admin()->create();

        // Buat 15 anggota dengan nama berawalan "Ahmad" agar terjadi pagination (limit 10)
        Anggota::factory()->count(15)->create([
            'nama_lengkap' => 'Ahmad '.fake()->unique()->name(),
        ]);

        // Buat 1 anggota lain
        Anggota::factory()->create([
            'nama_lengkap' => 'Siti Walidah',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Ahmad']));

        $response->assertStatus(200);
        $response->assertSee('page=2');
        $response->assertSee('search=Ahmad');
    });

    test('aksi detail dapat diakses dari hasil pencarian', function () {
        $admin = User::factory()->admin()->create();
        $anggota = Anggota::factory()->create([
            'nama_lengkap' => 'Ahmad Dahlan',
            'nia' => '24260001',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Ahmad']));

        $response->assertStatus(200);
        $response->assertSee(route('admin.anggota.show', $anggota->id));

        $detailResponse = $this->actingAs($admin)
            ->get(route('admin.anggota.show', $anggota->id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Ahmad Dahlan');
    });
});
