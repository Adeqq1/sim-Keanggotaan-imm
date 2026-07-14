<?php

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function validPendaftaranPayload(array $overrides = []): array
{
    return array_merge([
        'nama_lengkap' => 'Ahmad Instruktur',
        'email' => 'ahmad.instruktur@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'instruktur',
        'tempat_lahir' => 'Yogyakarta',
        'tanggal_lahir' => '2000-01-01',
        'no_telp' => '08123456789',
        'alamat' => 'Jl. Contoh No. 1',
    ], $overrides);
}

test('public pendaftaran form shows role choices', function () {
    $response = $this->get(route('pendaftaran'));

    $response->assertOk();
    $response->assertSee('Daftar Sebagai');
    $response->assertSee('value="kader"', false);
    $response->assertSee('value="instruktur"', false);
    $response->assertSee('type="password" name="password"', false);
    $response->assertSee('type="password" name="password_confirmation"', false);
});

test('public pendaftaran stores selected role', function () {
    $password = 'password';
    $response = $this->post(route('pendaftaran.store'), validPendaftaranPayload(['password' => $password, 'password_confirmation' => $password]));

    $response->assertRedirect(route('pendaftaran.success'));

    $this->assertDatabaseHas('pendaftaran', [
        'email' => 'ahmad.instruktur@example.com',
        'role' => 'instruktur',
        'status_validasi' => 'pending',
    ]);

    $pendaftaran = Pendaftaran::where('email', 'ahmad.instruktur@example.com')->firstOrFail();
    expect($pendaftaran->password)->not->toBe($password)
        ->and(Hash::check($password, $pendaftaran->password))->toBeTrue();
});

test('public pendaftaran requires a confirmed password', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'email' => 'password.invalid@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('password');
    $this->assertDatabaseMissing('pendaftaran', ['email' => 'password.invalid@example.com']);
});

test('public pendaftaran requires a password', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'email' => 'password.required@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('password');
    $this->assertDatabaseMissing('pendaftaran', ['email' => 'password.required@example.com']);
});

test('public pendaftaran enforces the default password policy', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'email' => 'password.weak@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('password');
    $this->assertDatabaseMissing('pendaftaran', ['email' => 'password.weak@example.com']);
});

test('public pendaftaran rejects admin role', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'nama_lengkap' => 'Calon Admin',
            'email' => 'calon.admin@example.com',
            'role' => 'admin',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('role');

    $this->assertDatabaseMissing('pendaftaran', [
        'email' => 'calon.admin@example.com',
    ]);
});

test('public pendaftaran rejects email that already belongs to a user', function () {
    $user = User::factory()->kader()->create();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'nama_lengkap' => 'Email Terdaftar',
            'email' => $user->email,
            'role' => 'kader',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('email');
});

test('public pendaftaran rejects duplicate pending registration email', function () {
    $pendaftaran = Pendaftaran::factory()->create();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'nama_lengkap' => 'Pendaftar Duplikat',
            'email' => $pendaftaran->email,
            'role' => 'kader',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('email');
});
