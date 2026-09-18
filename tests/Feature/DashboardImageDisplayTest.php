<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('user avatar accessors only return a URL when the photo exists', function () {
    Storage::fake('public');
    $user = User::factory()->admin()->create(['name' => 'Ahmad Dahlan']);
    $anggota = Anggota::factory()->create([
        'user_id' => $user->id,
        'nama_lengkap' => 'Ahmad Dahlan',
        'foto_profil' => 'foto_profil/avatar.webp',
    ]);

    expect($user->refresh()->initials)->toBe('AD')
        ->and($user->profile_photo_url)->toBeNull()
        ->and($anggota->initials)->toBe('AD')
        ->and($anggota->foto_profil_url)->toBeNull();

    Storage::disk('public')->put($anggota->foto_profil, 'image');

    expect($user->refresh()->profile_photo_url)->toContain('storage/foto_profil/avatar.webp')
        ->and($anggota->refresh()->foto_profil_url)->toContain('storage/foto_profil/avatar.webp');
});

test('initials accessor handles single-word and multi-word names gracefully', function () {
    $userSingle = User::factory()->make(['name' => 'Sukarno']);
    $userDouble = User::factory()->make(['name' => 'Ahmad Dahlan']);
    $userEmpty = User::factory()->make(['name' => '']);

    expect($userSingle->initials)->toBe('S')
        ->and($userDouble->initials)->toBe('AD')
        ->and($userEmpty->initials)->toBe('U');
});

test('admin without anggota renders initials in the shared layout', function () {
    $admin = User::factory()->admin()->create(['name' => 'Ahmad Dahlan']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSeeText('AD')
        ->assertDontSee('Storage::url', false);
});

test('missing activity thumbnail uses the placeholder', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    Kegiatan::factory()->create(['thumbnail' => 'kegiatan_thumbnails/missing.jpg']);

    $this->actingAs($admin)
        ->get(route('admin.kegiatan.index'))
        ->assertSuccessful()
        ->assertSee('images/placeholder-kegiatan.png', false)
        ->assertDontSee('storage/kegiatan_thumbnails/missing.jpg', false);
});

test('existing activity thumbnail uses the public asset URL', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create(['thumbnail' => 'kegiatan_thumbnails/activity.webp']);
    Storage::disk('public')->put($kegiatan->thumbnail, 'image');

    $this->actingAs($admin)
        ->get(route('admin.kegiatan.index'))
        ->assertSuccessful()
        ->assertSee('storage/kegiatan_thumbnails/activity.webp', false);
});
