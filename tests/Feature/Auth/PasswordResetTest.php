<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

const RESET_LINK_STATUS = 'Jika alamat email tersebut terdaftar, kami telah mengirimkan tautan pengaturan ulang kata sandi.';

test('reset password link screen can be rendered', function () {
    $this->get('/forgot-password')->assertOk();
});

test('reset password link can be requested', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHas('status', RESET_LINK_STATUS);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('unknown reset email uses the same generic response', function () {
    Notification::fake();

    $this->post('/forgot-password', ['email' => 'tidak.terdaftar@example.com'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', RESET_LINK_STATUS);

    Notification::assertNothingSent();
});

test('reset link requests are limited to five per minute per IP', function () {
    Notification::fake();
    $ip = '203.0.113.20';

    foreach (range(1, 5) as $attempt) {
        $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post('/forgot-password', ['email' => "unknown-{$attempt}@example.com"])
            ->assertSessionHas('status', RESET_LINK_STATUS);
    }

    $this->withServerVariables(['REMOTE_ADDR' => $ip])
        ->post('/forgot-password', ['email' => 'blocked@example.com'])
        ->assertTooManyRequests()
        ->assertHeader('X-RateLimit-Limit', '5')
        ->assertHeader('X-RateLimit-Remaining', '0');

    Notification::assertNothingSent();
});

test('reset password screen can be rendered', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $this->get('/reset-password/'.$notification->token)->assertOk();

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();
    $user = User::factory()->create();
    $oldRememberToken = $user->remember_token;

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($oldRememberToken, $user) {
        $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'Kata sandi Anda telah berhasil diatur ulang.')
            ->assertRedirect(route('login'));

        $user->refresh();

        expect(Hash::check('password-baru', $user->password))->toBeTrue()
            ->and($user->remember_token)->not->toBe($oldRememberToken);
        $this->assertCredentials(['email' => $user->email, 'password' => 'password-baru']);
        $this->assertInvalidCredentials(['email' => $user->email, 'password' => 'password']);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);

        return true;
    });
});

test('password cannot be reset with an invalid token', function () {
    $user = User::factory()->create();

    $this->from('/reset-password/invalid-token')->post('/reset-password', [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'password-baru',
        'password_confirmation' => 'password-baru',
    ])->assertRedirect('/reset-password/invalid-token')
        ->assertSessionHasErrors('email');

    $this->assertCredentials(['email' => $user->email, 'password' => 'password']);
});

test('expired reset token is rejected', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $this->travel(61)->minutes();

        $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertSessionHasErrors('email');

        $this->assertCredentials(['email' => $user->email, 'password' => 'password']);

        return true;
    });
});

test('reset token can only be used once', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $payload = [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ];

        $this->post('/reset-password', $payload)->assertRedirect(route('login'));
        $this->post('/reset-password', $payload)->assertSessionHasErrors('email');

        return true;
    });
});
