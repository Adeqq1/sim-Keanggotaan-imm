<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow('2026-06-24 12:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

test('admin can view admin dashboard and see chart data structure with correct data indexes', function () {
    $admin = User::factory()->admin()->create();

    // Create some dummy data in various months to test aggregation
    // Month 1: current month (June 2026)
    $anggotaCurrent = Anggota::factory()->create(['created_at' => Carbon::now()]);
    $kegiatanCurrent = Kegiatan::factory()->create(['tanggal_waktu' => Carbon::now()]);
    Presensi::factory()->hadir()->create([
        'kegiatan_id' => $kegiatanCurrent->id,
        'anggota_id' => $anggotaCurrent->id,
        'waktu_hadir' => Carbon::now(),
    ]);

    // Month 2: 5 months ago (January 2026)
    $anggotaPast = Anggota::factory()->create(['created_at' => Carbon::now()->subMonths(5)]);
    $kegiatanPast = Kegiatan::factory()->create(['tanggal_waktu' => Carbon::now()->subMonths(5)]);
    Presensi::factory()->hadir()->create([
        'kegiatan_id' => $kegiatanPast->id,
        'anggota_id' => $anggotaPast->id,
        'waktu_hadir' => Carbon::now()->subMonths(5),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('chartData');

    $chartData = $response->viewData('chartData');

    // Assert structures
    expect($chartData)->toBeArray()
        ->toHaveKeys(['anggota_per_bulan', 'kegiatan_per_bulan', 'kehadiran_per_bulan']);

    // Assert data counts
    expect($chartData['anggota_per_bulan']['labels'])->toHaveCount(12);
    expect($chartData['anggota_per_bulan']['data'])->toHaveCount(12);
    expect($chartData['kegiatan_per_bulan']['labels'])->toHaveCount(12);
    expect($chartData['kegiatan_per_bulan']['data'])->toHaveCount(12);
    expect($chartData['kehadiran_per_bulan']['labels'])->toHaveCount(12);
    expect($chartData['kehadiran_per_bulan']['data'])->toHaveCount(12);

    // Verify correct month labels are present
    $expectedLabel = Carbon::now()->format('M Y');
    expect(end($chartData['anggota_per_bulan']['labels']))->toBe($expectedLabel);

    // Verify value counts mapping
    // Current month (June 2026) should have 1 anggota, 1 kegiatan, 1 kehadiran
    expect(end($chartData['anggota_per_bulan']['data']))->toBe(1);
    expect(end($chartData['kegiatan_per_bulan']['data']))->toBe(1);
    expect(end($chartData['kehadiran_per_bulan']['data']))->toBe(1);

    // Verify historical month (5 months ago: Jan 2026) mapping
    $pastLabel = Carbon::now()->subMonths(5)->format('M Y');
    $pastIndex = array_search($pastLabel, $chartData['anggota_per_bulan']['labels']);

    expect($pastIndex)->not->toBeFalse();
    expect($chartData['anggota_per_bulan']['data'][$pastIndex])->toBe(1);
    expect($chartData['kegiatan_per_bulan']['data'][$pastIndex])->toBe(1);
    expect($chartData['kehadiran_per_bulan']['data'][$pastIndex])->toBe(1);
});

test('non-admin is forbidden to access admin dashboard', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->get(route('admin.dashboard'));

    $response->assertStatus(403);
});
