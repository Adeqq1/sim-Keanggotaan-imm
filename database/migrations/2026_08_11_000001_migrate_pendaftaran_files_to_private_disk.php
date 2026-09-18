<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $public = Storage::disk('public');
        $local = Storage::disk('local');

        DB::table('pendaftaran')
            ->whereNotNull('file_persyaratan')
            ->chunkById(100, function ($pendaftarans) use ($public, $local): void {
                foreach ($pendaftarans as $pendaftaran) {
                    $path = ltrim((string) $pendaftaran->file_persyaratan, '/');

                    if ($path === '' || ! $public->exists($path)) {
                        continue;
                    }

                    $publicContents = $public->get($path);

                    if (! is_string($publicContents)) {
                        throw new RuntimeException('Dokumen pendaftaran publik gagal dibaca.');
                    }

                    if (! $local->exists($path)) {
                        if (! $local->put($path, $publicContents) || ! $local->exists($path)) {
                            throw new RuntimeException('Dokumen pendaftaran gagal disalin ke penyimpanan privat.');
                        }
                    }

                    $localContents = $local->get($path);

                    if (! is_string($localContents) || hash('sha256', $publicContents) !== hash('sha256', $localContents)) {
                        throw new RuntimeException('Salinan dokumen pendaftaran tidak cocok dengan sumbernya.');
                    }

                    if (! $public->delete($path)) {
                        throw new RuntimeException('Dokumen pendaftaran publik gagal dihapus.');
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way security migration: never republish identity documents.
    }
};
