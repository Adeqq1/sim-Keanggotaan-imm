<?php

use App\Http\Controllers\KegiatanController;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\MateriKegiatan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

function materiInstructor(): User
{
    return User::factory()->instruktur()->create();
}

/**
 * @return array{0: User, 1: Anggota}
 */
function materiKader(): array
{
    $user = User::factory()->kader()->create();

    return [$user, Anggota::factory()->create(['user_id' => $user->id])];
}

function materiFile(string $name = 'materi.pdf', int $kilobytes = 100): UploadedFile
{
    return UploadedFile::fake()->create($name, $kilobytes, 'application/pdf');
}

test('instruktur dapat melihat daftar dan membuat materi privat untuk kegiatan dari route', function () {
    Storage::fake('local');
    $instruktur = materiInstructor();
    $kegiatan = Kegiatan::factory()->create();
    $kegiatanLain = Kegiatan::factory()->create();

    $this->actingAs($instruktur)
        ->get(route('admin.kegiatan.materi-kegiatan.index', $kegiatan))
        ->assertSuccessful()
        ->assertSee($kegiatan->nama_kegiatan);

    $this->actingAs($instruktur)
        ->post(route('admin.kegiatan.materi-kegiatan.store', $kegiatan), [
            'kegiatan_id' => $kegiatanLain->id,
            'judul' => 'Modul Perkaderan',
            'deskripsi' => 'Materi wajib peserta kegiatan.',
            'file_materi' => materiFile(),
        ])
        ->assertRedirect(route('admin.kegiatan.materi-kegiatan.index', $kegiatan))
        ->assertSessionHas('success', 'Materi berhasil ditambahkan.');

    $materi = MateriKegiatan::sole();
    expect($materi->kegiatan_id)->toBe($kegiatan->id)
        ->and($materi->file_materi)->toStartWith('materi_kegiatan/');
    Storage::disk('local')->assertExists($materi->file_materi);
});

test('kegagalan cleanup rollback tidak menutupi exception database create', function () {
    Storage::fake('local');
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('exists')->once()->andThrow(new RuntimeException('storage cleanup failure'));
    $event = 'eloquent.creating: '.MateriKegiatan::class;

    Event::listen($event, function () use ($disk): void {
        Storage::shouldReceive('disk')->once()->with('local')->andReturn($disk);
        throw new RuntimeException('database create failure');
    });

    try {
        expect(fn () => $this->withoutExceptionHandling()
            ->actingAs(materiInstructor())
            ->post(route('admin.kegiatan.materi-kegiatan.store', Kegiatan::factory()->create()), [
                'judul' => 'Materi Gagal',
                'deskripsi' => 'Database gagal.',
                'file_materi' => materiFile(),
            ]))->toThrow(RuntimeException::class, 'database create failure');
    } finally {
        Event::forget($event);
    }

    expect(MateriKegiatan::count())->toBe(0);
});

test('instruktur dapat memperbarui materi tanpa mengganti file', function () {
    Storage::fake('local');
    $materi = MateriKegiatan::factory()->create(['file_materi' => 'materi_kegiatan/lama.pdf']);
    Storage::disk('local')->put($materi->file_materi, 'lama');

    $this->actingAs(materiInstructor())
        ->put(route('admin.kegiatan.materi-kegiatan.update', [$materi->kegiatan, $materi]), [
            'judul' => 'Judul Baru',
            'deskripsi' => 'Deskripsi baru.',
        ])
        ->assertRedirect(route('admin.kegiatan.materi-kegiatan.index', $materi->kegiatan));

    expect($materi->refresh()->file_materi)->toBe('materi_kegiatan/lama.pdf')
        ->and($materi->judul)->toBe('Judul Baru');
    Storage::disk('local')->assertExists('materi_kegiatan/lama.pdf');
});

test('instruktur dapat mengganti file dan file lama dibersihkan', function () {
    Storage::fake('local');
    $materi = MateriKegiatan::factory()->create(['file_materi' => 'materi_kegiatan/lama.pdf']);
    Storage::disk('local')->put($materi->file_materi, 'lama');

    $this->actingAs(materiInstructor())
        ->patch(route('admin.kegiatan.materi-kegiatan.update', [$materi->kegiatan, $materi]), [
            'judul' => $materi->judul,
            'deskripsi' => $materi->deskripsi,
            'file_materi' => materiFile('baru.pdf'),
        ])
        ->assertRedirect();

    $newPath = $materi->refresh()->file_materi;
    expect($newPath)->not->toBe('materi_kegiatan/lama.pdf');
    Storage::disk('local')->assertMissing('materi_kegiatan/lama.pdf');
    Storage::disk('local')->assertExists($newPath);
});

