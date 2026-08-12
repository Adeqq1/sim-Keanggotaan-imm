<?php

use App\Jobs\GenerateCertificateJob;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

function createHadirPresensi(Anggota $anggota, int $count): Collection
{
    if ($count === 0) {
        return collect();
    }

    return collect(range(1, $count))->map(fn () => Presensi::factory()->hadir()->create([
        'anggota_id' => $anggota->id,
    ]));
}

test('invalid claim preserves historical claim data', function () {
    $anggota = Anggota::factory()->create();
    $kader = $anggota->user;
    $kegiatan = Kegiatan::factory()->create();
    $legacyPath = 'bukti_kehadiran/legacy-proof.jpg';

    Storage::fake('public');
    Storage::disk('public')->put($legacyPath, 'legacy proof');

    $presensi = Presensi::create([
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'alfa',
        'bukti_kehadiran' => $legacyPath,
        'status_klaim' => 'pending',
    ]);

    $this->actingAs($kader)
        ->post(route('kader.sertifikat.klaim', $presensi))
        ->assertForbidden();

    expect($presensi->fresh()->status_kehadiran)->toBe('alfa')
        ->and($presensi->fresh()->status_klaim)->toBe('pending')
        ->and($presensi->fresh()->bukti_kehadiran)->toBe($legacyPath);
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

    $this->actingAs($admin)
        ->post('/admin/sertifikat/verifikasi/'.$presensi->id.'/'.$action)
        ->assertNotFound();

    expect($presensi->fresh()->status_klaim)->toBe('pending')
        ->and($presensi->fresh()->status_kehadiran)->toBe('alfa')
        ->and($presensi->fresh()->waktu_hadir)->toBeNull();
})->with(['setuju', 'tolak']);

test('riwayat shows attendance progress and only eligible claim forms', function (int $count, bool $eligible) {
    $anggota = Anggota::factory()->create();
    $presensis = createHadirPresensi($anggota, $count);
    $response = $this->actingAs($anggota->user)->get(route('kader.riwayat.index'));

    $response->assertSuccessful()
        ->assertSeeText($count.' dari '.Sertifikat::MINIMUM_KEGIATAN_HADIR.' kegiatan hadir')
        ->assertDontSee('type="file"', false)
        ->assertDontSee('multipart/form-data', false)
        ->assertDontSeeText('bukti_kehadiran')
        ->assertDontSeeText('status_klaim');

    if ($eligible) {
        $response->assertSee(route('kader.sertifikat.klaim', $presensis->first()), false)
            ->assertSeeText('Klaim Sertifikat');
    } else {
        $response->assertDontSee('/klaim', false)
            ->assertDontSeeText('Klaim Sertifikat');
    }
})->with([
    'nol kegiatan hadir' => [0, false],
    'satu kegiatan hadir' => [1, false],
    'dua kegiatan hadir' => [2, false],
    'tiga kegiatan hadir' => [3, true],
]);

test('manual claim is forbidden below the minimum attendance threshold', function (int $count) {
    $anggota = Anggota::factory()->create();
    $presensi = createHadirPresensi($anggota, $count)->first();
    Queue::fake();

    $this->actingAs($anggota->user)
        ->post(route('kader.sertifikat.klaim', $presensi))
        ->assertForbidden();

    Queue::assertNothingPushed();
    expect(Sertifikat::count())->toBe(0);
})->with([1, 2]);

test('manual claim rejects an invalid target even when the kader is eligible', function () {
    $anggota = Anggota::factory()->create();
    createHadirPresensi($anggota, Sertifikat::MINIMUM_KEGIATAN_HADIR);
    $target = Presensi::factory()->create([
        'anggota_id' => $anggota->id,
        'status_kehadiran' => 'izin',
        'waktu_hadir' => null,
    ]);
    Queue::fake();

    $this->actingAs($anggota->user)
        ->post(route('kader.sertifikat.klaim', $target))
        ->assertForbidden();

    Queue::assertNothingPushed();
    expect(Sertifikat::count())->toBe(0);
});

