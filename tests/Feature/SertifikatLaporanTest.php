<?php

use App\Jobs\GenerateCertificateJob;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\PenilaianKegiatan;
use App\Models\Sertifikat;
use App\Models\SesiKegiatan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('admin can generate sertifikat for selected kader', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota1 = Anggota::factory()->create();
    $anggota2 = Anggota::factory()->create();
    $sesi = SesiKegiatan::factory()->for($kegiatan)->create();
    Presensi::factory()->terverifikasi()->create(['kegiatan_id' => $kegiatan->id, 'sesi_kegiatan_id' => $sesi->id, 'anggota_id' => $anggota1->id]);
    Presensi::factory()->terverifikasi()->create(['kegiatan_id' => $kegiatan->id, 'sesi_kegiatan_id' => $sesi->id, 'anggota_id' => $anggota2->id]);

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

test('admin bulk certificate generation dispatches GenerateCertificateJob', function () {
    Queue::fake();
    Storage::disk('public')->deleteDirectory('sertifikat');

    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota1 = Anggota::factory()->create();
    $anggota2 = Anggota::factory()->create();
    $sesi = SesiKegiatan::factory()->for($kegiatan)->create();
    Presensi::factory()->terverifikasi()->create(['kegiatan_id' => $kegiatan->id, 'sesi_kegiatan_id' => $sesi->id, 'anggota_id' => $anggota1->id]);
    Presensi::factory()->terverifikasi()->create(['kegiatan_id' => $kegiatan->id, 'sesi_kegiatan_id' => $sesi->id, 'anggota_id' => $anggota2->id]);

    $response = $this->actingAs($admin)
        ->post(route('admin.sertifikat.generate'), [
            'kegiatan_id' => $kegiatan->id,
            'anggota_ids' => [$anggota1->id, $anggota2->id],
        ]);

    $response->assertRedirect(route('admin.sertifikat.index'));
    $response->assertSessionHas('success', 'Sertifikat sedang dibuat di latar belakang.');

    Queue::assertPushed(GenerateCertificateJob::class, 2);
    Queue::assertPushed(GenerateCertificateJob::class, function ($job) use ($kegiatan, $anggota1) {
        return $job->kegiatan->id === $kegiatan->id && $job->anggota->id === $anggota1->id;
    });
    Queue::assertPushed(GenerateCertificateJob::class, function ($job) use ($kegiatan, $anggota2) {
        return $job->kegiatan->id === $kegiatan->id && $job->anggota->id === $anggota2->id;
    });
});

test('certificate snapshots and PDF page count follow the activity policy', function () {
    Storage::fake('public');
    Storage::fake('local');
    Storage::disk('local')->put('sertifikat_settings.json', json_encode(['use_background' => false]));

    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create([
        'jenis_pelaksanaan' => Kegiatan::MULTI_SESI,
        'minimum_sesi_terverifikasi' => 3,
    ]);

    foreach (range(1, 3) as $urutan) {
        $sesi = SesiKegiatan::factory()->for($kegiatan)->create(['urutan' => $urutan]);
        Presensi::factory()->terverifikasi()->create([
            'kegiatan_id' => $kegiatan->id,
            'sesi_kegiatan_id' => $sesi->id,
            'anggota_id' => $anggota->id,
        ]);
    }
    PenilaianKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'nilai' => 'B',
    ]);

    $sertifikat = \App\Http\Controllers\SertifikatController::generateCertificateFile($kegiatan, $anggota);
    $pdf = Pdf::loadView('pdf.sertifikat', [
        'kegiatan' => $kegiatan,
        'anggota' => $anggota,
        'nomorSertifikat' => $sertifikat->nomor_sertifikat,
        'role' => 'Kader',
        'instruktur' => 'Instruktur',
        'issuedAt' => $sertifikat->created_at,
        'useBackground' => false,
        'tipe_sertifikat' => $sertifikat->tipe_sertifikat,
        'nilai_snapshot' => $sertifikat->nilai_snapshot,
        'label_nilai' => PenilaianKegiatan::NILAI_LABELS[$sertifikat->nilai_snapshot],
    ]);
    $pdf->render();

    expect($sertifikat->tipe_sertifikat)->toBe(Sertifikat::MULTI_SESI)
        ->and($sertifikat->nilai_snapshot)->toBe('B')
        ->and($pdf->getDomPDF()->getCanvas()->get_page_count())->toBe(2);
    Storage::disk('public')->assertExists($sertifikat->file_sertifikat);
});

test('admin rejects the whole certificate batch before dispatching any job', function () {
    Queue::fake();

    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $eligible = Anggota::factory()->create();
    $ineligible = Anggota::factory()->inactive()->create();
    $sesi = SesiKegiatan::factory()->for($kegiatan)->create();
    Presensi::factory()->terverifikasi()->create([
        'kegiatan_id' => $kegiatan->id,
        'sesi_kegiatan_id' => $sesi->id,
        'anggota_id' => $eligible->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.sertifikat.generate'), [
            'kegiatan_id' => $kegiatan->id,
            'anggota_ids' => [$eligible->id, $ineligible->id],
        ])
        ->assertSessionHasErrors('anggota_ids');

    Queue::assertNothingPushed();
    expect(\App\Models\Sertifikat::count())->toBe(0);
    Storage::disk('public')->assertDirectoryEmpty('sertifikat');
});

test('admin laporan page renders semantic form without alert', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.laporan.index'));

    $content = $response->assertOk()
        ->assertSee(route('admin.laporan.exportPdf'), false)
        ->assertSee(route('admin.laporan.exportExcel'), false)
        ->assertSee('name="jenis_laporan"', false)
        ->assertSee('name="tanggal_mulai"', false)
        ->assertSee('name="tanggal_selesai"', false)
        ->assertSee('data-date-range-form', false)
        ->getContent();

    expect($content)->not->toContain('alert(');
    expect($content)->not->toContain('submitForm');
    expect($content)->not->toContain('formPdf');
    expect($content)->not->toContain('formExcel');
});
