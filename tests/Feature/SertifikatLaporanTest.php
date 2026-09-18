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

test('certificate create form renders accessible member selection', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->withDefaultSession()->create([
        'jenis_pelaksanaan' => Kegiatan::SATU_SESI,
        'minimum_sesi_terverifikasi' => 1,
    ]);
    $anggota = Anggota::factory()->create([
        'nama_lengkap' => 'Ahmad Dahlan',
        'nia' => 'IMM-001',
    ]);
    Presensi::factory()->terverifikasi()->create([
        'kegiatan_id' => $kegiatan->id,
        'sesi_kegiatan_id' => $kegiatan->sesiKegiatans()->first()->id,
        'anggota_id' => $anggota->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.sertifikat.create', ['kegiatan_id' => $kegiatan->id]))
        ->assertSuccessful()
        ->assertSee('id="kegiatan_id"', false)
        ->assertSee('name="kegiatan_id"', false)
        ->assertSee('method="GET"', false)
        ->assertSee('onchange="this.form.submit()"', false)
        ->assertSee('name="anggota_ids[]"', false)
        ->assertSee('id="anggota-'.$anggota->id.'"', false)
        ->assertSee('for="anggota-'.$anggota->id.'"', false)
        ->assertSee('aria-describedby="anggota-help"', false)
        ->assertSeeText('Ahmad Dahlan')
        ->assertSeeText('NIA IMM-001');
});

test('certificate create form restores selected activity and members', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->withDefaultSession()->create([
        'jenis_pelaksanaan' => Kegiatan::SATU_SESI,
        'minimum_sesi_terverifikasi' => 1,
    ]);
    $anggota = Anggota::factory()->create();
    Presensi::factory()->terverifikasi()->create([
        'kegiatan_id' => $kegiatan->id,
        'sesi_kegiatan_id' => $kegiatan->sesiKegiatans()->first()->id,
        'anggota_id' => $anggota->id,
    ]);

    $this->actingAs($admin)
        ->withSession([
            '_old_input' => [
                'kegiatan_id' => (string) $kegiatan->id,
                'anggota_ids' => [(string) $anggota->id],
            ],
        ])
        ->get(route('admin.sertifikat.create'))
        ->assertSuccessful()
        ->assertSee('value="'.$kegiatan->id.'" selected', false)
        ->assertSee('value="'.$anggota->id.'" checked', false);
});

test('certificate create form only lists eligible kader without an existing certificate', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->withDefaultSession()->create([
        'jenis_pelaksanaan' => Kegiatan::SATU_SESI,
        'minimum_sesi_terverifikasi' => 1,
    ]);
    $eligible = Anggota::factory()->create(['nama_lengkap' => 'Kader Layak Sertifikat']);
    $noAttendance = Anggota::factory()->create(['nama_lengkap' => 'Kader Belum Hadir']);
    $nonKader = Anggota::factory()->create([
        'nama_lengkap' => 'Instruktur Bukan Kader',
        'user_id' => User::factory()->instruktur(),
    ]);
    $alreadyIssued = Anggota::factory()->create(['nama_lengkap' => 'Kader Sudah Terbit']);
    $sesi = $kegiatan->sesiKegiatans()->first();

    Presensi::factory()->terverifikasi()->create([
        'kegiatan_id' => $kegiatan->id,
        'sesi_kegiatan_id' => $sesi->id,
        'anggota_id' => $eligible->id,
    ]);
    Presensi::factory()->terverifikasi()->create([
        'kegiatan_id' => $kegiatan->id,
        'sesi_kegiatan_id' => $sesi->id,
        'anggota_id' => $nonKader->id,
    ]);
    Presensi::factory()->terverifikasi()->create([
        'kegiatan_id' => $kegiatan->id,
        'sesi_kegiatan_id' => $sesi->id,
        'anggota_id' => $alreadyIssued->id,
    ]);
    Sertifikat::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $alreadyIssued->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.sertifikat.create', ['kegiatan_id' => $kegiatan->id]))
        ->assertSuccessful()
        ->assertSeeText('Kader Layak Sertifikat')
        ->assertDontSeeText('Kader Belum Hadir')
        ->assertDontSeeText('Instruktur Bukan Kader')
        ->assertDontSeeText('Kader Sudah Terbit')
        ->assertSeeText('1 anggota memenuhi syarat');
});

test('admin certificate list renders a labeled PDF download action', function () {
    $admin = User::factory()->admin()->create();
    $anggota = Anggota::factory()->create(['nama_lengkap' => 'Anggota Sertifikat']);
    $kegiatan = Kegiatan::factory()->create(['nama_kegiatan' => 'Pelatihan Kader']);
    $sertifikat = Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.sertifikat.index'))
        ->assertSuccessful()
        ->assertSeeText('Unduh PDF')
        ->assertSee('certificate-card', false)
        ->assertSee('certificate-card__download', false)
        ->assertSee(route('admin.sertifikat.download', $sertifikat), false)
        ->assertSee('aria-label="Unduh sertifikat Anggota Sertifikat dalam format PDF"', false);
});

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

    $response->assertRedirectContains(route('admin.sertifikat.index').'?generation=');

    $this->assertDatabaseHas('sertifikat', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota1->id,
    ]);

    $this->assertDatabaseHas('sertifikat', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota2->id,
    ]);
});

test('admin can read owned certificate generation batch status', function () {
    Queue::fake();
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota = Anggota::factory()->create();
    $sesi = SesiKegiatan::factory()->for($kegiatan)->create();
    Presensi::factory()->terverifikasi()->create(['kegiatan_id' => $kegiatan->id, 'sesi_kegiatan_id' => $sesi->id, 'anggota_id' => $anggota->id]);

    $response = $this->actingAs($admin)->post(route('admin.sertifikat.generate'), [
        'kegiatan_id' => $kegiatan->id,
        'anggota_ids' => [$anggota->id],
    ]);
    $batchId = last(explode('generation=', $response->headers->get('Location')));

    $this->actingAs($admin)
        ->get(route('admin.sertifikat.generation.status', $batchId))
        ->assertOk()
        ->assertJsonPath('total', 1)
        ->assertJsonPath('finished', false);

    $otherAdmin = User::factory()->admin()->create();
    $this->actingAs($otherAdmin)->get(route('admin.sertifikat.generation.status', $batchId))->assertNotFound();
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

    $response->assertRedirectContains(route('admin.sertifikat.index').'?generation=');
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
