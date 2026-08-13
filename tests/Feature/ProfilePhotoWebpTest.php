<?php

use App\Models\Anggota;
use App\Models\User;
use App\Services\ProfilePhoto;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

function profilePhotoPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Nama Pengguna',
        'email' => 'profile-photo@example.com',
        'nama_lengkap' => 'Nama Lengkap',
        'tempat_lahir' => 'Jambi',
        'tanggal_lahir' => '2000-01-01',
        'alamat' => 'Alamat lengkap',
        'no_telp' => '081234567890',
    ], $overrides);
}

function adminProfilePhotoPayload(array $overrides = []): array
{
    return array_merge([
        'nia' => '24260123',
        'nama_lengkap' => 'Nama Anggota',
        'tempat_lahir' => 'Jambi',
        'tanggal_lahir' => '2000-01-01',
        'alamat' => 'Alamat lengkap',
        'no_telp' => '081234567890',
        'status_aktif' => 1,
        'email' => 'new-member@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'kader',
    ], $overrides);
}

function assertProfilePhotoIsWebp(string $path, int $width = 24, int $height = 16): void
{
    $bytes = Storage::disk('public')->get($path);
    $imageInfo = getimagesizefromstring($bytes);

    expect($path)->toStartWith('foto_profil/')->toEndWith('.webp');
    expect($imageInfo)->toBeArray()
        ->and($imageInfo['mime'])->toBe('image/webp')
        ->and($imageInfo[0])->toBe($width)
        ->and($imageInfo[1])->toBe($height);
}

test('profile uploads convert jpg jpeg and png sources to webp', function (string $filename) {
    Storage::fake('public');
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), profilePhotoPayload([
            'email' => $user->email,
            'foto_profil' => UploadedFile::fake()->image($filename, 24, 16),
        ]));

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHasNoErrors();

    $path = $anggota->refresh()->foto_profil;
    expect($path)->not->toContain(pathinfo($filename, PATHINFO_FILENAME));
    Storage::disk('public')->assertExists($path);
    assertProfilePhotoIsWebp($path);

    expect(Storage::disk('public')->allFiles('foto_profil'))->toHaveCount(1);
})->with([
    'jpg' => 'source-image.jpg',
    'jpeg' => 'source-image.jpeg',
    'png' => 'source-image.png',
]);

test('admin can create an anggota with a webp profile photo', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.anggota.store'), adminProfilePhotoPayload([
        'foto_profil' => UploadedFile::fake()->image('admin-source.jpg', 24, 16),
    ]));

    $response->assertRedirect(route('admin.anggota.index'))
        ->assertSessionHasNoErrors();

    $anggota = Anggota::where('nama_lengkap', 'Nama Anggota')->firstOrFail();
    Storage::disk('public')->assertExists($anggota->foto_profil);
    assertProfilePhotoIsWebp($anggota->foto_profil);
});

test('profile photo replacement removes a legacy jpg or webp after the new photo is saved', function (string $oldPath) {
    Storage::fake('public');
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);
    $legacyFile = UploadedFile::fake()->image('legacy.'.pathinfo($oldPath, PATHINFO_EXTENSION));
    Storage::disk('public')->put($oldPath, file_get_contents(
        $legacyFile->getPathname()
    ));
    $anggota->update(['foto_profil' => $oldPath]);

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), profilePhotoPayload([
            'email' => $user->email,
            'foto_profil' => UploadedFile::fake()->image('replacement.png', 24, 16),
        ]));

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHasNoErrors();

    $newPath = $anggota->refresh()->foto_profil;
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($newPath);
    assertProfilePhotoIsWebp($newPath);
})->with([
    'legacy jpg' => 'foto_profil/legacy.jpg',
    'legacy webp' => 'foto_profil/legacy.webp',
]);

test('admin update replaces the old photo only after storing the new webp', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $anggota = Anggota::factory()->create([
        'foto_profil' => 'foto_profil/legacy.jpg',
    ]);
    Storage::disk('public')->put($anggota->foto_profil, 'legacy photo');

    $response = $this->actingAs($admin)->put(route('admin.anggota.update', $anggota), adminProfilePhotoPayload([
        'nia' => $anggota->nia,
        'nama_lengkap' => $anggota->nama_lengkap,
        'email' => 'updated-member@example.com',
        'role' => $anggota->user->role,
        'foto_profil' => UploadedFile::fake()->image('new-photo.png', 24, 16),
    ]));

    expect($response->status())->toBe(302)
        ->and($response->headers->get('Location'))->toBe(route('admin.anggota.index'));
    $response->assertSessionHasNoErrors();

    $newPath = $anggota->refresh()->foto_profil;
    Storage::disk('public')->assertMissing('foto_profil/legacy.jpg');
    Storage::disk('public')->assertExists($newPath);
    assertProfilePhotoIsWebp($newPath);
});

