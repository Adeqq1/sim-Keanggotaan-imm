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

class ProfilePhoto
{
    public function store(UploadedFile $file): string
    {
        $disk = Storage::disk('public');
        $path = null;

        try {
            $driver = new Driver;

            if (! function_exists('imagewebp') || ! $driver->supports(Format::WEBP)) {
                throw new RuntimeException('WebP tidak didukung oleh GD.');
            }

            $image = (new ImageManager($driver))->decodePath($file->getPathname());
            $encoded = $image->encodeUsingFormat(Format::WEBP);
            $path = 'foto_profil/'.Str::uuid().'.webp';

            if (! $disk->put($path, (string) $encoded)) {
                throw new RuntimeException('Gagal menyimpan foto profil WebP.');
            }

            return $path;
        } catch (Throwable $exception) {
            if ($path !== null) {
                try {
                    if ($disk->exists($path) && ! $disk->delete($path)) {
                        report(new RuntimeException('File foto profil WebP gagal dibersihkan.', 0, $exception));
                    }
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            report($exception);

            throw ValidationException::withMessages([
                'foto_profil' => 'Foto profil gagal diproses. Silakan coba file lain.',
            ]);
        }
    }
}
