<?php

use App\Models\Anggota;
use App\Models\Pendaftaran;
use App\Services\ProfilePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

test('images:webp converts stored images and updates their references', function () {
    Storage::fake('public');
    Storage::fake('local');
    $source = UploadedFile::fake()->image('old.jpg', 24, 16);
    Storage::disk('public')->put('foto_profil/old.jpg', file_get_contents($source->getPathname()));
    $anggota = Anggota::factory()->create(['foto_profil' => 'foto_profil/old.jpg']);

    $this->artisan('images:webp --dry-run')
        ->expectsOutputToContain('[dry-run] public:foto_profil/old.jpg')
        ->assertSuccessful();
    expect($anggota->refresh()->foto_profil)->toBe('foto_profil/old.jpg');

    $this->artisan('images:webp')->assertSuccessful();

    $path = $anggota->refresh()->foto_profil;
    expect($path)->toStartWith('foto_profil/')->toEndWith('.webp')
        ->and(getimagesizefromstring(Storage::disk('public')->get($path))['mime'])->toBe('image/webp');
    Storage::disk('public')->assertMissing('foto_profil/old.jpg');
});

test('images:webp leaves non-image files untouched', function () {
    Storage::fake('local');
    Storage::disk('local')->put('pendaftaran/doc.pdf', '%PDF-1.4 content');
    $pendaftaran = Pendaftaran::factory()->create(['file_persyaratan' => 'pendaftaran/doc.pdf']);

    $this->artisan('images:webp')->assertSuccessful();

    expect($pendaftaran->refresh()->file_persyaratan)->toBe('pendaftaran/doc.pdf');
    Storage::disk('local')->assertExists('pendaftaran/doc.pdf');
});

test('images over 25 megapixels are rejected without writing a file', function () {
    Storage::fake('public');
    $tmp = tempnam(sys_get_temp_dir(), 'big-');
    // Minimal PNG header with a 6000x6000 IHDR (36 MP); pixel data is not read by getimagesize().
    file_put_contents($tmp, "\x89PNG\r\n\x1a\n".pack('N', 13).'IHDR'.pack('NN', 6000, 6000)."\x08\x02\x00\x00\x00".pack('N', 0));

    try {
        expect(fn () => app(ProfilePhoto::class)->store(new UploadedFile($tmp, 'big.png', 'image/png', null, true), 'kegiatan_thumbnails', 'public', 'thumbnail'))
            ->toThrow(ValidationException::class);
        expect(Storage::disk('public')->allFiles())->toBeEmpty();
    } finally {
        unlink($tmp);
    }
});

test('images:webp fails without touching the database or deleting the corrupt source', function () {
    Storage::fake('public');
    Storage::disk('public')->put('foto_profil/rusak.jpg', 'bukan gambar');
    $anggota = Anggota::factory()->create(['foto_profil' => 'foto_profil/rusak.jpg']);

    $this->artisan('images:webp')->assertFailed();

    expect($anggota->refresh()->foto_profil)->toBe('foto_profil/rusak.jpg');
    Storage::disk('public')->assertExists('foto_profil/rusak.jpg');
});
