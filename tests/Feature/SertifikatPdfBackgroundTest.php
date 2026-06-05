<?php

use App\Http\Controllers\SertifikatController;
use App\Models\Anggota;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
    if (File::exists($this->backupPath)) {
        if (File::exists($this->bgPath)) {
            File::delete($this->bgPath);
        }

        File::move($this->backupPath, $this->bgPath);
    } elseif (File::exists($this->bgPath)) {
        File::delete($this->bgPath);
    }

    if (Storage::disk('local')->exists('sertifikat_settings.json')) {
        Storage::disk('local')->delete('sertifikat_settings.json');
    }

});

test('certificate pdf view uses uploaded background when enabled', function () {
    Storage::disk('local')->put('sertifikat_settings.json', json_encode([
        'use_background' => true,
    ]));

    File::put($this->bgPath, base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAQEBUQEBAVFhUVFRUVFRUVFRUVFRUVFRUXFhUVFRUYHSggGBolHRUVITEhJSkrLi4uFx8zODMsNygtLisBCgoKDg0OGhAQGi0lHyUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAAEAAgMBIgACEQEDEQH/xAAXAAADAQAAAAAAAAAAAAAAAAAAAQMC/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEAMQAAAB6gD/xAAVEAEBAAAAAAAAAAAAAAAAAAABAP/aAAgBAQABBQJf/8QAFBEBAAAAAAAAAAAAAAAAAAAAEP/aAAgBAwEBPwF//8QAFBEBAAAAAAAAAAAAAAAAAAAAEP/aAAgBAgEBPwF//8QAFBABAAAAAAAAAAAAAAAAAAAAEP/aAAgBAQAGPwJf/8QAFhABAQEAAAAAAAAAAAAAAAAAABEh/9oACAEBAAE/IYaf/9k='));

    $kegiatan = Kegiatan::factory()->create();
    $anggota = Anggota::factory()->create();

    $html = view('pdf.sertifikat', [
        'kegiatan' => $kegiatan,
        'anggota' => $anggota,
        'nomorSertifikat' => 'CERT-TEST',
        'role' => 'Kader',
        'instruktur' => 'Instruktur Uji',
        'useBackground' => SertifikatController::useBackground(),
        'bgPath' => 'images/sertificate-asset/bg-sertificate.jpg',
        'bgExists' => true,
    ])->render();

    expect($html)->toContain('bg-sertificate.jpg')
        ->toContain('class="page-background"')
        ->toContain('rgba(255, 255, 255, 0.75)')
        ->not->toContain('<div class="decor-left-bar"></div>');
});

test('certificate pdf view keeps default ornaments when background is disabled', function () {
    Storage::disk('local')->put('sertifikat_settings.json', json_encode([
        'use_background' => false,
    ]));

    $kegiatan = Kegiatan::factory()->create();
    $anggota = Anggota::factory()->create();

    $html = view('pdf.sertifikat', [
        'kegiatan' => $kegiatan,
        'anggota' => $anggota,
        'nomorSertifikat' => 'CERT-TEST',
        'role' => 'Kader',
        'instruktur' => 'Instruktur Uji',
        'useBackground' => SertifikatController::useBackground(),
        'bgPath' => 'images/sertificate-asset/bg-sertificate.jpg',
        'bgExists' => false,
    ])->render();

    expect($html)->toContain('decor-left-bar')
        ->not->toContain('rgba(255, 255, 255, 0.75)');
});
