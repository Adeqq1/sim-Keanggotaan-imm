<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('creates an admin interactively', function () {
    $this->artisan('admin:create')
        ->expectsQuestion('Nama admin', 'Admin Hosting')
        ->expectsQuestion('Email admin', 'admin@example.com')
        ->expectsQuestion('Password admin', 'password-rahasia')
        ->expectsQuestion('Konfirmasi password', 'password-rahasia')
        ->expectsOutput('Admin admin@example.com berhasil dibuat.')
        ->assertSuccessful();

    $admin = User::where('email', 'admin@example.com')->firstOrFail();

    expect($admin->name)->toBe('Admin Hosting')
        ->and($admin->role)->toBe('admin')
        ->and(Hash::check('password-rahasia', $admin->password))->toBeTrue();
});

it('rejects an existing email', function () {
    User::factory()->create(['email' => 'admin@example.com']);

    $this->artisan('admin:create')
        ->expectsQuestion('Nama admin', 'Admin Hosting')
        ->expectsQuestion('Email admin', 'admin@example.com')
        ->expectsQuestion('Password admin', 'password-rahasia')
        ->expectsQuestion('Konfirmasi password', 'password-rahasia')
        ->assertFailed();

    expect(User::where('email', 'admin@example.com')->count())->toBe(1);
});
