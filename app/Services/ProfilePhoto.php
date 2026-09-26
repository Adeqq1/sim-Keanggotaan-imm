<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use RuntimeException;
use Throwable;

// Converts any uploaded image to WebP; used for profile photos, thumbnails and private image uploads.
class ProfilePhoto
{
    public function store(UploadedFile $file, string $directory = 'foto_profil', string $diskName = 'public', string $field = 'foto_profil'): string
    {
        $disk = Storage::disk($diskName);
        $path = null;

        try {
            $driver = new Driver;

            if (! function_exists('imagewebp') || ! $driver->supports(Format::WEBP)) {
                throw new RuntimeException('WebP tidak didukung oleh GD.');
            }

            // ponytail: GD needs ~5 bytes/pixel and a memory-limit fatal cannot be caught; 25 MP fits in 256M.
            [$width, $height] = @getimagesize($file->getPathname()) ?: [0, 0];

            if ($width * $height > 25_000_000) {
                throw new RuntimeException('Resolusi gambar melebihi 25 MP.');
            }

            $image = (new ImageManager($driver))->decodePath($file->getPathname());
            $encoded = $image->encodeUsingFormat(Format::WEBP);
            $path = $directory.'/'.Str::uuid().'.webp';

            if (! $disk->put($path, (string) $encoded)) {
                throw new RuntimeException('Gagal menyimpan gambar WebP.');
            }

            return $path;
        } catch (Throwable $exception) {
            if ($path !== null) {
                try {
                    if ($disk->exists($path) && ! $disk->delete($path)) {
                        report(new RuntimeException('File WebP gagal dibersihkan.', 0, $exception));
                    }
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            report($exception);

            throw ValidationException::withMessages([
                $field => $field === 'foto_profil'
                    ? 'Foto profil gagal diproses. Silakan coba file lain.'
                    : 'Gambar gagal diproses atau resolusinya terlalu besar (maks. 25 MP). Silakan coba file lain.',
            ]);
        }
    }
}
