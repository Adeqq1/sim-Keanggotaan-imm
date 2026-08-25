<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function instrukturSidebarXPath(string $content): DOMXPath
{
    $dom = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_use_internal_errors($previous);

    return new DOMXPath($dom);
}

function instrukturSidebarSublink(DOMXPath $xpath, string $href): ?DOMElement
{
    $nodes = $xpath->query(
        '//a[contains(concat(" ", normalize-space(@class), " "), " sidebar-sublink ")][@href="'.$href.'"]'
    );

    return $nodes->length === 1 ? $nodes->item(0) : null;
}

function instrukturActiveSublinkCount(DOMXPath $xpath): int
{
    return $xpath->query(
        '//a[contains(concat(" ", normalize-space(@class), " "), " sidebar-sublink ")][contains(concat(" ", normalize-space(@class), " "), " active ")]'
    )->length;
}

test('instruktur redirects to admin.kegiatan.index on login', function () {
    $instruktur = User::factory()->instruktur()->create();

    $response = $this->post(route('login'), [
        'email' => $instruktur->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.kegiatan.index'));
});

test('instruktur sidebar shows kegiatan submenu with correct links', function () {
    $instruktur = User::factory()->instruktur()->create();

    $response = $this->actingAs($instruktur)
        ->get(route('admin.kegiatan.index'))
        ->assertOk()
        ->assertSee('aria-label="Navigasi kegiatan"', false)
        ->assertSeeText('Daftar Kegiatan')
        ->assertSeeText('Buat Kegiatan Baru')
        ->assertSeeText('Rekap Presensi');

    $xpath = instrukturSidebarXPath($response->getContent());

    foreach ([
        route('admin.kegiatan.index') => 'Daftar Kegiatan',
        route('admin.kegiatan.create') => 'Buat Kegiatan Baru',
        route('admin.presensi.index') => 'Rekap Presensi',
    ] as $href => $text) {
        $link = instrukturSidebarSublink($xpath, $href);
        expect($link)->not->toBeNull()
            ->and($link->textContent)->toContain($text);
    }

    $daftar = instrukturSidebarSublink($xpath, route('admin.kegiatan.index'));
    expect($daftar->getAttribute('class'))->toContain('sidebar-sublink active')
        ->and($daftar->getAttribute('aria-current'))->toBe('page');

    expect(instrukturActiveSublinkCount($xpath))->toBe(1);
});

test('instruktur sidebar marks kegiatan create submenu as active', function () {
    $instruktur = User::factory()->instruktur()->create();

    $content = $this->actingAs($instruktur)
        ->get(route('admin.kegiatan.create'))
        ->assertOk()
        ->getContent();

    $xpath = instrukturSidebarXPath($content);
    $create = instrukturSidebarSublink($xpath, route('admin.kegiatan.create'));

    expect($create)->not->toBeNull()
        ->and($create->textContent)->toContain('Buat Kegiatan Baru')
        ->and($create->getAttribute('class'))->toContain('sidebar-sublink active')
        ->and($create->getAttribute('aria-current'))->toBe('page');

    expect(instrukturActiveSublinkCount($xpath))->toBe(1);
});

test('instruktur sidebar marks presensi submenu as active', function () {
    $instruktur = User::factory()->instruktur()->create();

    $content = $this->actingAs($instruktur)
        ->get(route('admin.presensi.index'))
        ->assertOk()
        ->getContent();

    $xpath = instrukturSidebarXPath($content);
    $presensi = instrukturSidebarSublink($xpath, route('admin.presensi.index'));

    expect($presensi)->not->toBeNull()
        ->and($presensi->textContent)->toContain('Rekap Presensi')
        ->and($presensi->getAttribute('class'))->toContain('sidebar-sublink active')
        ->and($presensi->getAttribute('aria-current'))->toBe('page');

    expect(instrukturActiveSublinkCount($xpath))->toBe(1);
});

test('instruktur can access kegiatan management and store new kegiatan with thumbnail', function () {
    Storage::fake('public');
    $instruktur = User::factory()->instruktur()->create();

    $response = $this->actingAs($instruktur)->get(route('admin.kegiatan.index'));
    $response->assertOk();

    $file = UploadedFile::fake()->create('thumbnail.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($instruktur)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Latihan Kader',
        'deskripsi' => 'Deskripsi latihan kader',
        'tanggal_waktu' => now()->addDays(2)->setTime(10, 0)->format('Y-m-d H:i'),
        'lokasi' => 'Aula IMM',
        'tahun_angkatan' => [now()->year],
        'jenis_pelaksanaan' => 'satu_sesi',
        'minimum_sesi_terverifikasi' => 1,
        'thumbnail' => $file,
    ]);

    $response->assertRedirect(route('admin.kegiatan.index'));

    $kegiatan = Kegiatan::where('nama_kegiatan', 'Latihan Kader')->first();
    expect($kegiatan)->not->toBeNull();
    expect($kegiatan->thumbnail)->not->toBeNull();

    Storage::disk('public')->assertExists($kegiatan->thumbnail);
});

test('kegiatan requires at least one target member year and stores multiple years', function () {
    $instruktur = User::factory()->instruktur()->create();

    $this->actingAs($instruktur)
        ->post(route('admin.kegiatan.store'), [
            'nama_kegiatan' => 'Tanpa Target Angkatan',
            'tanggal_waktu' => now()->addDays(2)->setTime(10, 0)->format('Y-m-d H:i'),
            'lokasi' => 'Aula IMM',
            'jenis_pelaksanaan' => 'satu_sesi',
            'minimum_sesi_terverifikasi' => 1,
        ])
        ->assertSessionHasErrors('tahun_angkatan');

    $this->actingAs($instruktur)
        ->post(route('admin.kegiatan.store'), [
            'nama_kegiatan' => 'Target Dua Angkatan',
            'tanggal_waktu' => now()->addDays(2)->setTime(10, 0)->format('Y-m-d H:i'),
            'lokasi' => 'Aula IMM',
            'jenis_pelaksanaan' => 'satu_sesi',
            'minimum_sesi_terverifikasi' => 1,
            'tahun_angkatan' => [2025, 2026],
        ])
        ->assertRedirect(route('admin.kegiatan.index'));

    $kegiatan = Kegiatan::where('nama_kegiatan', 'Target Dua Angkatan')->firstOrFail();
    expect($kegiatan->tahunAngkatans()->pluck('tahun_daftar')->all())->toBe([2025, 2026]);
});

test('kegiatan rejects past schedules and accepts a future split schedule', function () {
    $instruktur = User::factory()->instruktur()->create();
    $payload = [
        'nama_kegiatan' => 'Jadwal Aman',
        'lokasi' => 'Aula IMM',
        'jenis_pelaksanaan' => 'satu_sesi',
        'minimum_sesi_terverifikasi' => 1,
        'tahun_angkatan' => [now()->year],
    ];

    $this->actingAs($instruktur)
        ->post(route('admin.kegiatan.store'), $payload + [
            'tanggal_kegiatan' => now()->subDay()->toDateString(),
            'waktu_mulai' => now()->format('H:i'),
        ])
        ->assertSessionHasErrors('tanggal_waktu');

    $this->actingAs($instruktur)
        ->post(route('admin.kegiatan.store'), $payload + [
            'tanggal_kegiatan' => now()->addDay()->toDateString(),
            'waktu_mulai' => '09:00',
        ])
        ->assertRedirect(route('admin.kegiatan.index'));

    expect(App\Models\Kegiatan::where('nama_kegiatan', 'Jadwal Aman')->value('tanggal_waktu')->format('Y-m-d H:i'))
        ->toBe(now()->addDay()->format('Y-m-d').' 09:00');
});

test('instruktur can update kegiatan and replace thumbnail', function () {
    Storage::fake('public');
    $instruktur = User::factory()->instruktur()->create();

    $oldFile = UploadedFile::fake()->create('old.jpg', 100, 'image/jpeg');
    $oldPath = $oldFile->store('kegiatan_thumbnails', 'public');

    $kegiatan = Kegiatan::factory()->create([
        'thumbnail' => $oldPath,
    ]);

    Storage::disk('public')->assertExists($oldPath);

    $newFile = UploadedFile::fake()->create('new.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($instruktur)->put(route('admin.kegiatan.update', $kegiatan), [
        'nama_kegiatan' => 'Latihan Kader Updated',
        'tanggal_waktu' => now()->addDays(3)->setTime(10, 0)->format('Y-m-d H:i'),
        'lokasi' => 'Aula IMM Baru',
        'tahun_angkatan' => [now()->year],
        'jenis_pelaksanaan' => 'satu_sesi',
        'minimum_sesi_terverifikasi' => 1,
        'thumbnail' => $newFile,
    ]);

    $response->assertRedirect(route('admin.kegiatan.index'));

    $kegiatan->refresh();
    expect($kegiatan->nama_kegiatan)->toBe('Latihan Kader Updated');
    expect($kegiatan->thumbnail)->not->toBe($oldPath);

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($kegiatan->thumbnail);
});

