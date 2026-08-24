<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\PenilaianKegiatan;
use App\Models\Presensi;
use App\Models\SesiKegiatan;
use App\Models\User;

function makeAssessableActivity(): Kegiatan
{
    $kegiatan = Kegiatan::factory()->withDefaultSession()->create([
        'jenis_pelaksanaan' => Kegiatan::MULTI_SESI,
        'minimum_sesi_terverifikasi' => 3,
    ]);

    SesiKegiatan::factory()->for($kegiatan)->create(['urutan' => 2, 'nama_sesi' => 'Sesi 2']);
    SesiKegiatan::factory()->for($kegiatan)->create(['urutan' => 3, 'nama_sesi' => 'Sesi 3']);

    return $kegiatan->fresh('sesiKegiatans');
}

function verifyMemberFor(Kegiatan $kegiatan, Anggota $anggota): void
{
    $instruktur = User::factory()->instruktur()->create();

    foreach ($kegiatan->sesiKegiatans as $sesi) {
        Presensi::factory()->terverifikasi()->create([
            'kegiatan_id' => $kegiatan->id,
            'sesi_kegiatan_id' => $sesi->id,
            'anggota_id' => $anggota->id,
            'pemeriksa_id' => $instruktur->id,
        ]);
    }
}

test('instructor can create and update one assessment per eligible member', function (string $nilai) {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = makeAssessableActivity();
    $anggota = Anggota::factory()->create();
    verifyMemberFor($kegiatan, $anggota);

    $this->actingAs($instruktur)
        ->put(route('admin.kegiatan.penilaian.update', [$kegiatan, $anggota]), ['nilai' => $nilai])
        ->assertRedirect();

    $this->actingAs($instruktur)
        ->put(route('admin.kegiatan.penilaian.update', [$kegiatan, $anggota]), ['nilai' => 'A'])
        ->assertRedirect();

    expect(PenilaianKegiatan::where('kegiatan_id', $kegiatan->id)->where('anggota_id', $anggota->id)->count())->toBe(1)
        ->and(PenilaianKegiatan::first()->nilai)->toBe('A');
})->with(['A', 'B', 'C', 'D']);

test('assessment page shows only eligible active kader members and accessible controls', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = makeAssessableActivity();
    $eligible = Anggota::factory()->create(['nama_lengkap' => 'Kader Layak']);
    $inactive = Anggota::factory()->inactive()->create(['nama_lengkap' => 'Kader Nonaktif']);
    $otherRole = Anggota::factory()->create(['user_id' => User::factory()->admin()]);
    verifyMemberFor($kegiatan, $eligible);
    verifyMemberFor($kegiatan, $inactive);
    verifyMemberFor($kegiatan, $otherRole);

    $this->actingAs($instruktur)
        ->get(route('admin.kegiatan.penilaian.index', $kegiatan))
        ->assertSuccessful()
        ->assertSee('Kader Layak')
        ->assertDontSee('Kader Nonaktif')
        ->assertDontSee($otherRole->nama_lengkap)
        ->assertSee('name="nilai"', false)
        ->assertSee('A - Sangat Bagus')
        ->assertSee('Sesi 1');
});

test('admin can read assessments but cannot update them', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = makeAssessableActivity();
    $anggota = Anggota::factory()->create();
    verifyMemberFor($kegiatan, $anggota);
    PenilaianKegiatan::factory()->create(['kegiatan_id' => $kegiatan->id, 'anggota_id' => $anggota->id, 'nilai' => 'B']);

    $this->actingAs($admin)
        ->get(route('admin.kegiatan.penilaian.index', $kegiatan))
        ->assertSuccessful()
        ->assertSee('B - Bagus')
        ->assertDontSee('name="nilai"', false)
        ->assertDontSee('Simpan');

    $this->actingAs($admin)
        ->put(route('admin.kegiatan.penilaian.update', [$kegiatan, $anggota]), ['nilai' => 'A'])
        ->assertForbidden();

    expect(PenilaianKegiatan::first()->nilai)->toBe('B');
});

test('invalid assessment does not change the existing value', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = makeAssessableActivity();
    $anggota = Anggota::factory()->create();
    verifyMemberFor($kegiatan, $anggota);
    PenilaianKegiatan::factory()->create(['kegiatan_id' => $kegiatan->id, 'anggota_id' => $anggota->id, 'nilai' => 'B']);

    $response = $this->actingAs($instruktur)
        ->put(route('admin.kegiatan.penilaian.update', [$kegiatan, $anggota]), ['nilai' => 'E']);

    $response->assertSessionHasErrorsIn('penilaian-'.$anggota->id, 'nilai');
    expect(PenilaianKegiatan::first()->nilai)->toBe('B');
});

test('assessment is unavailable for single-session activities', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->withDefaultSession()->create();
    $anggota = Anggota::factory()->create();

    $this->actingAs($instruktur)
        ->get(route('admin.kegiatan.penilaian.index', $kegiatan))
        ->assertNotFound();

    $this->actingAs($instruktur)
        ->put(route('admin.kegiatan.penilaian.update', [$kegiatan, $anggota]), ['nilai' => 'A'])
        ->assertSessionHasErrors();
    expect(PenilaianKegiatan::count())->toBe(0);
});
