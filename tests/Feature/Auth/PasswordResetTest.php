<?php

use App\Models\User;
use App\Notifications\QueuedResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $this->get('/forgot-password')->assertOk();
});

test('reset password link can be requested', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHas('status', __('passwords.sent'));

    Notification::assertSentTo($user, QueuedResetPassword::class);
});

test('unknown reset email uses the same generic response', function () {
    Notification::fake();

    $this->post('/forgot-password', ['email' => 'tidak.terdaftar@example.com'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', __('passwords.sent'));

    Notification::assertNothingSent();
});

test('repeated request for a registered email does not reveal throttling', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', __('passwords.sent'));

    Notification::assertSentToTimes($user, QueuedResetPassword::class, 1);
});

test('reset link requests are limited to five per minute per IP', function () {
    Notification::fake();
    $ip = '203.0.113.20';

    foreach (range(1, 5) as $attempt) {
        $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post('/forgot-password', ['email' => "unknown-{$attempt}@example.com"])
            ->assertSessionHas('status', __('passwords.sent'));
    }

    $this->withServerVariables(['REMOTE_ADDR' => $ip])
        ->from('/forgot-password')
        ->post('/forgot-password', ['email' => 'blocked@example.com'])
        ->assertRedirect('/forgot-password')
        ->assertSessionHasErrors(['email' => __('passwords.throttled')]);

    Notification::assertNothingSent();
});

test('reset password screen can be rendered', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, QueuedResetPassword::class, function ($notification) {
        $this->get('/reset-password/'.$notification->token)->assertOk();

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();
    $user = User::factory()->create();
    $oldRememberToken = $user->remember_token;

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, QueuedResetPassword::class, function ($notification) use ($oldRememberToken, $user) {
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

test('reset with an unknown email shows the same error as an invalid token', function () {
    $this->from('/reset-password/invalid-token')->post('/reset-password', [
        'token' => 'invalid-token',
        'email' => 'tidak.terdaftar@example.com',
        'password' => 'password-baru',
        'password_confirmation' => 'password-baru',
    ])->assertRedirect('/reset-password/invalid-token')
        ->assertSessionHasErrors(['email' => __('passwords.token')]);
});

test('reset password email is written in Indonesian', function () {
    $user = User::factory()->create();

    $mail = (new QueuedResetPassword('token-contoh'))->toMail($user);

    expect($mail->subject)->toBe('Atur ulang kata sandi Anda')
        ->and($mail->actionText)->toBe('Atur Ulang Kata Sandi');
});

test('expired reset token is rejected', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, QueuedResetPassword::class, function ($notification) use ($user) {
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

    Notification::assertSentTo($user, QueuedResetPassword::class, function ($notification) use ($user) {
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