test('instruktur can delete kegiatan and its thumbnail', function () {
    Storage::fake('public');
    $instruktur = User::factory()->instruktur()->create();

    $file = UploadedFile::fake()->create('thumb.jpg', 100, 'image/jpeg');
    $path = $file->store('kegiatan_thumbnails', 'public');

    $kegiatan = Kegiatan::factory()->create([
        'thumbnail' => $path,
    ]);

    Storage::disk('public')->assertExists($path);

    $response = $this->actingAs($instruktur)->delete(route('admin.kegiatan.destroy', $kegiatan));

    $response->assertRedirect(route('admin.kegiatan.index'));

    $this->assertDatabaseMissing('kegiatan', ['id' => $kegiatan->id]);
    Storage::disk('public')->assertMissing($path);
});

test('instruktur can view and store presensi data immediately', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->withDefaultSession()->create();
    $anggota1 = Anggota::factory()->create(['tahun_daftar' => now()->year]);
    $anggota2 = Anggota::factory()->create(['tahun_daftar' => now()->year]);

    $this->actingAs($instruktur)
        ->get(route('admin.presensi.sesi.show', [$kegiatan, $kegiatan->sesiKegiatans()->first()]))
        ->assertSuccessful()
        ->assertSee('Simpan Presensi')
        ->assertSee('name="presensi['.$anggota1->id.'][status_kehadiran]"', false)
        ->assertSee('value="alfa" checked', false);

    $response = $this->actingAs($instruktur)
        ->post(route('admin.presensi.store', $kegiatan), [
            'presensi' => [
                [
                    'anggota_id' => $anggota1->id,
                    'status_kehadiran' => 'hadir',
                ],
                [
                    'anggota_id' => $anggota2->id,
                    'status_kehadiran' => 'izin',
                ],
            ],
        ]);

    $response->assertRedirect(route('admin.kegiatan.index'))
        ->assertSessionHas('success', 'Presensi berhasil disimpan.');

    $presensiHadir = Presensi::where('kegiatan_id', $kegiatan->id)
        ->where('anggota_id', $anggota1->id)
        ->firstOrFail();
    $presensiIzin = Presensi::where('kegiatan_id', $kegiatan->id)
        ->where('anggota_id', $anggota2->id)
        ->firstOrFail();

    expect($presensiHadir->status_kehadiran)->toBe('hadir')
        ->and($presensiHadir->waktu_hadir)->not->toBeNull()
        ->and($presensiIzin->status_kehadiran)->toBe('izin')
        ->and($presensiIzin->waktu_hadir)->toBeNull();
    expect(Sertifikat::where('kegiatan_id', $kegiatan->id)->count())->toBe(0);
});

