<?php

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

describe('Kader Arsip Index', function () {
    test('kader yang terdaftar sebagai anggota bisa mengakses halaman arsip', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('kader.arsip.index'));

        $response->assertSuccessful();
        $response->assertViewIs('kader.arsip.index');
    });

    test('kader bisa mengakses halaman upload arsip terpisah', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('kader.arsip.create'));

        $response->assertSuccessful();
        $response->assertViewIs('kader.arsip.create');
        $response->assertViewHas('kategori', function (array $kategori): bool {
            return array_keys($kategori) === [
                'proposal',
                'lpj',
                'surat_keputusan',
            ];
        });
        $response->assertSee('<option value="proposal"', false);
        $response->assertSee('Laporan Pertanggung Jawaban (LPJ)');
        $response->assertSee('Surat Keputusan');
        $response->assertDontSee('<option value="surat_masuk"', false);
        $response->assertDontSee('<option value="surat_keluar"', false);
        $response->assertDontSee('<option value="lainnya"', false);
        $response->assertDontSee('Pilih Anggota');
        $response->assertSee('5MB');
        $response->assertDontSee('JPG');
        $response->assertDontSee('PNG');
    });

    test('kader tanpa profil anggota tidak bisa mengakses halaman upload arsip', function () {
        $user = User::factory()->create(['role' => 'kader']);

        $response = $this->actingAs($user)->get(route('kader.arsip.create'));

        $response->assertRedirect(route('kader.dashboard'));
        $response->assertSessionHas('error');
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

        $response->assertSuccessful();
        $response->assertSee('Dokumen Kader A');
        $response->assertDontSee('Dokumen Kader B');
    });

    test('kader bisa mencari dan memfilter arsip miliknya', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        Arsip::factory()->create([
            'anggota_id' => $anggota->id,
            'judul_dokumen' => 'Proposal Musyawarah',
            'nomor_dokumen' => 'PROP-001',
            'kategori_arsip' => 'proposal',
        ]);

        Arsip::factory()->create([
            'anggota_id' => $anggota->id,
            'judul_dokumen' => 'Surat Undangan',
            'nomor_dokumen' => 'SM-001',
            'kategori_arsip' => 'surat_masuk',
        ]);

        $response = $this->actingAs($user)->get(route('kader.arsip.index', [
            'q' => 'PROP',
            'kategori' => 'proposal',
        ]));

        $response->assertSuccessful();
        $response->assertSee('Proposal Musyawarah');
        $response->assertDontSee('Surat Undangan');
        $response->assertSee('Atur ulang filter');
    });

    test('arsip lama dengan kategori global tetap terlihat oleh kader', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        Arsip::factory()->create([
            'anggota_id' => $anggota->id,
            'judul_dokumen' => 'Surat Masuk Lama',
            'kategori_arsip' => 'surat_masuk',
        ]);

        $response = $this->actingAs($user)->get(route('kader.arsip.index'));

        $response->assertSuccessful();
        $response->assertSee('Surat Masuk Lama');
        $response->assertSee('Surat Masuk');
    });
});