test('profile photo is rendered in the shared authenticated layout for every role', function (string $role, string $surfaceRoute) {
    Storage::fake('public');
    $user = User::factory()->create(['role' => $role]);
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->patch(route('profile.update'), profilePhotoPayload([
        'email' => $user->email,
        'foto_profil' => UploadedFile::fake()->image('layout-photo.jpg', 24, 16),
    ]))->assertRedirect(route('profile.edit'));

    $path = $anggota->refresh()->foto_profil;
    $this->actingAs($user)->get(route($surfaceRoute))
        ->assertSuccessful()
        ->assertSee(Storage::url($path), false);
})->with([
    'admin dashboard' => ['admin', 'admin.dashboard'],
    'kader dashboard' => ['kader', 'kader.dashboard'],
    'instruktur activities' => ['instruktur', 'admin.kegiatan.index'],
]);

test('profile and member pages render the stored webp URL', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $anggota = Anggota::factory()->create(['foto_profil' => 'foto_profil/member.webp']);
    Storage::disk('public')->put($anggota->foto_profil, 'webp placeholder');

    $this->actingAs($admin)->get(route('admin.anggota.index'))
        ->assertSuccessful()
        ->assertSee(Storage::url($anggota->foto_profil), false);
    $this->actingAs($admin)->get(route('admin.anggota.show', $anggota))
        ->assertSuccessful()
        ->assertSee(Storage::url($anggota->foto_profil), false);
    $this->actingAs($admin)->get(route('admin.anggota.edit', $anggota))
        ->assertSuccessful()
        ->assertSee(Storage::url($anggota->foto_profil), false);
});

test('profile photo input is hidden and rejected for a user without an anggota', function (string $role) {
    Storage::fake('public');
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)->get(route('profile.edit'))
        ->assertSuccessful()
        ->assertDontSee('name="foto_profil"', false);

    $response = $this->actingAs($user)->patch(route('profile.update'), profilePhotoPayload([
        'email' => $user->email,
        'foto_profil' => UploadedFile::fake()->image('orphan.jpg', 24, 16),
    ]));

    expect($response->status())->toBe(302)
        ->and($response->headers->get('Location'))->toBe(route('profile.edit'));
    $response->assertSessionHasErrors('foto_profil');
    expect(Anggota::count())->toBe(0)
        ->and(Storage::disk('public')->allFiles('foto_profil'))->toBeEmpty();
})->with(['admin', 'instruktur']);

test('profile photo conversion failure leaves no output file', function () {
    Storage::fake('public');
    $temporaryPath = tempnam(sys_get_temp_dir(), 'invalid-profile-photo-');
    file_put_contents($temporaryPath, 'not an image');
    $file = new UploadedFile($temporaryPath, 'invalid.jpg', 'image/jpeg', null, true);

    try {
        $exception = null;

        try {
            app(ProfilePhoto::class)->store($file);
        } catch (ValidationException $caught) {
            $exception = $caught;
        }

        expect($exception)->toBeInstanceOf(ValidationException::class)
            ->and($exception->errors())->toHaveKey('foto_profil');
        expect(Storage::disk('public')->allFiles('foto_profil'))->toBeEmpty();
    } finally {
        unlink($temporaryPath);
    }
});

test('profile photo write failure returns a validation error without an output file', function () {
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('put')->once()->andReturnFalse();
    $disk->shouldReceive('exists')->once()->andReturnFalse();
    Storage::shouldReceive('disk')->once()->with('public')->andReturn($disk);

    try {
        app(ProfilePhoto::class)->store(UploadedFile::fake()->image('write-failure.jpg', 24, 16));
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey('foto_profil');

        return;
    }

    expect(false)->toBeTrue('Expected a validation exception when WebP storage fails.');
});

