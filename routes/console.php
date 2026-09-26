<?php

use App\Models\Anggota;
use App\Models\Arsip;
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

Artisan::command('images:webp {--dry-run : Tampilkan file yang akan dikonversi tanpa mengubah apa pun}', function (ProfilePhoto $converter) {
    // ponytail: Presensi.bukti_kehadiran is historical data and deliberately left out.
    $targets = [
        [Anggota::class, 'foto_profil', 'public'],
        [Kegiatan::class, 'thumbnail', 'public'],
        [Pendaftaran::class, 'file_persyaratan', 'local'],
        [LaporanKegiatan::class, 'file_lampiran', 'local'],
        [Arsip::class, 'file_arsip', 'local'],
    ];
    $converted = [];

    foreach ($targets as [$model, $column, $disk]) {
        $table = (new $model)->getTable();
        $storage = Storage::disk($disk);
        $paths = DB::table($table)->whereNotNull($column)->where($column, 'not like', '%.webp')->distinct()->pluck($column);

        foreach ($paths as $path) {
            $mime = $storage->exists($path) ? (string) $storage->mimeType($path) : '';

            if (! str_starts_with($mime, 'image/') || $mime === 'image/webp') {
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("[dry-run] {$disk}:{$path}");

                continue;
            }

            try {
                $newPath = $converter->store(new UploadedFile($storage->path($path), basename($path)), dirname($path), $disk, $column);
            } catch (ValidationException) {
                $this->warn("Gagal: {$disk}:{$path}");

                continue;
            }

            // Old file is removed only after every reference points at the new one.
            DB::table($table)->where($column, $path)->update([$column => $newPath]);
            $converted[] = [$storage, $path];
            $this->line("{$disk}:{$path} -> {$newPath}");
        }
    }

    // Landing cache may still hold old thumbnail paths; drop it before those files disappear.
    Cache::forget('kegiatan.terbaru');

    foreach ($converted as [$storage, $path]) {
        $storage->delete($path);
    }

    $this->info(count($converted).' gambar dikonversi ke WebP.');
})->purpose('Convert stored jpg/png uploads to WebP and update their database paths');
