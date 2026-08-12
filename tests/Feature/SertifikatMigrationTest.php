<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Sertifikat;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

test('certificate migration keeps the newest row with an existing PDF', function () {
    Storage::fake('public');
    Schema::table('sertifikat', function (Blueprint $table) {
        $table->dropUnique('sertifikat_kegiatan_anggota_unique');
    });

    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();
    Storage::disk('public')->put('sertifikat/old.pdf', 'old certificate');
    Storage::disk('public')->put('sertifikat/new.pdf', 'new certificate');
    $old = Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
        'file_sertifikat' => 'sertifikat/old.pdf',
        'created_at' => now()->subMinutes(2),
        'updated_at' => now()->subMinutes(2),
    ]);
    $existingNew = Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
        'file_sertifikat' => 'sertifikat/new.pdf',
        'created_at' => now()->subMinute(),
        'updated_at' => now()->subMinute(),
    ]);
    $missingNewest = Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
        'file_sertifikat' => 'sertifikat/missing.pdf',
    ]);

    $migration = include database_path('migrations/2026_08_12_144520_add_kegiatan_anggota_unique_index_to_sertifikat_table.php');
    $migration->up();

    expect(Sertifikat::where('kegiatan_id', $kegiatan->id)
        ->where('anggota_id', $anggota->id)
        ->pluck('id')->all())->toBe([$existingNew->id]);
    Storage::disk('public')->assertExists('sertifikat/old.pdf');
    Storage::disk('public')->assertExists('sertifikat/new.pdf');
    expect(DB::table('sertifikat')->whereIn('id', [$old->id, $missingNewest->id])->exists())->toBeFalse();
});

test('certificate migration enforces one certificate per activity and member', function () {
    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();
    Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);

    expect(fn () => Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]))->toThrow(QueryException::class);
});
