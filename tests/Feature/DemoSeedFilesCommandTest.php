<?php

use App\Models\Arsip;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');
});

test('command fails gracefully if demo records are missing', function () {
    $this->artisan('demo:seed-files')
        ->expectsOutput('Baseline demo records are missing.')
        ->assertFailed();
});

test('command preserves files outside demo namespaces', function () {
    $this->seed(DemoSeeder::class);
    Storage::disk('public')->put('foto_profil/user-uploaded.png', 'content');
    Storage::disk('local')->put('arsip/user-uploaded.pdf', 'content');

    $this->artisan('demo:seed-files')->assertSuccessful();

    Storage::disk('public')->assertExists('foto_profil/user-uploaded.png');
    Storage::disk('local')->assertExists('arsip/user-uploaded.pdf');
});

test('private demo archives enforce authorization', function () {
    $this->seed(DemoSeeder::class);
    $archive = Arsip::where('file_arsip', 'like', 'arsip/demo/%')->firstOrFail();
    $owner = $archive->anggota->user;
    $otherKader = User::where('role', 'kader')->whereKeyNot($owner->id)->whereHas('anggota')->firstOrFail();
    $admin = User::where('role', 'admin')->firstOrFail();

    $this->actingAs($owner)->get(route('kader.arsip.download', $archive))->assertSuccessful();
    $this->actingAs($otherKader)->get(route('kader.arsip.download', $archive))->assertForbidden();
    $this->actingAs($admin)->get(route('admin.arsip.download', $archive))->assertSuccessful();
});