test('menghapus materi menghapus row pivot dan file privat', function () {
    Storage::fake('local');
    [$kader, $anggota] = materiKader();
    $materi = MateriKegiatan::factory()->create(['file_materi' => 'materi_kegiatan/hapus.pdf']);
    $anggota->materiTersimpan()->attach($materi);
    Storage::disk('local')->put($materi->file_materi, 'isi');

    $this->actingAs(materiInstructor())
        ->delete(route('admin.kegiatan.materi-kegiatan.destroy', [$materi->kegiatan, $materi]))
        ->assertRedirect();

    $this->assertModelMissing($materi);
    $this->assertDatabaseEmpty('materi_tersimpan');
    Storage::disk('local')->assertMissing('materi_kegiatan/hapus.pdf');
});

test('hapus materi membersihkan path terbaru dari row terkunci bukan path route model yang stale', function () {
    Storage::fake('local');
    $materi = MateriKegiatan::factory()->create(['file_materi' => 'materi_kegiatan/lama.pdf']);
    $staleMateri = clone $materi;
    Storage::disk('local')->put('materi_kegiatan/lama.pdf', 'lama');
    Storage::disk('local')->put('materi_kegiatan/baru.pdf', 'baru');

    MateriKegiatan::query()->whereKey($materi->id)->update(['file_materi' => 'materi_kegiatan/baru.pdf']);
    Storage::disk('local')->delete('materi_kegiatan/lama.pdf');

    $this->actingAs(materiInstructor())
        ->delete(route('admin.kegiatan.materi-kegiatan.destroy', [$materi->kegiatan, $staleMateri]))
        ->assertRedirect();

    $this->assertModelMissing($materi);
    Storage::disk('local')->assertMissing(['materi_kegiatan/lama.pdf', 'materi_kegiatan/baru.pdf']);
});

test('scoped route menolak materi dari kegiatan lain', function (string $method) {
    Storage::fake('local');
    $kegiatan = Kegiatan::factory()->create();
    $materi = MateriKegiatan::factory()->create();
    $route = route("admin.kegiatan.materi-kegiatan.{$method}", [$kegiatan, $materi]);
    $request = $this->actingAs(materiInstructor());

    $response = match ($method) {
        'edit' => $request->get($route),
        'update' => $request->put($route, ['judul' => 'Ubah', 'deskripsi' => 'Ubah']),
        'destroy' => $request->delete($route),
    };

    $response->assertNotFound();
})->with(['edit', 'update', 'destroy']);

test('hanya instruktur yang dapat memakai CRUD materi', function (string $role) {
    $kegiatan = Kegiatan::factory()->create();
    $user = User::factory()->{$role}()->create();

    $this->actingAs($user)
        ->get(route('admin.kegiatan.materi-kegiatan.index', $kegiatan))
        ->assertForbidden();
})->with(['admin', 'kader']);

test('guest diarahkan ke login dari CRUD materi', function () {
    $this->get(route('admin.kegiatan.materi-kegiatan.index', Kegiatan::factory()->create()))
        ->assertRedirect(route('login'));
});

test('upload materi menolak format dan ukuran yang tidak diizinkan tanpa membuat row', function (UploadedFile $file) {
    Storage::fake('local');

    $this->actingAs(materiInstructor())
        ->post(route('admin.kegiatan.materi-kegiatan.store', Kegiatan::factory()->create()), [
            'judul' => 'Materi Invalid',
            'deskripsi' => 'Tidak boleh disimpan.',
            'file_materi' => $file,
        ])
        ->assertSessionHasErrors('file_materi');

    expect(MateriKegiatan::count())->toBe(0)
        ->and(Storage::disk('local')->allFiles('materi_kegiatan'))->toBe([]);
})->with([
    'format gambar' => fn () => UploadedFile::fake()->image('materi.jpg'),
    'lebih dari 2 MiB' => fn () => materiFile('besar.pdf', 2049),
]);