describe('Kader Arsip Upload (Store)', function () {
    test('kader bisa upload dokumen dengan setiap kategori yang diizinkan', function (string $kategori) {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $judul = 'Dokumen '.str_replace('_', ' ', $kategori);
        $filePdf = UploadedFile::fake()->create($kategori.'.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => $judul,
            'kategori_arsip' => $kategori,
            'nomor_dokumen' => '001/LPJ/2026',
            'file_arsip' => $filePdf,
        ]);

        $response->assertRedirect(route('kader.arsip.index'));
        $response->assertSessionHas('success', 'Dokumen berhasil diunggah.');

        $this->assertDatabaseHas('arsip', [
            'anggota_id' => $anggota->id,
            'judul_dokumen' => $judul,
            'kategori_arsip' => $kategori,
            'nomor_dokumen' => '001/LPJ/2026',
        ]);

        $arsip = Arsip::where('anggota_id', $anggota->id)->firstOrFail();
        Storage::disk('local')->assertExists($arsip->file_arsip);
    })->with([
        'proposal' => 'proposal',
        'lpj' => 'lpj',
        'surat keputusan' => 'surat_keputusan',
    ]);

    test('kader tidak bisa upload kategori di luar allow-list', function (string $kategori) {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);
        $judul = 'Kategori Ilegal '.str_replace('_', ' ', $kategori);

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => $judul,
            'kategori_arsip' => $kategori,
            'file_arsip' => UploadedFile::fake()->create('ilegal.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors([
            'kategori_arsip' => 'Kategori tidak valid.',
        ]);
        $this->assertDatabaseMissing('arsip', ['judul_dokumen' => $judul]);
        expect(Storage::disk('local')->allFiles('arsip'))->toBeEmpty();
    })->with([
        'surat masuk' => 'surat_masuk',
        'surat keluar' => 'surat_keluar',
        'lainnya' => 'lainnya',
    ]);

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
            'kategori_arsip' => 'proposal',
            'file_arsip' => $filePdf,
        ]);

        $response->assertRedirect(route('kader.arsip.index'));

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
            'kategori_arsip' => 'proposal',
            'file_arsip' => $fileExe,
        ]);

        $response->assertSessionHasErrors('file_arsip');
    });

    test('validasi menolak file yang melebihi batas ukuran 5MB', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        // 5121 KB — satu kilobyte melebihi batas 5MB
        $fileBig = UploadedFile::fake()->create('besar.pdf', 5121, 'application/pdf');

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => 'File Raksasa',
            'kategori_arsip' => 'proposal',
            'file_arsip' => $fileBig,
        ]);

        $response->assertSessionHasErrors('file_arsip');
    });

    test('validasi menerima file tepat 5MB', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        // Tepat 5120 KB = 5MB
        $fileExact = UploadedFile::fake()->create('tepat5mb.pdf', 5120, 'application/pdf');

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => 'File Tepat 5MB',
            'kategori_arsip' => 'proposal',
            'file_arsip' => $fileExact,
        ]);

        $response->assertRedirect(route('kader.arsip.index'));
        $response->assertSessionHas('success');
    });

    test('validasi menolak file JPG', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $fileJpg = UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => 'Upload Foto JPG',
            'kategori_arsip' => 'proposal',
            'file_arsip' => $fileJpg,
        ]);

        $response->assertSessionHasErrors('file_arsip');
    });

    test('validasi menolak file JPEG', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $fileJpeg = UploadedFile::fake()->create('foto.jpeg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => 'Upload Foto JPEG',
            'kategori_arsip' => 'proposal',
            'file_arsip' => $fileJpeg,
        ]);

        $response->assertSessionHasErrors('file_arsip');
    });

    test('validasi menolak file PNG', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $filePng = UploadedFile::fake()->create('gambar.png', 100, 'image/png');

        $response = $this->actingAs($user)->post(route('kader.arsip.store'), [
            'judul_dokumen' => 'Upload Gambar PNG',
            'kategori_arsip' => 'proposal',
            'file_arsip' => $filePng,
        ]);

        $response->assertSessionHasErrors('file_arsip');
    });
});

describe('Kader Arsip Download', function () {
    test('kader bisa mendownload arsip miliknya sendiri', function () {
        $user = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->create(['user_id' => $user->id]);

        $filePdf = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');
        $path = Storage::disk('local')->putFile('arsip', $filePdf);

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
        $path = Storage::disk('local')->putFile('arsip', $filePdf);

        $arsipB = Arsip::factory()->create([
            'anggota_id' => $anggotaB->id,
            'file_arsip' => $path,
            'judul_dokumen' => 'Dokumen Rahasia B',
        ]);

        $response = $this->actingAs($kaderA)->get(route('kader.arsip.download', $arsipB));

        $response->assertForbidden();
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
