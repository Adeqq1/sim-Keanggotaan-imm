<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->kader()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('kader.dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response
        ->assertRedirect('/login')
        ->assertSessionHas('errors', function ($errors) {
            return $errors->get('email') === ['Email atau kata sandi yang Anda masukkan tidak sesuai.'];
        });

    $this->assertGuest();
});

test('login throttling uses an Indonesian error message', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $_) {
        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHas('errors', function ($errors) {
        return str_starts_with($errors->first('email'), 'Terlalu banyak percobaan masuk.');
    });

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
