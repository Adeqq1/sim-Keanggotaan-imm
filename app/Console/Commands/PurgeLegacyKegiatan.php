<?php

namespace App\Console\Commands;

use App\Models\Kegiatan;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Signature('kegiatan:purge-legacy {--yes : Confirm destructive deletion}')]
#[Description('Delete legacy activities and their related database records and files.')]
class PurgeLegacyKegiatan extends Command
{
    public function handle(): int
    {
        $count = Kegiatan::count();
        if (! $this->option('yes')) {
            $this->warn("This will permanently delete {$count} activities and related records/files.");
            $this->line('Run with --yes after taking a database and storage backup.');
            return self::FAILURE;
        }

        $files = [];
        Kegiatan::with(['materiKegiatans', 'laporanKegiatan', 'sertifikat'])->chunkById(100, function ($kegiatans) use (&$files): void {
            foreach ($kegiatans as $kegiatan) {
                $files[] = ['disk' => 'public', 'path' => $kegiatan->thumbnail];
                foreach ($kegiatan->sertifikat as $sertifikat) {
                    $files[] = ['disk' => 'public', 'path' => $sertifikat->file_sertifikat];
                }
                foreach ($kegiatan->materiKegiatans as $materi) {
                    $files[] = ['disk' => 'local', 'path' => $materi->file_materi];
                }
                if ($kegiatan->laporanKegiatan) {
                    $files[] = ['disk' => 'local', 'path' => $kegiatan->laporanKegiatan->file_lampiran];
                }
            }
        });

        DB::transaction(fn () => Kegiatan::query()->delete());
        foreach ($files as $file) {
            if ($file['path']) {
                Storage::disk($file['disk'])->delete($file['path']);
            }
        }
        Cache::forget('kegiatan.terbaru');
        $this->info("Deleted {$count} legacy activities.");
        return self::SUCCESS;
    }
}
