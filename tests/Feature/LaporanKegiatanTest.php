<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use App\Models\MateriKegiatan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function laporanPayload(array $overrides = []): array
{
    return [
        'tujuan' => 'Meningkatkan kapasitas kader.',
        'ringkasan' => 'Kegiatan terlaksana sesuai rencana.',
        'agenda' => 'Materi, diskusi, dan evaluasi.',
        'narasumber' => 'Ahmad Fauzan',
        'hasil' => 'Peserta memahami materi.',
        'kendala' => 'Waktu terbatas.',
        'tindak_lanjut' => 'Pendampingan lanjutan.',
        ...$overrides,
    ];
}

test('admin melihat submenu detail kegiatan dan rekap presensi yang terisolasi', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create(['nama_kegiatan' => 'Kajian Utama']);
    $kegiatanLain = Kegiatan::factory()->create(['nama_kegiatan' => 'Kajian Lain']);

    foreach (['hadir', 'hadir', 'izin', 'alfa'] as $status) {
        Presensi::factory()->create([
            'kegiatan_id' => $kegiatan,
            'anggota_id' => Anggota::factory(),
            'status_kehadiran' => $status,
        ]);
    }
    Presensi::factory()->hadir()->create(['kegiatan_id' => $kegiatanLain, 'anggota_id' => Anggota::factory()]);

    $this->actingAs($admin)->get(route('admin.kegiatan.index'))
        ->assertSuccessful()
        ->assertSeeInOrder(['Daftar Kegiatan', 'Rekap Presensi', 'Laporan Kegiatan', 'Materi Kegiatan']);

    $this->actingAs($admin)->get(route('admin.kegiatan.show', $kegiatan))
        ->assertSuccessful()
        ->assertSee('Kajian Utama')
        ->assertSeeInOrder(['Jumlah Peserta', '4', 'Hadir', '2', 'Izin', '1', 'Alfa', '1']);

    $rekap = $this->actingAs($admin)->get(route('admin.presensi.index'))->assertSuccessful();
    $rekap->assertSee("data-kegiatan-id=\"{$kegiatan->id}\" data-peserta=\"4\" data-hadir=\"2\" data-izin=\"1\" data-alfa=\"1\"", false)
        ->assertSee("data-kegiatan-id=\"{$kegiatanLain->id}\" data-peserta=\"1\" data-hadir=\"1\" data-izin=\"0\" data-alfa=\"0\"", false);
});

test('kegiatan tanpa presensi ditampilkan sebagai belum dicatat bukan alfa otomatis', function () {
    $kegiatan = Kegiatan::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.kegiatan.show', $kegiatan))
        ->assertSuccessful()
        ->assertSee('Presensi belum dicatat')
        ->assertSeeInOrder(['Jumlah Peserta', '0', 'Hadir', '0', 'Izin', '0', 'Alfa', '0']);
});

test('admin membuat satu laporan berdasarkan kegiatan route dan unique constraint melarang duplikat', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $kegiatanLain = Kegiatan::factory()->create();

    $this->actingAs($admin)->post(route('admin.kegiatan.laporan-kegiatan.store', $kegiatan), laporanPayload([
        'kegiatan_id' => $kegiatanLain->id,
    ]))->assertRedirect();

    $laporan = LaporanKegiatan::sole();
    expect($laporan->kegiatan_id)->toBe($kegiatan->id);

    $this->actingAs($admin)->get(route('admin.kegiatan.laporan-kegiatan.create', $kegiatan))
        ->assertRedirect(route('admin.laporan-kegiatan.edit', $laporan));
    $this->actingAs($admin)->post(route('admin.kegiatan.laporan-kegiatan.store', $kegiatan), laporanPayload())
        ->assertRedirect(route('admin.laporan-kegiatan.edit', $laporan));
    expect(LaporanKegiatan::count())->toBe(1);

    expect(fn () => LaporanKegiatan::factory()->create(['kegiatan_id' => $kegiatan]))
        ->toThrow(QueryException::class);
});

