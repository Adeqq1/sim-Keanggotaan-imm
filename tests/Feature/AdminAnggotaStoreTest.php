<?php

use App\Models\Anggota;
use App\Models\User;

test('admin can create anggota with linked user account', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.anggota.store'), [
            'nia' => '24260099',
            'nama_lengkap' => 'M. Miftahul Khoiri. S',
            'tempat_lahir' => 'Merangin',
            'tanggal_lahir' => '2005-05-16',
            'alamat' => 'Kerang Berahi',
            'no_telp' => '085288886666',
            'status_aktif' => 1,
            'email' => 'miftahul@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'kader',
        ]);

    $response->assertRedirect(route('admin.anggota.index'));

    $user = User::where('email', 'miftahul@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('M. Miftahul Khoiri. S')
        ->and($user->role)->toBe('kader');

    $this->assertDatabaseHas('anggota', [
        'user_id' => $user->id,
        'nia' => '24260099',
        'nama_lengkap' => 'M. Miftahul Khoiri. S',
        'status_aktif' => 1,
    ]);
});

test('admin create anggota requires email password and role', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->from(route('admin.anggota.create'))
        ->post(route('admin.anggota.store'), [
            'nama_lengkap' => 'Tanpa Akun',
            'tempat_lahir' => 'Jambi',
            'tanggal_lahir' => '2000-01-01',
            'alamat' => 'Alamat',
            'no_telp' => '081234567890',
            'status_aktif' => 1,
        ]);

    $response->assertRedirect(route('admin.anggota.create'));
    $response->assertSessionHasErrors(['email', 'password', 'role']);
    expect(Anggota::where('nama_lengkap', 'Tanpa Akun')->exists())->toBeFalse();
});
