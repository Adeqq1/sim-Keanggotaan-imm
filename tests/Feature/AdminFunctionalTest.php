<?php

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can approve pendaftaran and create kader account', function () {
    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'disetujui',
        ]);

    $response->assertRedirect(route('admin.pendaftaran.index'));

    $pendaftaran->refresh();
    expect($pendaftaran->status_validasi)->toBe('disetujui');

    $this->assertDatabaseHas('users', [
        'email' => $pendaftaran->email,
        'role' => 'kader',
    ]);

    $newUser = User::where('email', $pendaftaran->email)->first();
    $this->assertDatabaseHas('anggota', [
        'user_id' => $newUser->id,
        'nama_lengkap' => $pendaftaran->nama_lengkap,
    ]);
});

test('admin can reject pendaftaran', function () {
    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'ditolak',
            'catatan_admin' => 'Data tidak lengkap.',
        ]);

    $response->assertRedirect(route('admin.pendaftaran.index'));

    $pendaftaran->refresh();
    expect($pendaftaran->status_validasi)->toBe('ditolak');
    expect($pendaftaran->catatan_admin)->toBe('Data tidak lengkap.');
});

test('admin can store presensi data', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota1 = Anggota::factory()->create();
    $anggota2 = Anggota::factory()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.presensi.store', $kegiatan->id), [
            'presensi' => [
                $anggota1->id => 'hadir',
                $anggota2->id => 'izin',
            ],
        ]);

    $response->assertRedirect(route('admin.kegiatan.index'));

    $this->assertDatabaseHas('presensi', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota1->id,
        'status_kehadiran' => 'hadir',
    ]);

    $this->assertDatabaseHas('presensi', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota2->id,
        'status_kehadiran' => 'izin',
    ]);
});

test('admin can upload arsip', function () {
    $admin = User::factory()->admin()->create();
    $anggota = Anggota::factory()->create();

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($admin)
        ->post(route('admin.arsip.store'), [
            'anggota_id' => $anggota->id,
            'judul_dokumen' => 'Surat Keterangan',
            'kategori_arsip' => 'surat',
            'file_arsip' => $file,
            'tanggal_unggah' => now()->toDateString(),
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('arsip', [
        'anggota_id' => $anggota->id,
        'judul_dokumen' => 'Surat Keterangan',
        'kategori_arsip' => 'surat',
    ]);
});

test('admin can download arsip', function () {
    $admin = User::factory()->admin()->create();

    Storage::fake('public');
    Storage::disk('public')->put('arsip/test.pdf', 'dummy content');

    $arsip = Arsip::factory()->create([
        'file_arsip' => 'arsip/test.pdf',
        'judul_dokumen' => 'Test Document',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.arsip.download', $arsip->id));

    $response->assertSuccessful();
});

test('admin can delete arsip', function () {
    $admin = User::factory()->admin()->create();

    Storage::fake('public');
    Storage::disk('public')->put('arsip/test.pdf', 'dummy content');

    $arsip = Arsip::factory()->create([
        'file_arsip' => 'arsip/test.pdf',
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('admin.arsip.destroy', $arsip->id));

    $response->assertRedirect();

    $this->assertDatabaseMissing('arsip', ['id' => $arsip->id]);
    Storage::disk('public')->assertMissing('arsip/test.pdf');
});