test('validasi laporan menolak field wajib format dan ukuran lampiran', function () {
    Storage::fake('local');
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();

    $this->actingAs($admin)->post(route('admin.kegiatan.laporan-kegiatan.store', $kegiatan), [])
        ->assertSessionHasErrors(['tujuan', 'ringkasan', 'agenda', 'hasil']);
    $this->actingAs($admin)->post(route('admin.kegiatan.laporan-kegiatan.store', $kegiatan), laporanPayload([
        'file_lampiran' => UploadedFile::fake()->create('script.exe', 10, 'application/octet-stream'),
    ]))->assertSessionHasErrors('file_lampiran');
    $this->actingAs($admin)->post(route('admin.kegiatan.laporan-kegiatan.store', $kegiatan), laporanPayload([
        'file_lampiran' => UploadedFile::fake()->create('besar.pdf', 2049, 'application/pdf'),
    ]))->assertSessionHasErrors('file_lampiran');

    expect(LaporanKegiatan::count())->toBe(0);
    expect(Storage::disk('local')->allFiles('laporan_kegiatan'))->toBe([]);
});

test('validasi membatasi seluruh narasi laporan saat create dan update', function () {
    Storage::fake('local');
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $tooLong = str_repeat('a', 16001);
    $fields = ['tujuan', 'ringkasan', 'agenda', 'narasumber', 'hasil', 'kendala', 'tindak_lanjut'];

    $this->actingAs($admin)->get(route('admin.kegiatan.laporan-kegiatan.create', $kegiatan))
        ->assertSuccessful()
        ->assertSee('maxlength="16000"', false);

    $this->actingAs($admin)->post(
        route('admin.kegiatan.laporan-kegiatan.store', $kegiatan),
        laporanPayload(array_fill_keys($fields, $tooLong)),
    )->assertSessionHasErrors($fields);
    expect(LaporanKegiatan::count())->toBe(0);

    $path = 'laporan_kegiatan/lama.pdf';
    Storage::disk('local')->put($path, 'lama');
    $laporan = LaporanKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan,
        'hasil' => 'Hasil lama.',
        'file_lampiran' => $path,
    ]);

    $this->actingAs($admin)->put(
        route('admin.laporan-kegiatan.update', $laporan),
        laporanPayload(['hasil' => $tooLong]),
    )->assertSessionHasErrors('hasil');

    expect($laporan->refresh()->hasil)->toBe('Hasil lama.')
        ->and($laporan->file_lampiran)->toBe($path);
    Storage::disk('local')->assertExists($path);
});

test('lampiran privat dapat dibuat dipertahankan diganti diunduh dan dihapus', function () {
    Storage::fake('local');
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create(['nama_kegiatan' => 'Pelatihan Kader']);

    $this->actingAs($admin)->post(route('admin.kegiatan.laporan-kegiatan.store', $kegiatan), laporanPayload([
        'file_lampiran' => UploadedFile::fake()->create('awal.pdf', 100, 'application/pdf'),
    ]));
    $laporan = LaporanKegiatan::sole();
    $oldPath = $laporan->file_lampiran;
    Storage::disk('local')->assertExists($oldPath);

    $this->actingAs($admin)->put(route('admin.laporan-kegiatan.update', $laporan), laporanPayload(['hasil' => 'Hasil baru.']));
    expect($laporan->refresh()->file_lampiran)->toBe($oldPath);
    Storage::disk('local')->assertExists($oldPath);

    $this->actingAs($admin)->put(route('admin.laporan-kegiatan.update', $laporan), laporanPayload([
        'file_lampiran' => UploadedFile::fake()->create('baru.pdf', 100, 'application/pdf'),
    ]));
    $newPath = $laporan->refresh()->file_lampiran;
    expect($newPath)->not->toBe($oldPath);
    Storage::disk('local')->assertMissing($oldPath);
    Storage::disk('local')->assertExists($newPath);

    $download = $this->actingAs($admin)->get(route('admin.laporan-kegiatan.lampiran.download', $laporan));
    $download->assertSuccessful()
        ->assertDownload('pelatihan-kader-lampiran.pdf')
        ->assertHeader('pragma', 'no-cache')
        ->assertHeader('x-content-type-options', 'nosniff');
    expect($download->headers->get('cache-control'))
        ->toContain('private')->toContain('no-store')->toContain('max-age=0');

    $this->actingAs($admin)->delete(route('admin.laporan-kegiatan.destroy', $laporan))
        ->assertRedirect(route('admin.laporan-kegiatan.index'));
    $this->assertModelMissing($laporan);
    Storage::disk('local')->assertMissing($newPath);
});