test('presensi is limited to the activity target member years', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->withDefaultSession()->create();
    $target = Anggota::factory()->create(['tahun_daftar' => now()->year]);
    $other = Anggota::factory()->create(['tahun_daftar' => now()->year - 1]);
    $sesi = $kegiatan->sesiKegiatans()->first();

    $this->actingAs($instruktur)
        ->get(route('admin.presensi.sesi.show', [$kegiatan, $sesi]))
        ->assertSee($target->nama_lengkap)
        ->assertDontSee($other->nama_lengkap);

    $this->actingAs($instruktur)
        ->post(route('admin.presensi.store', [$kegiatan, $sesi]), [
            'presensi' => [[
                'anggota_id' => $other->id,
                'status_kehadiran' => 'hadir',
            ]],
        ])
        ->assertSessionHasErrors('presensi.0.anggota_id');

    expect(Presensi::where('kegiatan_id', $kegiatan->id)->exists())->toBeFalse();
});

test('repeated presensi submissions do not create duplicates', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota = Anggota::factory()->create();
    $payload = [
        'presensi' => [[
            'anggota_id' => $anggota->id,
            'status_kehadiran' => 'hadir',
        ]],
    ];

    $this->actingAs($instruktur)->post(route('admin.presensi.store', $kegiatan), $payload)
        ->assertRedirect(route('admin.kegiatan.index'));
    $this->actingAs($instruktur)->post(route('admin.presensi.store', $kegiatan), $payload)
        ->assertRedirect(route('admin.kegiatan.index'));

    expect(Presensi::where('kegiatan_id', $kegiatan->id)
        ->where('anggota_id', $anggota->id)
        ->count())->toBe(1);
    expect(Presensi::where('kegiatan_id', $kegiatan->id)
        ->where('anggota_id', $anggota->id)
        ->value('status_kehadiran'))->toBe('hadir');
});

