<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('admin can generate sertifikat for selected kader', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota1 = Anggota::factory()->create();
    $anggota2 = Anggota::factory()->create();

    Storage::fake('public');

    $response = $this->actingAs($admin)
        ->post(route('admin.sertifikat.generate'), [
            'kegiatan_id' => $kegiatan->id,
            'anggota_ids' => [$anggota1->id, $anggota2->id],
        ]);

    $response->assertRedirect(route('admin.sertifikat.index'));

    $this->assertDatabaseHas('sertifikat', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota1->id,
    ]);

    $this->assertDatabaseHas('sertifikat', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota2->id,
    ]);
});

test('admin can export laporan pdf', function () {
    $admin = User::factory()->admin()->create();
    Kegiatan::factory()->create(['tanggal_waktu' => now()]);

    $response = $this->actingAs($admin)
        ->post(route('admin.laporan.exportPdf'), [
            'jenis_laporan' => 'kegiatan',
            'tanggal_mulai' => now()->subMonth()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
        ]);

    $response->assertSuccessful();
    expect($response->headers->get('Content-Type'))->toContain('pdf');
});

test('admin can export laporan excel', function () {
    $admin = User::factory()->admin()->create();
    Kegiatan::factory()->create(['tanggal_waktu' => now()]);

    $response = $this->actingAs($admin)
        ->post(route('admin.laporan.exportExcel'), [
            'jenis_laporan' => 'kegiatan',
            'tanggal_mulai' => now()->subMonth()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
        ]);

    $response->assertSuccessful();
});