test('detail laporan membaca metadata dan presensi live', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create(['nama_kegiatan' => 'Seminar Live', 'lokasi' => 'Aula Merah']);
    $laporan = LaporanKegiatan::factory()->create(['kegiatan_id' => $kegiatan, 'file_lampiran' => null]);
    $presensi = Presensi::factory()->hadir()->create(['kegiatan_id' => $kegiatan, 'anggota_id' => Anggota::factory()]);

    $this->actingAs($admin)->get(route('admin.laporan-kegiatan.show', $laporan))
        ->assertSuccessful()->assertSee('Seminar Live')->assertSee('Aula Merah')
        ->assertSeeInOrder(['Hadir', '1', 'Izin', '0']);

    $presensi->update(['status_kehadiran' => 'izin']);
    $this->actingAs($admin)->get(route('admin.laporan-kegiatan.show', $laporan))
        ->assertSeeInOrder(['Hadir', '0', 'Izin', '1']);
});

test('instruktur dapat mengunduh laporan kegiatan sebagai pdf dengan rekap presensi', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->create([
        'nama_kegiatan' => 'Pelatihan Kader',
        'tanggal_waktu' => '2026-08-20 10:30:00',
    ]);
    $laporan = LaporanKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan,
        'kendala' => "Waktu terbatas\nAgenda disesuaikan.",
    ]);

    foreach (['hadir', 'hadir', 'izin', 'alfa'] as $status) {
        Presensi::factory()->create([
            'kegiatan_id' => $kegiatan,
            'anggota_id' => Anggota::factory(),
            'status_kehadiran' => $status,
        ]);
    }

    $this->actingAs($instruktur)
        ->get(route('admin.laporan-kegiatan.download', $laporan))
        ->assertSuccessful()
        ->assertDownload('laporan-kegiatan-pelatihan-kader-20260820.pdf')
        ->assertHeader('content-type', 'application/pdf');
});

test('tombol laporan instruktur hanya aktif saat laporan tersedia', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->create(['nama_kegiatan' => 'Kegiatan Berlaporan']);
    $laporan = LaporanKegiatan::factory()->create(['kegiatan_id' => $kegiatan]);

    $this->actingAs($instruktur)->get(route('admin.kegiatan.show', $kegiatan))
        ->assertSuccessful()
        ->assertSee('Unduh Laporan')
        ->assertSee(route('admin.laporan-kegiatan.download', $laporan), false);

    $tanpaLaporan = Kegiatan::factory()->create(['nama_kegiatan' => 'Kegiatan Tanpa Laporan']);
    $this->actingAs($instruktur)->get(route('admin.kegiatan.show', $tanpaLaporan))
        ->assertSuccessful()
        ->assertSee('Laporan belum tersedia')
        ->assertDontSee(route('admin.laporan-kegiatan.download', ['laporanKegiatan' => $tanpaLaporan->id]), false);
});

test('download pdf laporan kegiatan hanya untuk instruktur', function () {
    $kegiatan = Kegiatan::factory()->create();
    $laporan = LaporanKegiatan::factory()->create(['kegiatan_id' => $kegiatan]);

    foreach ([User::factory()->admin()->create(), User::factory()->kader()->create()] as $user) {
        $this->actingAs($user)
            ->get(route('admin.laporan-kegiatan.download', $laporan))
            ->assertForbidden();
    }

    auth()->logout();
    $this->get(route('admin.laporan-kegiatan.download', $laporan))
        ->assertRedirect(route('login'));

    $this->actingAs(User::factory()->instruktur()->create())
        ->get(route('admin.laporan-kegiatan.download', ['laporanKegiatan' => 999999]))
        ->assertNotFound();
});

test('menghapus kegiatan cascade laporan dan membersihkan lampiran privat', function () {
    Storage::fake('local');
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $path = 'laporan_kegiatan/lampiran.pdf';
    Storage::disk('local')->put($path, 'laporan');
    $laporan = LaporanKegiatan::factory()->create(['kegiatan_id' => $kegiatan, 'file_lampiran' => $path]);

    $this->actingAs($admin)->delete(route('admin.kegiatan.destroy', $kegiatan))->assertRedirect();

    $this->assertModelMissing($laporan);
    Storage::disk('local')->assertMissing($path);
});