test('upload materi menerima setiap format yang didukung', function (string $filename, string $mime) {
    Storage::fake('local');
    $kegiatan = Kegiatan::factory()->create();

    $this->actingAs(materiInstructor())
        ->post(route('admin.kegiatan.materi-kegiatan.store', $kegiatan), [
            'judul' => 'Materi '.strtoupper(pathinfo($filename, PATHINFO_EXTENSION)),
            'deskripsi' => 'Materi dalam format yang didukung.',
            'file_materi' => UploadedFile::fake()->create($filename, 100, $mime),
        ])
        ->assertRedirect(route('admin.kegiatan.materi-kegiatan.index', $kegiatan));

    $materi = MateriKegiatan::sole();
    expect(pathinfo($materi->file_materi, PATHINFO_EXTENSION))->toBe(pathinfo($filename, PATHINFO_EXTENSION));
    Storage::disk('local')->assertExists($materi->file_materi);
})->with([
    'pdf' => ['materi.pdf', 'application/pdf'],
    'doc' => ['materi.doc', 'application/msword'],
    'docx' => ['materi.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    'ppt' => ['materi.ppt', 'application/vnd.ms-powerpoint'],
    'pptx' => ['materi.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
    'xls' => ['materi.xls', 'application/vnd.ms-excel'],
    'xlsx' => ['materi.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
]);

test('kader hadir melihat dan mengunduh materi dengan header privat', function () {
    Storage::fake('local');
    [$user, $anggota] = materiKader();
    $materi = MateriKegiatan::factory()->create([
        'judul' => 'Materi Khusus Kader',
        'file_materi' => 'materi_kegiatan/khusus.pdf',
    ]);
    Presensi::factory()->hadir()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $materi->kegiatan_id,
    ]);
    Storage::disk('local')->put($materi->file_materi, 'isi materi');

    $this->actingAs($user)
        ->get(route('kader.materi.index'))
        ->assertSuccessful()
        ->assertSee('Materi Khusus Kader');

    $response = $this->actingAs($user)->get(route('kader.materi.download', $materi));
    $response->assertSuccessful()
        ->assertHeader('Pragma', 'no-cache')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
    expect($response->headers->get('Content-Disposition'))->toContain('attachment')
        ->and($response->headers->get('Cache-Control'))->toContain('private', 'no-store', 'max-age=0');
});

test('status selain hadir tidak memberi akses melihat menyimpan atau mengunduh', function (?string $status) {
    Storage::fake('local');
    [$user, $anggota] = materiKader();
    $materi = MateriKegiatan::factory()->create();

    if ($status) {
        Presensi::factory()->create([
            'anggota_id' => $anggota->id,
            'kegiatan_id' => $materi->kegiatan_id,
            'status_kehadiran' => $status,
        ]);
    }

    $this->actingAs($user)->get(route('kader.materi.index'))
        ->assertSuccessful()
        ->assertDontSee($materi->judul);
    $this->actingAs($user)->post(route('kader.materi.save', $materi))->assertForbidden();
    $this->actingAs($user)->get(route('kader.materi.download', $materi))->assertForbidden();
})->with(['izin', 'alfa', 'tanpa presensi' => null]);

test('presensi kader lain tidak memberi akses', function () {
    [$user] = materiKader();
    [, $anggotaLain] = materiKader();
    $materi = MateriKegiatan::factory()->create();
    Presensi::factory()->hadir()->create([
        'anggota_id' => $anggotaLain->id,
        'kegiatan_id' => $materi->kegiatan_id,
    ]);

    $this->actingAs($user)->post(route('kader.materi.save', $materi))->assertForbidden();
    $this->actingAs($user)->get(route('kader.materi.download', $materi))->assertForbidden();
});

test('simpan materi idempotent dan tampil pada halaman tersimpan', function () {
    [$user, $anggota] = materiKader();
    $materi = MateriKegiatan::factory()->create();
    Presensi::factory()->hadir()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $materi->kegiatan_id,
    ]);

    $this->actingAs($user)->post(route('kader.materi.save', $materi))->assertRedirect();
    $this->actingAs($user)->post(route('kader.materi.save', $materi))->assertRedirect();

    $this->assertDatabaseCount('materi_tersimpan', 1);
    $this->actingAs($user)->get(route('kader.materi.saved.index'))
        ->assertSuccessful()
        ->assertSee($materi->judul);
});

