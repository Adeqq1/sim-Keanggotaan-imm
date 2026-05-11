<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('kader can view profile edit page', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertSuccessful();
});

test('kader can update profile', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'nama_lengkap' => 'Updated Nama Lengkap',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1999-05-15',
            'no_telp' => '089876543210',
            'alamat' => 'Jl. Baru No. 10',
        ]);

    $response->assertRedirect(route('profile.edit'));

    $user->refresh();
    expect($user->name)->toBe('Updated Name');

    $anggota->refresh();
    expect($anggota->nama_lengkap)->toBe('Updated Nama Lengkap');
    expect($anggota->tempat_lahir)->toBe('Bandung');
});

test('kader can view ekta preview', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('kader.ekta'));

    $response->assertSuccessful();
});

test('kader can download ekta pdf', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id, 'nia' => '9999999999']);

    $response = $this->actingAs($user)->get(route('kader.ekta.download'));

    $response->assertSuccessful();
    expect($response->headers->get('Content-Type'))->toContain('pdf');
});

test('kader can view sertifikat list', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);
    $kegiatan = Kegiatan::factory()->create();

    Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);

    $response = $this->actingAs($user)->get(route('kader.sertifikat.index'));

    $response->assertSuccessful();
});

test('kader can download sertifikat pdf', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);

    Storage::fake('public');
    Storage::disk('public')->put('sertifikat/test.pdf', 'dummy content');

    $sertifikat = Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'file_sertifikat' => 'sertifikat/test.pdf',
    ]);

    $response = $this->actingAs($user)->get(route('kader.sertifikat.download', $sertifikat->id));

    $response->assertSuccessful();
});

test('kader can view riwayat keaktifan', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);
    $kegiatan = Kegiatan::factory()->create();

    Presensi::factory()->hadir()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);

    $response = $this->actingAs($user)->get(route('kader.riwayat.index'));

    $response->assertSuccessful();
});

test('kader can view arsip list', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('kader.arsip.index'));

    $response->assertSuccessful();
});
