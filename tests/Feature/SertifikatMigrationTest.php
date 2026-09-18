<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Sertifikat;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('certificate migration refuses duplicate rows without deleting data', function () {
    Schema::table('sertifikat', function (Blueprint $table) {
        $table->dropUnique('sertifikat_kegiatan_anggota_unique');
    });

    $anggota = Anggota::factory()->create();
    $kegiatan = Kegiatan::factory()->create();
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
    expect(fn () => $migration->up())
        ->toThrow(\RuntimeException::class, 'duplicate activity/member pairs require manual reconciliation');

    expect(DB::table('sertifikat')->whereIn('id', [$old->id, $existingNew->id, $missingNewest->id])->count())->toBe(3);
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
