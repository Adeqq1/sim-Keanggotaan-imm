<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\SesiKegiatan;
use App\Models\User;
use App\Services\VerifiedAttendance;

test('verified attendance counts only verified present sessions for the target activity', function () {
    $kegiatan = Kegiatan::factory()->create([
        'jenis_pelaksanaan' => Kegiatan::MULTI_SESI,
        'minimum_sesi_terverifikasi' => 3,
    ]);
    $anggota = Anggota::factory()->create();
    $instruktur = User::factory()->instruktur()->create();
    $sesi2 = SesiKegiatan::factory()->for($kegiatan)->create(['urutan' => 2, 'nama_sesi' => 'Sesi 2']);
    $sesi3 = SesiKegiatan::factory()->for($kegiatan)->create(['urutan' => 3, 'nama_sesi' => 'Sesi 3']);

    Presensi::factory()->terverifikasi()->create(['kegiatan_id' => $kegiatan->id, 'sesi_kegiatan_id' => $kegiatan->sesiKegiatans()->first()->id, 'anggota_id' => $anggota->id, 'pemeriksa_id' => $instruktur->id]);
    Presensi::factory()->terverifikasi()->create(['kegiatan_id' => $kegiatan->id, 'sesi_kegiatan_id' => $sesi2->id, 'anggota_id' => $anggota->id, 'pemeriksa_id' => $instruktur->id]);
    Presensi::factory()->create(['kegiatan_id' => $kegiatan->id, 'sesi_kegiatan_id' => $sesi3->id, 'anggota_id' => $anggota->id, 'status_kehadiran' => 'hadir']);

    expect(app(VerifiedAttendance::class)->countFor($kegiatan, $anggota))->toBe(2)
        ->and(app(VerifiedAttendance::class)->meetsRequirement($kegiatan, $anggota))->toBeFalse();
});

test('instruktur can record and verify attendance per session', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->create();
    $sesi = $kegiatan->sesiKegiatans()->first();
    $anggota = Anggota::factory()->create();

    $this->actingAs($instruktur)
        ->post(route('admin.presensi.store', [$kegiatan, $sesi]), [
            'presensi' => [['anggota_id' => $anggota->id, 'status_kehadiran' => 'hadir']],
        ])->assertRedirect();

    $presensi = Presensi::where('sesi_kegiatan_id', $sesi->id)->firstOrFail();
    expect($presensi->status_verifikasi)->toBe('pending');

    $this->actingAs($instruktur)
        ->patch(route('admin.presensi.verifikasi.update', [$kegiatan, $sesi, $presensi]), ['status_verifikasi' => 'terverifikasi'])
        ->assertRedirect();

    $presensi->refresh();
    expect($presensi->pemeriksa_id)->toBe($instruktur->id)
        ->and($presensi->diperiksa_pada)->not->toBeNull();
});

test('admin can read but cannot record or verify attendance', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $sesi = $kegiatan->sesiKegiatans()->first();
    $anggota = Anggota::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.presensi.store', [$kegiatan, $sesi]), ['presensi' => [['anggota_id' => $anggota->id, 'status_kehadiran' => 'hadir']]])
        ->assertForbidden();
});
