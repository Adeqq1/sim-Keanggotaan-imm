<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('instruktur redirects to admin.kegiatan.index on login', function () {
    $instruktur = User::factory()->instruktur()->create();

    $response = $this->post(route('login'), [
        'email' => $instruktur->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.kegiatan.index'));
});

test('instruktur can access kegiatan management and store new kegiatan with thumbnail', function () {
    Storage::fake('public');
    $instruktur = User::factory()->instruktur()->create();

    $response = $this->actingAs($instruktur)->get(route('admin.kegiatan.index'));
    $response->assertOk();

    $file = UploadedFile::fake()->create('thumbnail.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($instruktur)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Latihan Kader',
        'deskripsi' => 'Deskripsi latihan kader',
        'tanggal_waktu' => '2026-06-10 10:00:00',
        'lokasi' => 'Aula IMM',
        'thumbnail' => $file,
    ]);

    $response->assertRedirect(route('admin.kegiatan.index'));

    $kegiatan = Kegiatan::where('nama_kegiatan', 'Latihan Kader')->first();
    expect($kegiatan)->not->toBeNull();
    expect($kegiatan->thumbnail)->not->toBeNull();

    Storage::disk('public')->assertExists($kegiatan->thumbnail);
});

test('instruktur can update kegiatan and replace thumbnail', function () {
    Storage::fake('public');
    $instruktur = User::factory()->instruktur()->create();

    $oldFile = UploadedFile::fake()->create('old.jpg', 100, 'image/jpeg');
    $oldPath = $oldFile->store('kegiatan_thumbnails', 'public');

    $kegiatan = Kegiatan::factory()->create([
        'thumbnail' => $oldPath,
    ]);

    Storage::disk('public')->assertExists($oldPath);

    $newFile = UploadedFile::fake()->create('new.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($instruktur)->put(route('admin.kegiatan.update', $kegiatan), [
        'nama_kegiatan' => 'Latihan Kader Updated',
        'tanggal_waktu' => '2026-06-12 10:00:00',
        'lokasi' => 'Aula IMM Baru',
        'thumbnail' => $newFile,
    ]);

    $response->assertRedirect(route('admin.kegiatan.index'));

    $kegiatan->refresh();
    expect($kegiatan->nama_kegiatan)->toBe('Latihan Kader Updated');
    expect($kegiatan->thumbnail)->not->toBe($oldPath);

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($kegiatan->thumbnail);
});

test('instruktur can delete kegiatan and its thumbnail', function () {
    Storage::fake('public');
    $instruktur = User::factory()->instruktur()->create();

    $file = UploadedFile::fake()->create('thumb.jpg', 100, 'image/jpeg');
    $path = $file->store('kegiatan_thumbnails', 'public');

    $kegiatan = Kegiatan::factory()->create([
        'thumbnail' => $path,
    ]);

    Storage::disk('public')->assertExists($path);

    $response = $this->actingAs($instruktur)->delete(route('admin.kegiatan.destroy', $kegiatan));

    $response->assertRedirect(route('admin.kegiatan.index'));

    $this->assertDatabaseMissing('kegiatan', ['id' => $kegiatan->id]);
    Storage::disk('public')->assertMissing($path);
});

test('instruktur can store presensi data', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota1 = Anggota::factory()->create();
    $anggota2 = Anggota::factory()->create();

    $response = $this->actingAs($instruktur)
        ->post(route('admin.presensi.store', $kegiatan->id), [
            'presensi' => [
                $anggota1->id => 'hadir',
                $anggota2->id => 'izin',
            ],
        ]);

    $response->assertRedirect(route('admin.kegiatan.index'));

    $this->assertDatabaseHas('presensi', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota1->id,
        'status_kehadiran' => 'hadir',
    ]);

    $this->assertDatabaseHas('presensi', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota2->id,
        'status_kehadiran' => 'izin',
    ]);
});

test('kader cannot access kegiatan and presensi management', function () {
    $kader = User::factory()->kader()->create();
    $kegiatan = Kegiatan::factory()->create();

    $response = $this->actingAs($kader)->get(route('admin.kegiatan.index'));
    $response->assertForbidden();

    $response = $this->actingAs($kader)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Illegal',
        'tanggal_waktu' => '2026-06-10 10:00:00',
        'lokasi' => 'Aula',
    ]);
    $response->assertForbidden();

    $response = $this->actingAs($kader)->get(route('admin.presensi.show', $kegiatan->id));
    $response->assertForbidden();
});

test('invalid thumbnail upload is rejected', function () {
    $instruktur = User::factory()->instruktur()->create();

    $badFile = UploadedFile::fake()->create('malware.exe', 500);

    $response = $this->actingAs($instruktur)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Latihan Kader',
        'tanggal_waktu' => '2026-06-10 10:00:00',
        'lokasi' => 'Aula IMM',
        'thumbnail' => $badFile,
    ]);

    $response->assertSessionHasErrors('thumbnail');

    $largeFile = UploadedFile::fake()->create('large.jpg', 3000, 'image/jpeg');

    $response = $this->actingAs($instruktur)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Latihan Kader 2',
        'tanggal_waktu' => '2026-06-10 10:00:00',
        'lokasi' => 'Aula IMM 2',
        'thumbnail' => $largeFile,
    ]);

    $response->assertSessionHasErrors('thumbnail');
});
