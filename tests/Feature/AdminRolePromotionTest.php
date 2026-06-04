<?php

use App\Models\Anggota;
use App\Models\User;

test('admin can access member edit page and see role dropdown', function () {
    $admin = User::factory()->admin()->create();
    $anggota = Anggota::factory()->create();

    $response = $this->actingAs($admin)
        ->get(route('admin.anggota.edit', $anggota->id));

    $response->assertSuccessful();
    $response->assertSee('Peran');
    $response->assertSee('value="kader"', false);
    $response->assertSee('value="instruktur"', false);
});

test('admin can promote kader to instruktur', function () {
    $admin = User::factory()->admin()->create();

    // Create an Anggota whose associated User is a kader
    $anggotaUser = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $anggotaUser->id]);

    $response = $this->actingAs($admin)
        ->put(route('admin.anggota.update', $anggota->id), [
            'nia' => $anggota->nia,
            'nama_lengkap' => $anggota->nama_lengkap,
            'tempat_lahir' => $anggota->tempat_lahir,
            'tanggal_lahir' => $anggota->tanggal_lahir->format('Y-m-d'),
            'alamat' => $anggota->alamat,
            'no_telp' => $anggota->no_telp,
            'status_aktif' => 1,
            'role' => 'instruktur',
        ]);

    $response->assertRedirect(route('admin.anggota.index'));

    $anggotaUser->refresh();
    expect($anggotaUser->role)->toBe('instruktur');
});

test('admin can demote instruktur to kader', function () {
    $admin = User::factory()->admin()->create();

    // Create an Anggota whose associated User is an instruktur
    $anggotaUser = User::factory()->instruktur()->create();
    $anggota = Anggota::factory()->create(['user_id' => $anggotaUser->id]);

    $response = $this->actingAs($admin)
        ->put(route('admin.anggota.update', $anggota->id), [
            'nia' => $anggota->nia,
            'nama_lengkap' => $anggota->nama_lengkap,
            'tempat_lahir' => $anggota->tempat_lahir,
            'tanggal_lahir' => $anggota->tanggal_lahir->format('Y-m-d'),
            'alamat' => $anggota->alamat,
            'no_telp' => $anggota->no_telp,
            'status_aktif' => 1,
            'role' => 'kader',
        ]);

    $response->assertRedirect(route('admin.anggota.index'));

    $anggotaUser->refresh();
    expect($anggotaUser->role)->toBe('kader');
});

test('role validation only allows kader and instruktur', function () {
    $admin = User::factory()->admin()->create();
    $anggota = Anggota::factory()->create();

    $response = $this->actingAs($admin)
        ->from(route('admin.anggota.edit', $anggota->id))
        ->put(route('admin.anggota.update', $anggota->id), [
            'nia' => $anggota->nia,
            'nama_lengkap' => $anggota->nama_lengkap,
            'tempat_lahir' => $anggota->tempat_lahir,
            'tanggal_lahir' => $anggota->tanggal_lahir->format('Y-m-d'),
            'alamat' => $anggota->alamat,
            'no_telp' => $anggota->no_telp,
            'status_aktif' => 1,
            'role' => 'admin', // invalid role (not allowed via edit form)
        ]);

    $response->assertSessionHasErrors('role');

    $anggota->user->refresh();
    expect($anggota->user->role)->not->toBe('admin');
});

test('non-admin users cannot promote or access edit pages', function () {
    $kader = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create();

    // Try accessing edit view
    $response = $this->actingAs($kader)
        ->get(route('admin.anggota.edit', $anggota->id));
    $response->assertStatus(403);

    // Try submitting update
    $response = $this->actingAs($kader)
        ->put(route('admin.anggota.update', $anggota->id), [
            'nia' => $anggota->nia,
            'nama_lengkap' => $anggota->nama_lengkap,
            'tempat_lahir' => $anggota->tempat_lahir,
            'tanggal_lahir' => $anggota->tanggal_lahir->format('Y-m-d'),
            'alamat' => $anggota->alamat,
            'no_telp' => $anggota->no_telp,
            'status_aktif' => 1,
            'role' => 'instruktur',
        ]);
    $response->assertStatus(403);
});

test('admin cannot demote themselves to kader', function () {
    $admin = User::factory()->admin()->create();
    $anggota = Anggota::factory()->create(['user_id' => $admin->id]);

    $response = $this->actingAs($admin)
        ->from(route('admin.anggota.edit', $anggota->id))
        ->put(route('admin.anggota.update', $anggota->id), [
            'nia' => $anggota->nia,
            'nama_lengkap' => $anggota->nama_lengkap,
            'tempat_lahir' => $anggota->tempat_lahir,
            'tanggal_lahir' => $anggota->tanggal_lahir->format('Y-m-d'),
            'alamat' => $anggota->alamat,
            'no_telp' => $anggota->no_telp,
            'status_aktif' => 1,
            'role' => 'kader',
        ]);

    $response->assertStatus(403);

    $admin->refresh();
    expect($admin->role)->toBe('admin');
});