test('upsert materi tersimpan mempertahankan waktu pertama kali disimpan', function () {
    [$user, $anggota] = materiKader();
    $materi = MateriKegiatan::factory()->create();
    Presensi::factory()->hadir()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $materi->kegiatan_id,
    ]);

    $this->actingAs($user)->post(route('kader.materi.save', $materi))->assertRedirect();
    $createdAt = DB::table('materi_tersimpan')->value('created_at');
    $this->travel(1)->second();
    $this->actingAs($user)->post(route('kader.materi.save', $materi))->assertRedirect();

    $pivot = DB::table('materi_tersimpan')->sole();
    expect($pivot->created_at)->toBe($createdAt)
        ->and($pivot->updated_at)->not->toBe($createdAt);
    $this->assertDatabaseCount('materi_tersimpan', 1);
});

test('perubahan presensi mencabut akses materi tersimpan dan unduhan', function () {
    Storage::fake('local');
    [$user, $anggota] = materiKader();
    $materi = MateriKegiatan::factory()->create();
    $presensi = Presensi::factory()->hadir()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $materi->kegiatan_id,
    ]);
    $anggota->materiTersimpan()->attach($materi);
    Storage::disk('local')->put($materi->file_materi, 'isi');
    $presensi->update(['status_kehadiran' => 'alfa']);

    $this->actingAs($user)->get(route('kader.materi.saved.index'))
        ->assertSuccessful()
        ->assertDontSee($materi->judul);
    $this->actingAs($user)->get(route('kader.materi.download', $materi))->assertForbidden();
});

test('file materi yang hilang menghasilkan 404 untuk kader hadir', function () {
    Storage::fake('local');
    [$user, $anggota] = materiKader();
    $materi = MateriKegiatan::factory()->create();
    Presensi::factory()->hadir()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $materi->kegiatan_id,
    ]);

    $this->actingAs($user)->get(route('kader.materi.download', $materi))->assertNotFound();
});

test('menghapus kegiatan membersihkan seluruh file materi privat', function () {
    Storage::fake('local');
    $kegiatan = Kegiatan::factory()->create();
    $materis = MateriKegiatan::factory()->count(2)->sequence(
        ['file_materi' => 'materi_kegiatan/satu.pdf'],
        ['file_materi' => 'materi_kegiatan/dua.pdf'],
    )->create(['kegiatan_id' => $kegiatan->id]);
    $materis->each(fn ($materi) => Storage::disk('local')->put($materi->file_materi, 'isi'));

    $this->actingAs(materiInstructor())
        ->delete(route('admin.kegiatan.destroy', $kegiatan))
        ->assertRedirect(route('admin.kegiatan.index'));

    $this->assertModelMissing($kegiatan);
    expect(MateriKegiatan::count())->toBe(0);
    Storage::disk('local')->assertMissing(['materi_kegiatan/satu.pdf', 'materi_kegiatan/dua.pdf']);
});

test('hapus kegiatan mengambil snapshot materi terbaru dari row terkunci', function () {
    Storage::fake('local');
    $kegiatan = Kegiatan::factory()->create();
    $staleKegiatan = clone $kegiatan;
    $materi = MateriKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'file_materi' => 'materi_kegiatan/terbaru.pdf',
    ]);
    Storage::disk('local')->put($materi->file_materi, 'isi');

    app(KegiatanController::class)->destroy($staleKegiatan);

    $this->assertModelMissing($kegiatan);
    $this->assertModelMissing($materi);
    Storage::disk('local')->assertMissing('materi_kegiatan/terbaru.pdf');
});

test('kegagalan exists saat cleanup materi tidak membatalkan penghapusan row', function () {
    $materi = MateriKegiatan::factory()->create(['file_materi' => 'materi_kegiatan/gagal.pdf']);
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('exists')->once()->andThrow(new RuntimeException('storage unavailable'));
    Storage::shouldReceive('disk')->once()->with('local')->andReturn($disk);

    $this->actingAs(materiInstructor())
        ->delete(route('admin.kegiatan.materi-kegiatan.destroy', [$materi->kegiatan, $materi]))
        ->assertRedirect();

    $this->assertModelMissing($materi);
});

test('kegagalan exists saat cleanup kegiatan tidak membatalkan penghapusan row', function () {
    $kegiatan = Kegiatan::factory()->create();
    $materi = MateriKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'file_materi' => 'materi_kegiatan/gagal.pdf',
    ]);
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('exists')->once()->andThrow(new RuntimeException('storage unavailable'));
    Storage::shouldReceive('disk')->once()->with('local')->andReturn($disk);

    app(KegiatanController::class)->destroy($kegiatan);

    $this->assertModelMissing($kegiatan);
    $this->assertModelMissing($materi);
});
