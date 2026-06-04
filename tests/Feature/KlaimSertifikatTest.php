<?php

use App\Jobs\GenerateCertificateJob;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

// 1x1 Transparent GIF representation to avoid GD dependency in tests
$gifBytes = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

test('kader can upload proof of attendance to claim certificate', function () use ($gifBytes) {
    Storage::fake('public');

    $anggota = Anggota::factory()->create();
    $kader = $anggota->user;
    $kegiatan = Kegiatan::factory()->create();

    // Create initial presensi as alfa (default)
    $presensi = Presensi::create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'alfa',
    ]);

    $file = UploadedFile::fake()->createWithContent('attendance_proof.jpg', $gifBytes);

    $response = $this->actingAs($kader)
        ->post(route('kader.sertifikat.klaim', $presensi), [
            'bukti_kehadiran' => $file,
        ]);

    $response->assertRedirect();

    // Verify changes in database
    $presensi->refresh();
    expect($presensi->status_klaim)->toBe('pending');
    expect($presensi->bukti_kehadiran)->not->toBeNull();

    // Verify file exists on public disk
    Storage::disk('public')->assertExists($presensi->bukti_kehadiran);
});

test('invalid file upload for claim certificate is rejected', function () {
    Storage::fake('public');

    $anggota = Anggota::factory()->create();
    $kader = $anggota->user;
    $kegiatan = Kegiatan::factory()->create();

    $presensi = Presensi::create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'alfa',
    ]);

    // Test non-image file
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->actingAs($kader)
        ->post(route('kader.sertifikat.klaim', $presensi), [
            'bukti_kehadiran' => $file,
        ]);

    $response->assertSessionHasErrors('bukti_kehadiran');
    $presensi->refresh();
    expect($presensi->status_klaim)->toBeNull();

    // Test image over 2MB (we use simple create to bypass GD size method)
    $largeFile = UploadedFile::fake()->create('large_proof.jpg', 3000, 'image/jpeg');

    $response = $this->actingAs($kader)
        ->post(route('kader.sertifikat.klaim', $presensi), [
            'bukti_kehadiran' => $largeFile,
        ]);

    $response->assertSessionHasErrors('bukti_kehadiran');
});

test('only admin and instruktur can view verifikasi page and actions', function () {
    $anggota = Anggota::factory()->create();
    $kader = $anggota->user;

    $admin = User::factory()->admin()->create();
    $instruktur = User::factory()->instruktur()->create();

    // Kader cannot access
    $this->actingAs($kader)->get(route('admin.sertifikat.verifikasi.index'))->assertStatus(403);

    // Admin can access
    $this->actingAs($admin)->get(route('admin.sertifikat.verifikasi.index'))->assertStatus(200);

    // Instruktur can access
    $this->actingAs($instruktur)->get(route('admin.sertifikat.verifikasi.index'))->assertStatus(200);
});

test('admin or instruktur can approve claim which updates attendance and generates certificate', function () {
    Storage::fake('public');

    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();

    $presensi = Presensi::create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'alfa',
        'bukti_kehadiran' => 'bukti_kehadiran/test.jpg',
        'status_klaim' => 'pending',
    ]);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.sertifikat.verifikasi.setuju', $presensi));

    $response->assertRedirect(route('admin.sertifikat.verifikasi.index'));

    $presensi->refresh();
    expect($presensi->status_klaim)->toBe('disetujui');
    expect($presensi->status_kehadiran)->toBe('hadir');
    expect($presensi->waktu_hadir)->not->toBeNull();

    // Verify certificate generation
    $this->assertDatabaseHas('sertifikat', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
    ]);

    $sertifikat = Sertifikat::where('kegiatan_id', $kegiatan->id)->where('anggota_id', $anggota->id)->first();
    Storage::disk('public')->assertExists($sertifikat->file_sertifikat);
});

test('admin or instruktur can reject claim which deletes file and resets status', function () use ($gifBytes) {
    Storage::fake('public');

    // Store a fake proof file
    $uploadedFile = UploadedFile::fake()->createWithContent('proof.jpg', $gifBytes);
    $path = Storage::disk('public')->putFileAs('bukti_kehadiran', $uploadedFile, 'proof.jpg');

    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();

    $presensi = Presensi::create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'alfa',
        'bukti_kehadiran' => $path,
        'status_klaim' => 'pending',
    ]);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.sertifikat.verifikasi.tolak', $presensi));

    $response->assertRedirect(route('admin.sertifikat.verifikasi.index'));

    $presensi->refresh();
    expect($presensi->status_klaim)->toBe('ditolak');
    expect($presensi->bukti_kehadiran)->toBeNull();

    // Verify proof file was deleted from storage
    Storage::disk('public')->assertMissing($path);
});

test('kader cannot download other users certificate but can download theirs', function () {
    Storage::fake('public');
    Storage::disk('public')->put('sertifikat/test.pdf', 'dummy content');

    $anggota = Anggota::factory()->create();
    $kader = $anggota->user;
    $kegiatan = Kegiatan::factory()->create();

    $sertifikat = Sertifikat::create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'nomor_sertifikat' => 'CERT-TEST-123',
        'file_sertifikat' => 'sertifikat/test.pdf',
    ]);

    $nonOwner = Anggota::factory()->create()->user;
    $this->actingAs($nonOwner)->get(route('kader.sertifikat.download', $sertifikat))->assertStatus(403);

    $response = $this->actingAs($kader)->get(route('kader.sertifikat.download', $sertifikat));
    $response->assertSuccessful();
});

test('admin or instruktur approval dispatches GenerateCertificateJob', function () {
    Queue::fake();

    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();

    $presensi = Presensi::create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'alfa',
        'bukti_kehadiran' => 'bukti_kehadiran/test.jpg',
        'status_klaim' => 'pending',
    ]);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.sertifikat.verifikasi.setuju', $presensi));

    $response->assertRedirect(route('admin.sertifikat.verifikasi.index'));

    Queue::assertPushed(GenerateCertificateJob::class, function ($job) use ($presensi) {
        return $job->presensi->id === $presensi->id;
    });
});
