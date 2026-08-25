<?php

use App\Jobs\GenerateCertificateJob;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('kader claim route is retired while historical attendance data remains', function () {
    $anggota = Anggota::factory()->create();
    $presensi = Presensi::create([
        'kegiatan_id' => Kegiatan::factory()->create()->id,
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'alfa',
        'bukti_kehadiran' => 'bukti_kehadiran/legacy-proof.jpg',
        'status_klaim' => 'pending',
    ]);

    expect(route('kader.sertifikat.index'))->not->toContain('klaim');
    expect($presensi->fresh()->status_klaim)->toBe('pending')
        ->and($presensi->fresh()->bukti_kehadiran)->toBe('bukti_kehadiran/legacy-proof.jpg');
});

test('legacy certificate jobs are no-op', function () {
    Queue::fake();
    $presensi = Presensi::factory()->terverifikasi()->create();

    (new GenerateCertificateJob($presensi))->handle();

    expect(Sertifikat::count())->toBe(0);
});

test('kader history no longer renders a claim form', function () {
    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();
    Presensi::factory()->terverifikasi()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);

    $this->actingAs($anggota->user)
        ->get(route('kader.riwayat.index'))
        ->assertSuccessful()
        ->assertDontSeeText('Klaim Sertifikat')
        ->assertSeeText('Sertifikat diterbitkan oleh admin');
});

test('kader owner can download an existing certificate after current attendance check', function () {
    Storage::fake('public');
    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();
    Presensi::factory()->terverifikasi()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);
    Storage::disk('public')->put('sertifikat/test.pdf', 'dummy content');
    $sertifikat = Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
        'file_sertifikat' => 'sertifikat/test.pdf',
    ]);

    $this->actingAs($anggota->user)
        ->get(route('kader.sertifikat.download', $sertifikat))
        ->assertSuccessful();
});

test('missing certificate file returns not found', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $sertifikat = Sertifikat::factory()->create(['file_sertifikat' => 'sertifikat/missing.pdf']);

    $this->actingAs($admin)
        ->get(route('admin.sertifikat.download', $sertifikat))
        ->assertNotFound();
});