test('manual claim rejects a target owned by another kader', function () {
    $owner = Anggota::factory()->create();
    createHadirPresensi($owner, Sertifikat::MINIMUM_KEGIATAN_HADIR);
    $other = Anggota::factory()->create();
    $target = Presensi::factory()->hadir()->create(['anggota_id' => $other->id]);
    Queue::fake();

    $this->actingAs($owner->user)
        ->post(route('kader.sertifikat.klaim', $target))
        ->assertForbidden();

    Queue::assertNothingPushed();
    expect(Sertifikat::count())->toBe(0);
});

test('eligible kader can claim without uploading proof', function () {
    $anggota = Anggota::factory()->create();
    $target = createHadirPresensi($anggota, Sertifikat::MINIMUM_KEGIATAN_HADIR)->first();
    $target->update([
        'bukti_kehadiran' => 'bukti_kehadiran/legacy-proof.jpg',
        'status_klaim' => 'pending',
    ]);
    Queue::fake();

    $response = $this->actingAs($anggota->user)
        ->post(route('kader.sertifikat.klaim', $target));

    $response->assertRedirect(route('kader.riwayat.index'))
        ->assertSessionHas('success', 'Klaim sertifikat sedang diproses.');
    Queue::assertPushed(
        GenerateCertificateJob::class,
        fn (GenerateCertificateJob $job) => $job->presensi?->is($target),
    );

    expect($target->fresh()->bukti_kehadiran)->toBe('bukti_kehadiran/legacy-proof.jpg')
        ->and($target->fresh()->status_klaim)->toBe('pending');
});

test('eligible claim generates one certificate without a second approval', function () {
    Storage::fake('public');
    $anggota = Anggota::factory()->create();
    $target = createHadirPresensi($anggota, Sertifikat::MINIMUM_KEGIATAN_HADIR)->first();

    $this->actingAs($anggota->user)
        ->post(route('kader.sertifikat.klaim', $target))
        ->assertRedirect(route('kader.riwayat.index'));

    $sertifikat = Sertifikat::where('kegiatan_id', $target->kegiatan_id)
        ->where('anggota_id', $anggota->id)
        ->firstOrFail();

    expect($sertifikat)->not->toBeNull();
    Storage::disk('public')->assertExists($sertifikat->file_sertifikat);
});

test('certificate job does not create a row when PDF storage fails', function () {
    $anggota = Anggota::factory()->create();
    $target = createHadirPresensi($anggota, Sertifikat::MINIMUM_KEGIATAN_HADIR)->first();
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('put')->once()->andReturnFalse();

    Storage::shouldReceive('disk')
        ->once()
        ->with('public')
        ->andReturn($disk);

    expect(fn () => (new GenerateCertificateJob($target))->handle())
        ->toThrow(RuntimeException::class, 'Gagal menyimpan file sertifikat.');
    expect(Sertifikat::where('kegiatan_id', $target->kegiatan_id)
        ->where('anggota_id', $anggota->id)
        ->doesntExist())->toBeTrue();
});

test('repeated claim creates only one certificate', function () {
    Storage::fake('public');
    $anggota = Anggota::factory()->create();
    $target = createHadirPresensi($anggota, Sertifikat::MINIMUM_KEGIATAN_HADIR)->first();

    $this->actingAs($anggota->user)
        ->post(route('kader.sertifikat.klaim', $target))
        ->assertRedirect(route('kader.riwayat.index'));
    $this->actingAs($anggota->user)
        ->post(route('kader.sertifikat.klaim', $target))
        ->assertRedirect(route('kader.riwayat.index'))
        ->assertSessionHas('info', 'Sertifikat untuk kegiatan ini sudah tersedia.');

    expect(Sertifikat::where('kegiatan_id', $target->kegiatan_id)
        ->where('anggota_id', $anggota->id)
        ->count())->toBe(1);
});

