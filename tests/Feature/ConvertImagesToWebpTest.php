<?php

use App\Models\Anggota;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('images:webp converts stored images and updates their references', function () {
    Storage::fake('public');
    Storage::fake('local');
    $source = UploadedFile::fake()->image('old.jpg', 24, 16);
    Storage::disk('public')->put('foto_profil/old.jpg', file_get_contents($source->getPathname()));
    $anggota = Anggota::factory()->create(['foto_profil' => 'foto_profil/old.jpg']);

    $this->artisan('images:webp --dry-run')->assertSuccessful();
    expect($anggota->refresh()->foto_profil)->toBe('foto_profil/old.jpg');

    $this->artisan('images:webp')->assertSuccessful();

    $path = $anggota->refresh()->foto_profil;
    expect($path)->toStartWith('foto_profil/')->toEndWith('.webp')
        ->and(getimagesizefromstring(Storage::disk('public')->get($path))['mime'])->toBe('image/webp');
    Storage::disk('public')->assertMissing('foto_profil/old.jpg');
});
