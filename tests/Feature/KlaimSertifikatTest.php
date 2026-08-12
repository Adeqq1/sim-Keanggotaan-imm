<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('legacy kader claim endpoint is unavailable and preserves claim data', function () {
    Storage::fake('public');

    $anggota = Anggota::factory()->create();
    $kader = $anggota->user;
    $kegiatan = Kegiatan::factory()->create();
    $legacyPath = 'bukti_kehadiran/legacy-proof.jpg';
    Storage::disk('public')->put($legacyPath, 'legacy proof');

    $presensi = Presensi::create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'alfa',
        'bukti_kehadiran' => $legacyPath,
        'status_klaim' => 'pending',
    ]);

    $response = $this->actingAs($kader)
        ->post('/kader/sertifikat/'.$presensi->id.'/klaim', [
            'bukti_kehadiran' => UploadedFile::fake()->create('new-proof.jpg', 100, 'image/jpeg'),
        ]);

    $response->assertNotFound();
    $presensi->refresh();
    expect($presensi->status_kehadiran)->toBe('alfa')
        ->and($presensi->status_klaim)->toBe('pending')
        ->and($presensi->bukti_kehadiran)->toBe($legacyPath);
    Storage::disk('public')->assertExists($legacyPath);
});

test('legacy certificate verification endpoints are unavailable', function (string $action) {
    $admin = User::factory()->admin()->create();
    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();
    $presensi = Presensi::create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'alfa',
        'status_klaim' => 'pending',
    ]);

    $response = $this->actingAs($admin)
        ->post('/admin/sertifikat/verifikasi/'.$presensi->id.'/'.$action);

    $response->assertNotFound();
    expect($presensi->fresh()->status_klaim)->toBe('pending')
        ->and($presensi->fresh()->status_kehadiran)->toBe('alfa')
        ->and($presensi->fresh()->waktu_hadir)->toBeNull();
})->with(['setuju', 'tolak']);

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
