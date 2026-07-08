<?php

use App\Models\Pendaftaran;
use App\Models\User;

test('public pendaftaran form shows role choices', function () {
    $response = $this->get(route('pendaftaran'));

    $response->assertOk();
    $response->assertSee('Daftar Sebagai');
    $response->assertSee('value="kader"', false);
    $response->assertSee('value="instruktur"', false);
});

test('public pendaftaran stores selected role', function () {
    $response = $this->post(route('pendaftaran.store'), [
        'nama_lengkap' => 'Ahmad Instruktur',
        'email' => 'ahmad.instruktur@example.com',
        'role' => 'instruktur',
        'tempat_lahir' => 'Yogyakarta',
        'tanggal_lahir' => '2000-01-01',
        'no_telp' => '08123456789',
        'alamat' => 'Jl. Contoh No. 1',
    ]);

    $response->assertRedirect(route('pendaftaran.success'));

    $this->assertDatabaseHas('pendaftaran', [
        'email' => 'ahmad.instruktur@example.com',
        'role' => 'instruktur',
        'status_validasi' => 'pending',
    ]);
});

test('public pendaftaran rejects admin role', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), [
            'nama_lengkap' => 'Calon Admin',
            'email' => 'calon.admin@example.com',
            'role' => 'admin',
            'tempat_lahir' => 'Yogyakarta',
            'tanggal_lahir' => '2000-01-01',
            'no_telp' => '08123456789',
            'alamat' => 'Jl. Contoh No. 1',
        ]);

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('role');

    $this->assertDatabaseMissing('pendaftaran', [
        'email' => 'calon.admin@example.com',
    ]);
});

test('public pendaftaran rejects email that already belongs to a user', function () {
    $user = User::factory()->kader()->create();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), [
            'nama_lengkap' => 'Email Terdaftar',
            'email' => $user->email,
            'role' => 'kader',
            'tempat_lahir' => 'Yogyakarta',
            'tanggal_lahir' => '2000-01-01',
            'no_telp' => '08123456789',
            'alamat' => 'Jl. Contoh No. 1',
        ]);

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('email');
});

test('public pendaftaran rejects duplicate pending registration email', function () {
    $pendaftaran = Pendaftaran::factory()->create();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), [
            'nama_lengkap' => 'Pendaftar Duplikat',
            'email' => $pendaftaran->email,
            'role' => 'kader',
            'tempat_lahir' => 'Yogyakarta',
            'tanggal_lahir' => '2000-01-01',
            'no_telp' => '08123456789',
            'alamat' => 'Jl. Contoh No. 1',
        ]);

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('email');
});
