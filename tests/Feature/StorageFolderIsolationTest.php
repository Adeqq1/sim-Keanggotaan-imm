<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\User;
use App\Services\ProfilePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('profile photos stay in the isolated public folder', function () {
    Storage::fake('public');

    $path = app(ProfilePhoto::class)->store(UploadedFile::fake()->image('avatar.jpg'));

    expect($path)->toStartWith('foto_profil/')->toEndWith('.webp');
    Storage::disk('public')->assertExists($path);
    expect(Storage::disk('public')->allFiles())->toEqual([$path]);
});

test('activity thumbnails stay isolated and are cleaned up on replacement and deletion', function () {
    Storage::fake('public');
    $instruktur = User::factory()->instruktur()->create();

    $response = $this->actingAs($instruktur)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Latihan Kader',
        'deskripsi' => 'Deskripsi kegiatan',
        'tanggal_waktu' => now()->addDays(3)->format('Y-m-d H:i'),
        'lokasi' => 'Auditorium Kampus',
        'tahun_angkatan' => [now()->year],
        'jenis_pelaksanaan' => 'satu_sesi',
        'minimum_sesi_terverifikasi' => 1,
        'thumbnail' => UploadedFile::fake()->image('banner.jpg'),
    ]);

    $response->assertRedirect(route('admin.kegiatan.index'));
    $kegiatan = Kegiatan::where('nama_kegiatan', 'Latihan Kader')->firstOrFail();
    $oldPath = $kegiatan->thumbnail;

    expect($oldPath)->toStartWith('kegiatan_thumbnails/');
    Storage::disk('public')->assertExists($oldPath);

    $this->actingAs($instruktur)->put(route('admin.kegiatan.update', $kegiatan), [
        'nama_kegiatan' => $kegiatan->nama_kegiatan,
        'deskripsi' => $kegiatan->deskripsi,
        'tanggal_waktu' => $kegiatan->tanggal_waktu->format('Y-m-d H:i'),
        'lokasi' => $kegiatan->lokasi,
        'tahun_angkatan' => [$kegiatan->tahunAngkatans()->value('tahun_daftar')],
        'jenis_pelaksanaan' => 'satu_sesi',
        'minimum_sesi_terverifikasi' => 1,
        'thumbnail' => UploadedFile::fake()->image('replacement.jpg'),
    ])->assertRedirect(route('admin.kegiatan.index'));

    $newPath = $kegiatan->refresh()->thumbnail;
    expect($newPath)->toStartWith('kegiatan_thumbnails/')->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($newPath);
    expect(Storage::disk('public')->allFiles())->toEqual([$newPath]);

    $this->actingAs($instruktur)->delete(route('admin.kegiatan.destroy', $kegiatan))
        ->assertRedirect(route('admin.kegiatan.index'));

    Storage::disk('public')->assertMissing($newPath);
});

test('deleting an account also removes its profile photo', function () {
    Storage::fake('public');
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);
    $path = 'foto_profil/account-photo.webp';
    $anggota->update(['foto_profil' => $path]);
    Storage::disk('public')->put($path, 'photo');

    $this->actingAs($user)->delete(route('profile.destroy'), [
        'password' => 'password',
    ])->assertRedirect('/');

    Storage::disk('public')->assertMissing($path);
});
