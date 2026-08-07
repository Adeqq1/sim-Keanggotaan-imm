<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('/forgot-password', ['email' => $user->email]);

    $response->assertSessionHas('status', 'Kami telah mengirimkan tautan pengaturan ulang kata sandi ke email Anda.');
    Notification::assertSentTo($user, ResetPassword::class);
});

test('unknown reset email uses an Indonesian error message', function () {
    Notification::fake();

    $response = $this->from('/forgot-password')->post('/forgot-password', [
        'email' => 'tidak.terdaftar@example.com',
    ]);

    $response
        ->assertRedirect('/forgot-password')
        ->assertSessionHas('errors', function ($errors) {
            return $errors->get('email') === ['Kami tidak dapat menemukan pengguna dengan alamat email tersebut.'];
        });

    Notification::assertNothingSent();
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'Kata sandi Anda telah berhasil diatur ulang.')
            ->assertRedirect(route('login'));

        return true;
    });
});