test('certificate job rechecks claim eligibility before generating', function () {
    Storage::fake('public');
    $anggota = Anggota::factory()->create();
    $target = createHadirPresensi($anggota, Sertifikat::MINIMUM_KEGIATAN_HADIR)->first();
    $job = new GenerateCertificateJob($target);

    $target->update(['status_kehadiran' => 'alfa', 'waktu_hadir' => null]);
    $job->handle();

    expect(Sertifikat::count())->toBe(0);
});

test('kader certificate list hides downloads until target and threshold are valid', function () {
    Storage::fake('public');
    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();
    $sertifikat = Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);

    $locked = $this->actingAs($anggota->user)->get(route('kader.sertifikat.index'));
    $locked->assertSuccessful()
        ->assertDontSee(route('kader.sertifikat.download', $sertifikat), false);

    Presensi::factory()->hadir()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);
    createHadirPresensi($anggota, Sertifikat::MINIMUM_KEGIATAN_HADIR - 1);

    $eligible = $this->actingAs($anggota->user)->get(route('kader.sertifikat.index'));
    $eligible->assertSuccessful()
        ->assertSee(route('kader.sertifikat.download', $sertifikat), false);
});

test('kader download requires three attended activities including the certificate target', function (int $count, bool $downloadable) {
    Storage::fake('public');
    Storage::disk('public')->put('sertifikat/test.pdf', 'dummy content');
    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();

    if ($count > 0) {
        Presensi::factory()->hadir()->create([
            'anggota_id' => $anggota->id,
            'kegiatan_id' => $kegiatan->id,
        ]);
        createHadirPresensi($anggota, $count - 1);
    }

    $sertifikat = Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
        'file_sertifikat' => 'sertifikat/test.pdf',
    ]);
    $response = $this->actingAs($anggota->user)
        ->get(route('kader.sertifikat.download', $sertifikat));

    if ($downloadable) {
        $response->assertSuccessful();
    } else {
        $response->assertForbidden();
    }
})->with([
    'nol kegiatan hadir' => [0, false],
    'satu kegiatan hadir' => [1, false],
    'dua kegiatan hadir' => [2, false],
    'tiga kegiatan hadir' => [3, true],
]);

test('kader cannot download a certificate for an unattended activity', function () {
    Storage::fake('public');
    $anggota = Anggota::factory()->create();
    createHadirPresensi($anggota, Sertifikat::MINIMUM_KEGIATAN_HADIR);
    $sertifikat = Sertifikat::factory()->create(['anggota_id' => $anggota->id]);

    $this->actingAs($anggota->user)
        ->get(route('kader.sertifikat.download', $sertifikat))
        ->assertForbidden();
});

test('eligible kader cannot download another users certificate', function () {
    Storage::fake('public');
    $owner = Anggota::factory()->create();
    $ownerTarget = createHadirPresensi($owner, Sertifikat::MINIMUM_KEGIATAN_HADIR)->first();
    $sertifikat = Sertifikat::factory()->create([
        'anggota_id' => $owner->id,
        'kegiatan_id' => $ownerTarget->kegiatan_id,
    ]);
    $other = Anggota::factory()->create();
    createHadirPresensi($other, Sertifikat::MINIMUM_KEGIATAN_HADIR);

    $this->actingAs($other->user)
        ->get(route('kader.sertifikat.download', $sertifikat))
        ->assertForbidden();
});

test('admin can download a certificate without kader attendance eligibility', function () {
    Storage::fake('public');
    Storage::disk('public')->put('sertifikat/test.pdf', 'dummy content');
    $admin = User::factory()->admin()->create();
    $sertifikat = Sertifikat::factory()->create(['file_sertifikat' => 'sertifikat/test.pdf']);

    $this->actingAs($admin)
        ->get(route('admin.sertifikat.download', $sertifikat))
        ->assertSuccessful();
});
