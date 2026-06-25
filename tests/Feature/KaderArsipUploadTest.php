<?php

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

describe('Kader Arsip Index', function () {
    test('kader yang terdaftar sebagai anggota bisa mengakses halaman arsip', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('kader.arsip.index'));

        $response->assertStatus(200);
        $response->assertViewIs('kader.arsip.index');
    });

    test('kader yang tidak terdaftar sebagai anggota diredirect dengan pesan error', function () {
        $user = User::factory()->create(['role' => 'kader']);

        $response = $this->actingAs($user)->get(route('kader.arsip.index'));

        $response->assertRedirect(route('kader.dashboard'));
        $response->assertSessionHas('error');
    });

    test('kader hanya melihat dokumen miliknya sendiri', function () {
        $kaderA = User::factory()->create(['role' => 'kader']);
        $anggotaA = Anggota::factory()->create(['user_id' => $kaderA->id]);
        $arsipA = Arsip::factory()->create([
            'anggota_id' => $anggotaA->id,
            'judul_dokumen' => 'Dokumen Kader A',
        ]);

        $kaderB = User::factory()->create(['role' => 'kader']);
        $anggotaB = Anggota::factory()->create(['user_id' => $kaderB->id]);
        $arsipB = Arsip::factory()->create([
            'anggota_id' => $anggotaB->id,
            'judul_dokumen' => 'Dokumen Kader B',
        ]);

        $response = $this->actingAs($kaderA)->get(route('kader.arsip.index'));

        $response->assertStatus(200);
        $response->assertSee('Dokumen Kader A');
        $response->assertDontSee('Dokumen Kader B');
    });
});

describe('Kader Arsip Upload (Store)', function () {
    test('kader bisa upload dokumen PDF dan Excel', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $filePdf = UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => 'Laporan Pertanggungjawaban',
            'kategori_arsip' => 'laporan',
            'nomor_dokumen' => '001/LPJ/2026',
            'file_arsip' => $filePdf,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Dokumen berhasil diunggah.');

        $this->assertDatabaseHas('arsip', [
            'anggota_id' => $anggota->id,
            'judul_dokumen' => 'Laporan Pertanggungjawaban',
            'kategori_arsip' => 'laporan',
            'nomor_dokumen' => '001/LPJ/2026',
        ]);

        $arsip = Arsip::where('anggota_id', $anggota->id)->first();
        Storage::disk('public')->assertExists($arsip->file_arsip);
    });

    test('anggota_id diset otomatis dari backend meskipun kader mencoba manipulasi request', function () {
        $kaderA = User::factory()->create(['role' => 'kader']);
        $anggotaA = Anggota::factory()->create(['user_id' => $kaderA->id]);

        $kaderB = User::factory()->create(['role' => 'kader']);
        $anggotaB = Anggota::factory()->create(['user_id' => $kaderB->id]);

        $filePdf = UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf');

        // Kader A mencoba mengunggah dengan anggota_id Kader B
        $response = $this->actingAs($kaderA)->post(route('kader.arsip.store'), [
            'anggota_id' => $anggotaB->id,
            'judul_dokumen' => 'Manipulasi Anggota ID',
            'kategori_arsip' => 'lainnya',
            'file_arsip' => $filePdf,
        ]);

        $response->assertRedirect();

        // Dokumen harus tetap masuk atas nama Kader A
        $this->assertDatabaseHas('arsip', [
            'anggota_id' => $anggotaA->id,
            'judul_dokumen' => 'Manipulasi Anggota ID',
        ]);
        $this->assertDatabaseMissing('arsip', [
            'anggota_id' => $anggotaB->id,
            'judul_dokumen' => 'Manipulasi Anggota ID',
        ]);
    });

    test('validasi menolak file dengan format yang tidak diizinkan', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $fileExe = UploadedFile::fake()->create('virus.exe', 50, 'application/x-msdownload');

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => 'File Jahat',
            'kategori_arsip' => 'lainnya',
            'file_arsip' => $fileExe,
        ]);

        $response->assertSessionHasErrors('file_arsip');
    });

    test('validasi menolak file yang melebihi batas ukuran 10MB', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        // 11MB file
        $fileBig = UploadedFile::fake()->create('besar.pdf', 11264, 'application/pdf');

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => 'File Raksasa',
            'kategori_arsip' => 'lainnya',
            'file_arsip' => $fileBig,
        ]);

        $response->assertSessionHasErrors('file_arsip');
    });
});

describe('Kader Arsip Download', function () {
    test('kader bisa mendownload arsip miliknya sendiri', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $filePdf = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');
        $path = Storage::disk('public')->putFile('arsip', $filePdf);

        $arsip = Arsip::factory()->create([
            'anggota_id' => $anggota->id,
            'file_arsip' => $path,
            'judul_dokumen' => 'Surat Rekomendasi',
        ]);

        $response = $this->actingAs($user)->get(route('kader.arsip.download', $arsip));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Surat_Rekomendasi.pdf');
    });

    test('kader tidak bisa mendownload arsip milik kader lain', function () {
        $kaderA = User::factory()->create(['role' => 'kader']);
        $anggotaA = Anggota::factory()->create(['user_id' => $kaderA->id]);

        $kaderB = User::factory()->create(['role' => 'kader']);
        $anggotaB = Anggota::factory()->create(['user_id' => $kaderB->id]);

        $filePdf = UploadedFile::fake()->create('rahasia.pdf', 100, 'application/pdf');
        $path = Storage::disk('public')->putFile('arsip', $filePdf);

        $arsipB = Arsip::factory()->create([
            'anggota_id' => $anggotaB->id,
            'file_arsip' => $path,
            'judul_dokumen' => 'Dokumen Rahasia B',
        ]);

        $response = $this->actingAs($kaderA)->get(route('kader.arsip.download', $arsipB));

        $response->assertStatus(403);
    });
});

describe('Akses Non-Kader', function () {
    test('non-kader tidak dapat mengakses route arsip kader', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('kader.arsip.index'));

        // Bergantung pada middleware penanganan role, biasanya diredirect atau forbidden
        // Di sini kita cek assertForbidden atau assertRedirect
        $response->assertForbidden();
    });

    test('guest tidak dapat mengakses route arsip kader', function () {
        $response = $this->get(route('kader.arsip.index'));

        $response->assertRedirect(route('login'));
    });
});
