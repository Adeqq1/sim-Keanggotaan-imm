<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;

test('admin can view admin dashboard and see chart data structure', function () {
    $admin = User::factory()->admin()->create();

    // Create some dummy data in various months to test aggregation
    // Month 1: current month
    $anggotaCurrent = Anggota::factory()->create(['created_at' => now()]);
    $kegiatanCurrent = Kegiatan::factory()->create(['tanggal_waktu' => now()]);
    Presensi::create([
        'kegiatan_id' => $kegiatanCurrent->id,
        'anggota_id' => $anggotaCurrent->id,
        'status_kehadiran' => 'hadir',
        'waktu_hadir' => now(),
    ]);

    // Month 2: 5 months ago
    $anggotaPast = Anggota::factory()->create(['created_at' => now()->subMonths(5)]);
    $kegiatanPast = Kegiatan::factory()->create(['tanggal_waktu' => now()->subMonths(5)]);
    Presensi::create([
        'kegiatan_id' => $kegiatanPast->id,
        'anggota_id' => $anggotaPast->id,
        'status_kehadiran' => 'hadir',
        'waktu_hadir' => now()->subMonths(5),
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
    $expectedLabel = now()->format('M Y');
    expect(end($chartData['anggota_per_bulan']['labels']))->toBe($expectedLabel);

    // Verify value counts mapping
    // Current month should have 1 anggota, 1 kegiatan, 1 kehadiran
    expect(end($chartData['anggota_per_bulan']['data']))->toBe(1);
    expect(end($chartData['kegiatan_per_bulan']['data']))->toBe(1);
    expect(end($chartData['kehadiran_per_bulan']['data']))->toBe(1);
});

test('non-admin is forbidden to access admin dashboard', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->get(route('admin.dashboard'));

    $response->assertStatus(403);
});
