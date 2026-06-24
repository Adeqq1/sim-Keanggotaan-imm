<?php

use App\Models\Anggota;
use App\Models\User;
use App\Services\NiaGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ============================================================
// NiaGenerator Service Unit Tests
// ============================================================

describe('NiaGenerator::generateNextNia', function () {
    test('menghasilkan NIA pertama dengan sequence 0001 jika belum ada NIA untuk tahun tersebut', function () {
        $generator = new NiaGenerator;
        $nia = $generator->generateNextNia('26');

        expect($nia)->toBe('24260001');
    });

    test('menghasilkan NIA berikutnya berurutan berdasarkan prefix tahun', function () {
        Anggota::factory()->tanpaNia()->create([
            'nia' => '24260001',
            'created_at' => Carbon::create(2026, 1, 1),
        ]);
        Anggota::factory()->tanpaNia()->create([
            'nia' => '24260002',
            'created_at' => Carbon::create(2026, 1, 2),
        ]);

        $generator = new NiaGenerator;
        $nia = $generator->generateNextNia('26');

        expect($nia)->toBe('24260003');
    });

    test('NIA berbeda tahun tidak saling mempengaruhi urutan', function () {
        Anggota::factory()->tanpaNia()->create([
            'nia' => '24240001',
            'created_at' => Carbon::create(2024, 1, 1),
        ]);

        $generator = new NiaGenerator;
        $nia2026 = $generator->generateNextNia('26');

        expect($nia2026)->toBe('24260001');
    });

    test('NIA yang dihasilkan tepat 8 karakter', function () {
        $generator = new NiaGenerator;
        $nia = $generator->generateNextNia('26');

        expect(strlen($nia))->toBe(8);
    });

    test('NIA yang dihasilkan hanya berisi angka', function () {
        $generator = new NiaGenerator;
        $nia = $generator->generateNextNia('26');

        expect($nia)->toMatch('/^[0-9]{8}$/');
    });
});

describe('NiaGenerator::generateForAnggota', function () {
    test('generate dan simpan NIA untuk anggota yang belum punya NIA', function () {
        $anggota = Anggota::factory()->tanpaNia()->create([
            'created_at' => Carbon::create(2026, 6, 1),
        ]);

        $generator = new NiaGenerator;
        $nia = $generator->generateForAnggota($anggota);

        expect($nia)->toMatch('/^24260\d{3}$/')
            ->and($anggota->fresh()->nia)->toBe($nia);
    });

    test('melempar RuntimeException jika anggota sudah punya NIA', function () {
        $anggota = Anggota::factory()->create([
            'nia' => '24260001',
        ]);

        $generator = new NiaGenerator;

        expect(fn () => $generator->generateForAnggota($anggota))
            ->toThrow(RuntimeException::class);
    });
});

// ============================================================
// HTTP Endpoint Tests
// ============================================================

describe('POST admin/anggota/{anggota}/generate-nia', function () {
    test('admin bisa generate NIA untuk anggota yang belum punya NIA', function () {
        $admin = User::factory()->admin()->create();
        $anggota = Anggota::factory()->tanpaNia()->create([
            'created_at' => Carbon::create(2026, 6, 1),
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.anggota.generate-nia', $anggota));

        $response->assertRedirect(route('admin.anggota.edit', $anggota));
        expect($anggota->fresh()->nia)->not->toBeNull()
            ->and(strlen($anggota->fresh()->nia))->toBe(8);
    });

    test('admin mendapat warning jika anggota sudah punya NIA', function () {
        $admin = User::factory()->admin()->create();
        $anggota = Anggota::factory()->create(['nia' => '24260001']);
        $niaBefore = $anggota->nia;

        $response = $this->actingAs($admin)
            ->post(route('admin.anggota.generate-nia', $anggota));

        $response->assertRedirect();
        $response->assertSessionHas('warning');
        expect($anggota->fresh()->nia)->toBe($niaBefore);
    });

    test('non-admin tidak bisa generate NIA', function () {
        $kader = User::factory()->create(['role' => 'kader']);
        $anggota = Anggota::factory()->tanpaNia()->create();

        $response = $this->actingAs($kader)
            ->post(route('admin.anggota.generate-nia', $anggota));

        $response->assertForbidden();
    });

    test('guest tidak bisa generate NIA', function () {
        $anggota = Anggota::factory()->tanpaNia()->create();

        $response = $this->post(route('admin.anggota.generate-nia', $anggota));

        $response->assertRedirect(route('login'));
    });
});

describe('POST admin/anggota/generate-nia-bulk', function () {
    test('admin bisa bulk generate NIA untuk anggota tanpa NIA', function () {
        $admin = User::factory()->admin()->create();
        $tanpaNia = Anggota::factory()->tanpaNia()->count(3)->create([
            'created_at' => Carbon::create(2026, 6, 1),
        ]);
        $denganNia = Anggota::factory()->create(['nia' => '24260099']);

        $response = $this->actingAs($admin)
            ->post(route('admin.anggota.generate-nia-bulk'));

        $response->assertRedirect(route('admin.anggota.index'));
        $response->assertSessionHas('success');

        foreach ($tanpaNia as $a) {
            expect($a->fresh()->nia)->not->toBeNull()
                ->and(strlen($a->fresh()->nia))->toBe(8);
        }
    });

    test('bulk generate tidak menimpa NIA yang sudah ada', function () {
        $admin = User::factory()->admin()->create();
        $anggota = Anggota::factory()->create(['nia' => '24260001']);

        $this->actingAs($admin)
            ->post(route('admin.anggota.generate-nia-bulk'));

        expect($anggota->fresh()->nia)->toBe('24260001');
    });

    test('bulk generate menampilkan pesan jika tidak ada anggota yang perlu diproses', function () {
        $admin = User::factory()->admin()->create();
        Anggota::factory()->create(['nia' => '24260001']);

        $response = $this->actingAs($admin)
            ->post(route('admin.anggota.generate-nia-bulk'));

        $response->assertSessionHas('success', 'Tidak ada anggota yang perlu di-generate NIA-nya.');
    });

    test('non-admin tidak bisa bulk generate NIA', function () {
        $kader = User::factory()->create(['role' => 'kader']);

        $response = $this->actingAs($kader)
            ->post(route('admin.anggota.generate-nia-bulk'));

        $response->assertForbidden();
    });
});
