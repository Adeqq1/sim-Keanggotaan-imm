<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use App\Models\Pendaftaran;
use App\Services\ProfilePhoto;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('images:webp {--dry-run : Tampilkan file yang akan dikonversi tanpa mengubah apa pun} {--force : Lewati konfirmasi di production}', function (ProfilePhoto $converter) {
    if (! $this->option('dry-run') && app()->isProduction() && ! $this->option('force')
        && ! $this->confirm('File asli akan DIHAPUS setelah dikonversi. Backup sudah dibuat?')) {
        return 1;
    }

    // ponytail: Arsip and Presensi.bukti_kehadiran are official/historical documents and deliberately left out.
    $targets = [
        [Anggota::class, 'foto_profil', 'public', 85],
        [Kegiatan::class, 'thumbnail', 'public', 85],
        [Pendaftaran::class, 'file_persyaratan', 'local', 90],
        [LaporanKegiatan::class, 'file_lampiran', 'local', 90],
    ];
    $converted = [];
    $failed = 0;

    foreach ($targets as [$model, $column, $disk, $quality]) {
        $table = (new $model)->getTable();
        $storage = Storage::disk($disk);
        $paths = DB::table($table)->whereNotNull($column)->where($column, 'not like', '%.webp')->distinct()->pluck($column);

        foreach ($paths as $path) {
            if (! str_contains($path, '/')) {
                $this->warn("Dilewati (tanpa folder): {$disk}:{$path}");

                continue;
            }

            $mime = $storage->exists($path) ? (string) $storage->mimeType($path) : '';

            if (! str_starts_with($mime, 'image/') || $mime === 'image/webp') {
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("[dry-run] {$disk}:{$path}");

                continue;
            }

            try {
                $newPath = $converter->store(new UploadedFile($storage->path($path), basename($path)), dirname($path), $disk, $column, $quality);
            } catch (ValidationException) {
                $this->warn("Gagal: {$disk}:{$path}");
                $failed++;

                continue;
            }

            try {
                // Old file is removed only after every reference points at the new one.
                DB::table($table)->where($column, $path)->update([$column => $newPath]);
            } catch (Throwable $exception) {
                $storage->delete($newPath);

                throw $exception;
            }

            $converted[] = [$storage, $path];
            $this->line("{$disk}:{$path} -> {$newPath}");
        }
    }

    // Landing cache may still hold old thumbnail paths; drop it before those files disappear.
    Cache::forget('kegiatan.terbaru');

    foreach ($converted as [$storage, $path]) {
        $storage->delete($path);
    }

    $this->info(count($converted).' gambar dikonversi ke WebP, '.$failed.' gagal.');

    return $failed > 0 ? 1 : 0;
})->purpose('Convert stored jpg/png uploads to WebP and update their database paths');
