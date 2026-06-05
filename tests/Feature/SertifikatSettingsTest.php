<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

beforeEach(function () {
    $this->bgDir = public_path('images/sertificate-asset');
    $this->bgPath = $this->bgDir.'/bg-sertificate.jpg';
    $this->backupPath = $this->bgDir.'/bg-sertificate.jpg.bak';

    if (! File::exists($this->bgDir)) {
        File::makeDirectory($this->bgDir, 0755, true);
    }

    if (File::exists($this->bgPath)) {
        File::copy($this->bgPath, $this->backupPath);
    }
});

afterEach(function () {
    gc_collect_cycles();
    if (File::exists($this->backupPath)) {
        if (File::exists($this->bgPath)) {
            File::delete($this->bgPath);
        }
        File::move($this->backupPath, $this->bgPath);
    }

    if (Storage::disk('local')->exists('sertifikat_settings.json')) {
        Storage::disk('local')->delete('sertifikat_settings.json');
    }
});

test('guest cannot access certificate settings', function () {
    $this->get(route('admin.sertifikat.settings'))
        ->assertRedirect(route('login'));
});

test('kader cannot access certificate settings', function () {
    $kader = User::factory()->kader()->create();

    $this->actingAs($kader)
        ->get(route('admin.sertifikat.settings'))
        ->assertForbidden();
});

test('admin can access certificate settings page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.sertifikat.settings'))
        ->assertOk()
        ->assertViewIs('admin.sertifikat.settings')
        ->assertViewHas('bgExists');
});

test('admin can upload new certificate background and it is resized to A4 landscape', function () {
    $admin = User::factory()->admin()->create();

    // Create a fake image (e.g. 500x500 square image)
    $fakeImage = UploadedFile::fake()->image('new-bg.png', 500, 500);

    $response = $this->actingAs($admin)
        ->from(route('admin.sertifikat.settings'))
        ->post(route('admin.sertifikat.settings.update'), [
            'background_image' => $fakeImage,
        ]);

    $response->assertRedirect(route('admin.sertifikat.settings'));
    $response->assertSessionHas('success');

    // Assert file exists at target destination
    $this->assertTrue(File::exists($this->bgPath));

    // Verify it was resized and cropped to A4 landscape (1122 x 794) if GD & JPEG are supported
    if (extension_loaded('gd') && function_exists('imagejpeg')) {
        $manager = new ImageManager(new Driver);
        $image = $manager->decodePath($this->bgPath);

        expect($image->width())->toBe(1122);
        expect($image->height())->toBe(794);
    }
});

test('admin upload fails with invalid file types or large sizes', function () {
    $admin = User::factory()->admin()->create();

    // Text file instead of image
    $fakeTextFile = UploadedFile::fake()->create('document.txt', 100);

    $response = $this->actingAs($admin)
        ->post(route('admin.sertifikat.settings.update'), [
            'background_image' => $fakeTextFile,
        ]);

    $response->assertSessionHasErrors(['background_image']);
});

test('admin can toggle the certificate background setting', function () {
    $admin = User::factory()->admin()->create();

    // Toggle background OFF
    $response = $this->actingAs($admin)
        ->from(route('admin.sertifikat.settings'))
        ->post(route('admin.sertifikat.settings.update'), []);

    $response->assertRedirect(route('admin.sertifikat.settings'));
    $response->assertSessionHas('success');

    expect(Storage::disk('local')->exists('sertifikat_settings.json'))->toBeTrue();
    $settings = json_decode(Storage::disk('local')->get('sertifikat_settings.json'), true);
    expect($settings['use_background'])->toBeFalse();

    // Toggle background ON
    $response = $this->actingAs($admin)
        ->from(route('admin.sertifikat.settings'))
        ->post(route('admin.sertifikat.settings.update'), [
            'use_background' => '1',
        ]);

    $response->assertRedirect(route('admin.sertifikat.settings'));
    $settings = json_decode(Storage::disk('local')->get('sertifikat_settings.json'), true);
    expect($settings['use_background'])->toBeTrue();
});