test('kegagalan cleanup thumbnail tetap melanjutkan cleanup seluruh file privat', function () {
    Storage::fake('local');
    $localDisk = Storage::disk('local');
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create(['thumbnail' => 'kegiatan_thumbnails/gagal.webp']);
    $materiPath = 'materi_kegiatan/materi.pdf';
    $laporanPath = 'laporan_kegiatan/lampiran.pdf';
    $materi = MateriKegiatan::factory()->create(['kegiatan_id' => $kegiatan, 'file_materi' => $materiPath]);
    $laporan = LaporanKegiatan::factory()->create(['kegiatan_id' => $kegiatan, 'file_lampiran' => $laporanPath]);
    $localDisk->put($materiPath, 'materi');
    $localDisk->put($laporanPath, 'laporan');

    $publicDisk = Mockery::mock(FilesystemAdapter::class);
    $publicDisk->shouldReceive('exists')->once()->with($kegiatan->thumbnail)->andReturnTrue();
    $publicDisk->shouldReceive('delete')->once()->with($kegiatan->thumbnail)->andThrow(new RuntimeException('public disk unavailable'));
    Storage::shouldReceive('disk')->once()->with('public')->andReturn($publicDisk);
    Storage::shouldReceive('disk')->twice()->with('local')->andReturn($localDisk);

    $this->actingAs($admin)->delete(route('admin.kegiatan.destroy', $kegiatan))->assertRedirect();

    $this->assertModelMissing($kegiatan);
    $this->assertModelMissing($materi);
    $this->assertModelMissing($laporan);
    $localDisk->assertMissing([$materiPath, $laporanPath]);
});

test('sidebar instruktur hanya mengaktifkan rekap presensi pada halaman presensi', function () {
    $response = $this->actingAs(User::factory()->instruktur()->create())
        ->get(route('admin.presensi.index'))
        ->assertSuccessful();

    expect(substr_count($response->getContent(), 'class="sidebar-link active"'))->toBe(1)
        ->and(substr_count($response->getContent(), 'class="sidebar-sublink active"'))->toBe(1);

    $response
        ->assertSee('href="'.route('admin.presensi.index').'"'.PHP_EOL.'                           class="sidebar-sublink active"', false)
        ->assertSee('aria-current="page"', false);
});

test('akses rekap laporan lampiran dan direktori materi mengikuti role', function () {
    Storage::fake('local');
    $admin = User::factory()->admin()->create();
    $instruktur = User::factory()->instruktur()->create();
    $kader = User::factory()->kader()->create();
    $kegiatan = Kegiatan::factory()->create();
    $materi = MateriKegiatan::factory()->create(['kegiatan_id' => $kegiatan]);
    $laporan = LaporanKegiatan::factory()->create(['kegiatan_id' => $kegiatan, 'file_lampiran' => 'laporan_kegiatan/hilang.pdf']);

    $this->actingAs($instruktur)->get(route('admin.presensi.index'))->assertSuccessful();
    $this->actingAs($kader)->get(route('admin.presensi.index'))->assertForbidden();
    auth()->logout();
    $this->get(route('admin.presensi.index'))->assertRedirect(route('login'));

    $this->actingAs($admin)->get(route('admin.materi-kegiatan.index'))
        ->assertSuccessful()->assertSee($materi->judul)->assertDontSee('Unduh Materi');
    $this->actingAs($admin)->get(route('admin.kegiatan.materi-kegiatan.index', $kegiatan))->assertForbidden();
    $this->actingAs($admin)->get(route('admin.laporan-kegiatan.lampiran.download', $laporan))->assertNotFound();

    foreach ([$instruktur, $kader] as $user) {
        $this->actingAs($user)->get(route('admin.laporan-kegiatan.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.kegiatan.laporan-kegiatan.create', $kegiatan))->assertForbidden();
        $this->actingAs($user)->post(route('admin.kegiatan.laporan-kegiatan.store', $kegiatan), laporanPayload())->assertForbidden();
        $this->actingAs($user)->get(route('admin.laporan-kegiatan.show', $laporan))->assertForbidden();
        $this->actingAs($user)->get(route('admin.laporan-kegiatan.edit', $laporan))->assertForbidden();
        $this->actingAs($user)->put(route('admin.laporan-kegiatan.update', $laporan), laporanPayload())->assertForbidden();
        $this->actingAs($user)->delete(route('admin.laporan-kegiatan.destroy', $laporan))->assertForbidden();
        $this->actingAs($user)->get(route('admin.laporan-kegiatan.lampiran.download', $laporan))->assertForbidden();
        $this->actingAs($user)->get(route('admin.materi-kegiatan.index'))->assertForbidden();
    }

    auth()->logout();
    $this->get(route('admin.laporan-kegiatan.index'))->assertRedirect(route('login'));
});
