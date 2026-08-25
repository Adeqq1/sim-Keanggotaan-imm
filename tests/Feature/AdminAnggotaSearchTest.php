<?php

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Admin Member Search', function () {
    test('admin dapat mencari anggota berdasarkan nama lengkap', function () {
        $admin = User::factory()->admin()->create();
        Anggota::factory()->create([
            'nama_lengkap' => 'Anggota Ahmad',
            'nia' => '24260001',
        ]);
        Anggota::factory()->create([
            'nama_lengkap' => 'Siti Walidah',
            'nia' => '24260002',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Anggota Ahmad']));

        $response->assertSuccessful();
        $response->assertSee('Anggota Ahmad');
        $response->assertDontSee('Siti Walidah');
    });

    test('admin dapat mencari anggota berdasarkan NIA', function () {
        $admin = User::factory()->admin()->create();
        Anggota::factory()->create([
            'nama_lengkap' => 'Anggota Ahmad',
            'nia' => '24260001',
        ]);
        Anggota::factory()->create([
            'nama_lengkap' => 'Siti Walidah',
            'nia' => '24260002',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => '24260002']));

        $response->assertSuccessful();
        $response->assertSee('Siti Walidah');
        $response->assertDontSee('Anggota Ahmad');
    });

    test('menampilkan empty state jika hasil pencarian tidak ditemukan', function () {
        $admin = User::factory()->admin()->create();
        Anggota::factory()->create([
            'nama_lengkap' => 'Ahmad Dahlan',
            'nia' => '24260001',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Walidah']));

        $response->assertSuccessful();
        $response->assertSee('Tidak ada anggota yang sesuai dengan pencarian atau filter yang dipilih.');
        $response->assertSee('Atur ulang filter');
    });

    test('link pagination tetap membawa parameter pencarian', function () {
        $admin = User::factory()->admin()->create();

        // Buat 15 anggota dengan nama berawalan "Ahmad" agar terjadi pagination (limit 12)
        Anggota::factory()->count(15)->create([
            'nama_lengkap' => 'Ahmad '.fake()->unique()->name(),
        ]);

        // Buat 1 anggota lain
        Anggota::factory()->create([
            'nama_lengkap' => 'Siti Walidah',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Ahmad']));

        $response->assertSuccessful();
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

        $response->assertSuccessful();
        $response->assertSee(route('admin.anggota.show', $anggota->id));

        $detailResponse = $this->actingAs($admin)
            ->get(route('admin.anggota.show', $anggota->id));
        $detailResponse->assertSuccessful();
        $detailResponse->assertSee('Ahmad Dahlan');
    });

    test('admin dapat memfilter anggota berdasarkan setiap role', function () {
        $admin = User::factory()->admin()->create();
        $kader = User::factory()->kader()->create();
        $instruktur = User::factory()->instruktur()->create();
        $adminAnggota = User::factory()->admin()->create();

        Anggota::factory()->create(['user_id' => $kader->id, 'nama_lengkap' => 'Anggota Kader Target']);
        Anggota::factory()->create(['user_id' => $instruktur->id, 'nama_lengkap' => 'Anggota Instruktur Target']);
        Anggota::factory()->create(['user_id' => $adminAnggota->id, 'nama_lengkap' => 'Anggota Admin Target']);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['role' => 'kader']))
            ->assertSuccessful()
            ->assertSee('Anggota Kader Target')
            ->assertDontSee('Anggota Instruktur Target')
            ->assertDontSee('Anggota Admin Target');

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['role' => 'instruktur']))
            ->assertSuccessful()
            ->assertSee('value="instruktur" selected', false)
            ->assertSee('Anggota Instruktur Target')
            ->assertDontSee('Anggota Kader Target')
            ->assertDontSee('Anggota Admin Target');

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['role' => 'admin']))
            ->assertSuccessful()
            ->assertSee('Anggota Admin Target')
            ->assertDontSee('Anggota Kader Target')
            ->assertDontSee('Anggota Instruktur Target');
    });

    test('admin dapat memfilter anggota berdasarkan komisariat', function () {
        $admin = User::factory()->admin()->create();
        $ahmad = Anggota::factory()->create([
            'nama_lengkap' => 'Anggota Ahmad',
            'komisariat_id' => 'ahmad-dahlan',
        ]);
        Anggota::factory()->create([
            'nama_lengkap' => 'Anggota Buya',
            'komisariat_id' => 'buya-hamka',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['komisariat' => 'ahmad-dahlan']))
            ->assertSuccessful()
            ->assertSee('Anggota Ahmad')
            ->assertDontSee('Anggota Buya')
            ->assertSee('<option value="ahmad-dahlan" selected>Ahmad Dahlan</option>', false);

        expect($ahmad->komisariat_id)->toBe('ahmad-dahlan');
    });

    test('filter komisariat mempertahankan parameter saat sorting', function () {
        $admin = User::factory()->admin()->create();
        Anggota::factory()->count(13)->create(['komisariat_id' => 'ahmad-dahlan']);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', [
                'komisariat' => 'ahmad-dahlan',
                'sort' => 'nama',
                'direction' => 'asc',
            ]))
            ->assertSuccessful()
            ->assertSee('komisariat=ahmad-dahlan', false)
            ->assertSee('sort=nama', false)
            ->assertSee('direction=asc', false);
    });

    test('komisariat tidak valid ditolak oleh validasi', function () {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.anggota.index'))
            ->get(route('admin.anggota.index', ['komisariat' => 'tidak-valid']))
            ->assertRedirect(route('admin.anggota.index'))
            ->assertSessionHasErrors('komisariat');
    });

    test('dropdown filter tidak menawarkan role admin', function () {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.anggota.index'))
            ->assertSuccessful()
            ->assertSee('<option value="kader"', false)
            ->assertSee('<option value="instruktur"', false)
            ->assertDontSee('<option value="admin"', false);
    });

    test('pencarian nama dan filter role diterapkan bersamaan', function () {
        $admin = User::factory()->admin()->create();
        $kader = User::factory()->kader()->create();
        $instruktur = User::factory()->instruktur()->create();

        Anggota::factory()->create(['user_id' => $kader->id, 'nama_lengkap' => 'Bersama Kader']);
        Anggota::factory()->create(['user_id' => $instruktur->id, 'nama_lengkap' => 'Bersama Instruktur']);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Bersama', 'role' => 'instruktur']))
            ->assertSuccessful()
            ->assertSee('Bersama Instruktur')
            ->assertDontSee('Bersama Kader');
    });

    test('pencarian NIA dan filter role diterapkan bersamaan', function () {
        $admin = User::factory()->admin()->create();
        $kader = User::factory()->kader()->create();
        $instruktur = User::factory()->instruktur()->create();

        Anggota::factory()->create([
            'user_id' => $kader->id,
            'nama_lengkap' => 'NIA Kader Bersama',
            'nia' => '78260001',
        ]);
        Anggota::factory()->create([
            'user_id' => $instruktur->id,
            'nama_lengkap' => 'NIA Instruktur Bersama',
            'nia' => '78260002',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => '7826', 'role' => 'instruktur']))
            ->assertSuccessful()
            ->assertSee('NIA Instruktur Bersama')
            ->assertDontSee('NIA Kader Bersama');
    });

    test('filter tanpa hasil menampilkan empty state dan link atur ulang', function () {
        $admin = User::factory()->admin()->create();
        Anggota::factory()->create(['nama_lengkap' => 'Hanya Anggota Kader']);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['role' => 'instruktur']))
            ->assertSuccessful()
            ->assertSee('Tidak ada anggota yang sesuai dengan pencarian atau filter yang dipilih.')
            ->assertSee('Atur ulang filter')
            ->assertSee('href="'.route('admin.anggota.index').'"', false)
            ->assertDontSee('Belum ada data anggota.');
    });

    test('link atur ulang mengembalikan seluruh anggota tanpa query filter', function () {
        $admin = User::factory()->admin()->create();
        $kader = Anggota::factory()->create(['nama_lengkap' => 'Reset Kader']);
        $instrukturUser = User::factory()->instruktur()->create();
        $instruktur = Anggota::factory()->create([
            'user_id' => $instrukturUser->id,
            'nama_lengkap' => 'Reset Instruktur',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Reset', 'role' => 'instruktur']))
            ->assertSuccessful()
            ->assertSee('href="'.route('admin.anggota.index').'"', false)
            ->assertSee($instruktur->nama_lengkap)
            ->assertDontSee($kader->nama_lengkap);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index'))
            ->assertSuccessful()
            ->assertSee($kader->nama_lengkap)
            ->assertSee($instruktur->nama_lengkap);
    });

    test('pagination mempertahankan pencarian dan filter role', function () {
        $admin = User::factory()->admin()->create();
        $instrukturUsers = User::factory()->instruktur()->count(13)->create();

        foreach ($instrukturUsers as $index => $instruktur) {
            Anggota::factory()->create([
                'user_id' => $instruktur->id,
                'nama_lengkap' => 'Paging Target '.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'created_at' => now()->addSeconds($index),
            ]);
        }

        Anggota::factory()->create(['nama_lengkap' => 'Paging Target Kader']);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', ['search' => 'Paging Target', 'role' => 'instruktur']))
            ->assertSuccessful()
            ->assertSee('page=2')
            ->assertSee('search=Paging%20Target', false)
            ->assertSee('role=instruktur', false);

        $this->actingAs($admin)
            ->get(route('admin.anggota.index', [
                'search' => 'Paging Target',
                'role' => 'instruktur',
                'page' => 2,
            ]))
            ->assertSuccessful()
            ->assertSee('Paging Target 00')
            ->assertDontSee('Paging Target Kader');
    });

    test('role tidak valid ditolak oleh validasi', function () {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.anggota.index'))
            ->get(route('admin.anggota.index', ['role' => 'tidak-valid']))
            ->assertRedirect(route('admin.anggota.index'))
            ->assertSessionHasErrors('role');
    });

    test('daftar anggota dengan filter tetap hanya dapat diakses admin', function () {
        $kader = User::factory()->kader()->create();
        $instruktur = User::factory()->instruktur()->create();
        $url = route('admin.anggota.index', ['role' => 'kader']);

        $this->actingAs($kader)->get($url)->assertForbidden();
        $this->actingAs($instruktur)->get($url)->assertForbidden();
        auth()->logout();
        $this->get($url)->assertRedirect(route('login'));
    });
});