test('profile conversion failure keeps the old photo and profile data', function () {
    Storage::fake('public');
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create([
        'user_id' => $user->id,
        'foto_profil' => 'foto_profil/old.jpg',
        'nama_lengkap' => 'Nama Lama',
    ]);
    Storage::disk('public')->put($anggota->foto_profil, 'legacy photo');

    $converter = Mockery::mock(ProfilePhoto::class);
    $converter->shouldReceive('store')
        ->once()
        ->andThrow(ValidationException::withMessages([
            'foto_profil' => 'Foto profil gagal diproses. Silakan coba file lain.',
        ]));
    $this->app->instance(ProfilePhoto::class, $converter);

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), profilePhotoPayload([
            'email' => $user->email,
            'nama_lengkap' => 'Nama Baru',
            'foto_profil' => UploadedFile::fake()->image('new.jpg', 24, 16),
        ]));

    expect($response->status())->toBe(302)
        ->and($response->headers->get('Location'))->toBe(route('profile.edit'));
    $response->assertSessionHasErrors('foto_profil');
    expect($anggota->refresh()->foto_profil)->toBe('foto_profil/old.jpg')
        ->and($anggota->nama_lengkap)->toBe('Nama Lama');
    Storage::disk('public')->assertExists('foto_profil/old.jpg');
    expect(Storage::disk('public')->allFiles('foto_profil'))->toBe(['foto_profil/old.jpg']);
});

test('database failure cleans up the new photo and preserves the old photo', function () {
    Storage::fake('public');
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create([
        'user_id' => $user->id,
        'foto_profil' => 'foto_profil/old.jpg',
    ]);
    Storage::disk('public')->put($anggota->foto_profil, 'legacy photo');
    $event = 'eloquent.updating: '.Anggota::class;
    Event::listen($event, fn () => throw new RuntimeException('forced profile update failure'));

    try {
        expect(fn () => $this->withoutExceptionHandling()
            ->actingAs($user)
            ->patch(route('profile.update'), profilePhotoPayload([
                'email' => $user->email,
                'foto_profil' => UploadedFile::fake()->image('new.jpg', 24, 16),
            ])))->toThrow(RuntimeException::class, 'forced profile update failure');
    } finally {
        Event::forget($event);
    }

    expect($anggota->refresh()->foto_profil)->toBe('foto_profil/old.jpg');
    Storage::disk('public')->assertExists('foto_profil/old.jpg');
    expect(Storage::disk('public')->allFiles('foto_profil'))->toBe(['foto_profil/old.jpg']);
});

test('profile photo validation rejects unsupported formats and oversized images', function (UploadedFile $file) {
    Storage::fake('public');
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), profilePhotoPayload([
            'email' => $user->email,
            'foto_profil' => $file,
        ]));

    expect($response->status())->toBe(302)
        ->and($response->headers->get('Location'))->toBe(route('profile.edit'));
    $response->assertSessionHasErrors('foto_profil');
    expect(Storage::disk('public')->allFiles('foto_profil'))->toBeEmpty();
})->with([
    'webp input' => fn () => UploadedFile::fake()->image('photo.webp', 24, 16),
    'gif input' => fn () => UploadedFile::fake()->image('photo.gif', 24, 16),
    'oversized input' => fn () => UploadedFile::fake()->image('photo.jpg', 24, 16)->size(2049),
    'too wide input' => fn () => UploadedFile::fake()->image('photo.jpg', 2049, 1),
    'too tall input' => fn () => UploadedFile::fake()->image('photo.jpg', 1, 2049),
]);

test('profile photo validation rejects bytes with a false client mime type', function () {
    Storage::fake('public');
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);
    $temporaryPath = tempnam(sys_get_temp_dir(), 'fake-profile-photo-');
    file_put_contents($temporaryPath, 'not an image');
    $file = new UploadedFile($temporaryPath, 'fake.jpg', 'image/jpeg', null, true);

    try {
        $response = $this->actingAs($user)
            ->from(route('profile.edit'))
            ->patch(route('profile.update'), profilePhotoPayload([
                'email' => $user->email,
                'foto_profil' => $file,
            ]));

        expect($response->status())->toBe(302)
            ->and($response->headers->get('Location'))->toBe(route('profile.edit'));
        $response->assertSessionHasErrors('foto_profil');
        expect(Storage::disk('public')->allFiles('foto_profil'))->toBeEmpty();
    } finally {
        unlink($temporaryPath);
    }
});

test('webp profile photos render in ekta preview and pdf', function () {
    Storage::fake('public');
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);

    $path = app(ProfilePhoto::class)->store(UploadedFile::fake()->image('ekta-source.jpg', 24, 16));
    $anggota->update(['foto_profil' => $path]);

    $preview = $this->actingAs($user)->get(route('kader.ekta'));
    $preview->assertSuccessful()
        ->assertSee('data:image/webp;base64,', false)
        ->assertDontSee('data-testid="ekta-photo-fallback"', false);

    $pdf = $this->actingAs($user)->get(route('kader.ekta.download'));
    $pdf->assertSuccessful();
    expect($pdf->getContent())->toStartWith('%PDF');
});
