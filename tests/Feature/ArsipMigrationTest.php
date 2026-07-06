<?php

use App\Models\Anggota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('migration memindahkan arsip publik ke disk privat dan backfill kategori lama', function () {
    Storage::fake('public');
    Storage::fake('local');

    $anggota = Anggota::factory()->create();
    Storage::disk('public')->put('arsip/lama.pdf', 'dokumen lama');

    DB::table('arsip')->insert([
        'anggota_id' => $anggota->id,
        'nomor_dokumen' => 'OLD-001',
        'judul_dokumen' => 'Dokumen Lama',
        'kategori_arsip' => 'laporan',
        'file_arsip' => 'arsip/lama.pdf',
        'tanggal_unggah' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $migration = include database_path('migrations/2026_07_06_223556_migrate_arsip_files_to_private_disk_and_backfill_categories.php');
    $migration->up();

    expect(DB::table('arsip')->where('judul_dokumen', 'Dokumen Lama')->value('kategori_arsip'))
        ->toBe('lpj');

    Storage::disk('local')->assertExists('arsip/lama.pdf');
    Storage::disk('public')->assertMissing('arsip/lama.pdf');
    expect(Storage::disk('local')->get('arsip/lama.pdf'))->toBe('dokumen lama');
});
