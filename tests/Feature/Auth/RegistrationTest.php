<?php

use App\Models\Anggota;

test('registration route redirects to public pendaftaran', function () {
    $response = $this->get('/register');

    $response->assertRedirect(route('pendaftaran'));
});

test('registration post does not create an account outside the approval workflow', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    $this->assertDatabaseMissing('pendaftaran', ['email' => 'test@example.com']);
    $this->assertDatabaseCount(Anggota::class, 0);
});