test('invalid presensi payload is rejected without creating records', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota = Anggota::factory()->create();

    $this->actingAs($instruktur)
        ->post(route('admin.presensi.store', $kegiatan), [
            'presensi' => [[
                'anggota_id' => $anggota->id,
                'status_kehadiran' => 'tidak_hadir',
            ]],
        ])
        ->assertSessionHasErrors('presensi.0.status_kehadiran');

    $this->actingAs($instruktur)
        ->post(route('admin.presensi.store', $kegiatan), [
            'presensi' => [[
                'anggota_id' => 999999,
                'status_kehadiran' => 'hadir',
            ]],
        ])
        ->assertSessionHasErrors('presensi.0.anggota_id');

    $this->actingAs($instruktur)
        ->post(route('admin.presensi.store', $kegiatan), [
            'presensi' => [
                ['anggota_id' => $anggota->id, 'status_kehadiran' => 'hadir'],
                ['anggota_id' => $anggota->id, 'status_kehadiran' => 'izin'],
            ],
        ])
        ->assertSessionHasErrors('presensi.1.anggota_id');

    expect(Presensi::where('kegiatan_id', $kegiatan->id)->count())->toBe(0);
});

test('inactive anggota cannot receive presensi', function () {
    $instruktur = User::factory()->instruktur()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota = Anggota::factory()->inactive()->create();

    $response = $this->actingAs($instruktur)->post(route('admin.presensi.store', $kegiatan), [
        'presensi' => [[
            'anggota_id' => $anggota->id,
            'status_kehadiran' => 'hadir',
        ]],
    ]);

    $response->assertSessionHasErrors('presensi.0.anggota_id');
    $this->assertDatabaseMissing('presensi', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota->id,
    ]);
});

test('kader cannot access kegiatan and presensi management', function () {
    $kader = User::factory()->kader()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota = Anggota::factory()->create();

    $response = $this->actingAs($kader)->get(route('admin.kegiatan.index'));
    $response->assertForbidden();

    $response = $this->actingAs($kader)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Illegal',
        'tanggal_waktu' => '2026-06-10 10:00:00',
        'lokasi' => 'Aula',
    ]);
    $response->assertForbidden();

    $response = $this->actingAs($kader)->get(route('admin.presensi.show', $kegiatan->id));
    $response->assertForbidden();

    $response = $this->actingAs($kader)->post(route('admin.presensi.store', $kegiatan), [
        'presensi' => [[
            'anggota_id' => $anggota->id,
            'status_kehadiran' => 'hadir',
        ]],
    ]);
    $response->assertForbidden();

    expect(Presensi::where('kegiatan_id', $kegiatan->id)->count())->toBe(0);
});

test('invalid thumbnail upload is rejected', function () {
    $instruktur = User::factory()->instruktur()->create();

    $badFile = UploadedFile::fake()->create('malware.exe', 500);

    $response = $this->actingAs($instruktur)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Latihan Kader',
        'tanggal_waktu' => '2026-06-10 10:00:00',
        'lokasi' => 'Aula IMM',
        'thumbnail' => $badFile,
    ]);

    $response->assertSessionHasErrors('thumbnail');

    $largeFile = UploadedFile::fake()->create('large.jpg', 3000, 'image/jpeg');

    $response = $this->actingAs($instruktur)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Latihan Kader 2',
        'tanggal_waktu' => '2026-06-10 10:00:00',
        'lokasi' => 'Aula IMM 2',
        'thumbnail' => $largeFile,
    ]);

    $response->assertSessionHasErrors('thumbnail');
});
