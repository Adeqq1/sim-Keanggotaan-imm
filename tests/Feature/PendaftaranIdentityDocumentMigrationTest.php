<?php

use App\Models\Pendaftaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

function runPendaftaranFilesMigration(): void
{
    $migration = include database_path('migrations/2026_08_11_000001_migrate_pendaftaran_files_to_private_disk.php');
    $migration->up();
}

test('migration moves legacy registration files privately without guessing document type', function () {
    $path = 'pendaftaran/legacy.pdf';
    $contents = 'legacy registration document';
    $pendaftaran = Pendaftaran::factory()->create([
        'file_persyaratan' => $path,
        'jenis_dokumen_identitas' => null,
    ]);
    Storage::disk('public')->put($path, $contents);

    runPendaftaranFilesMigration();

    Storage::disk('local')->assertExists($path);
    Storage::disk('public')->assertMissing($path);
    expect(Storage::disk('local')->get($path))->toBe($contents)
        ->and(DB::table('pendaftaran')->where('id', $pendaftaran->id)->value('file_persyaratan'))->toBe($path)
        ->and(DB::table('pendaftaran')->where('id', $pendaftaran->id)->value('jenis_dokumen_identitas'))->toBeNull();
});

test('migration is idempotent for existing equal private files and leaves private-only files alone', function () {
    $equalPath = 'pendaftaran/equal.pdf';
    $privateOnlyPath = 'pendaftaran/private-only.pdf';
    $contents = 'same document';

    Pendaftaran::factory()->create(['file_persyaratan' => $equalPath]);
    Pendaftaran::factory()->create(['file_persyaratan' => $privateOnlyPath]);
    Storage::disk('public')->put($equalPath, $contents);
    Storage::disk('local')->put($equalPath, $contents);
    Storage::disk('local')->put($privateOnlyPath, 'private document');

    runPendaftaranFilesMigration();

    Storage::disk('public')->assertMissing($equalPath);
    Storage::disk('local')->assertExists($equalPath);
    Storage::disk('local')->assertExists($privateOnlyPath);
});

test('migration refuses to delete a public file when the private copy differs', function () {
    $path = 'pendaftaran/conflict.pdf';
    Pendaftaran::factory()->create(['file_persyaratan' => $path]);
    Storage::disk('public')->put($path, 'public contents');
    Storage::disk('local')->put($path, 'different private contents');

    expect(fn () => runPendaftaranFilesMigration())
        ->toThrow(RuntimeException::class);

    Storage::disk('public')->assertExists($path);
    Storage::disk('local')->assertExists($path);
    expect(Storage::disk('public')->get($path))->toBe('public contents')
        ->and(Storage::disk('local')->get($path))->toBe('different private contents');
});

test('migration down never republishes registration files', function () {
    $path = 'pendaftaran/private.pdf';
    Storage::disk('local')->put($path, 'private contents');

    $migration = include database_path('migrations/2026_08_11_000001_migrate_pendaftaran_files_to_private_disk.php');
    $migration->down();

    Storage::disk('local')->assertExists($path);
    Storage::disk('public')->assertMissing($path);
});
