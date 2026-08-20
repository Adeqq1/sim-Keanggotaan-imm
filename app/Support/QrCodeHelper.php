<?php

namespace App\Support;

use App\Models\Anggota;

class QrCodeHelper
{
    public static function generateDataUri(string $text, int $size = 120): string
    {
        $modules = 29;
        $matrix = array_fill(0, $modules, array_fill(0, $modules, false));

        foreach ([[0, 0], [$modules - 7, 0], [0, $modules - 7]] as [$startX, $startY]) {
            for ($y = -1; $y <= 7; $y++) {
                for ($x = -1; $x <= 7; $x++) {
                    $px = $startX + $x;
                    $py = $startY + $y;
                    if ($px >= 0 && $py >= 0 && $px < $modules && $py < $modules) {
                        $matrix[$py][$px] = $x >= 0 && $x <= 6 && $y >= 0 && $y <= 6
                            && ($x === 0 || $x === 6 || $y === 0 || $y === 6 || ($x >= 2 && $x <= 4 && $y >= 2 && $y <= 4));
                    }
                }
            }
        }

        $bits = unpack('C*', hash('sha512', $text, true));
        $bit = 0;
        for ($y = 0; $y < $modules; $y++) {
            for ($x = 0; $x < $modules; $x++) {
                if ($matrix[$y][$x] || (($x < 8 && $y < 8) || ($x >= $modules - 8 && $y < 8) || ($x < 8 && $y >= $modules - 8))) {
                    continue;
                }
                $matrix[$y][$x] = (($bits[($bit % count($bits)) + 1] >> ($bit % 8)) & 1) === 1;
                $bit++;
            }
        }

        $cell = $size / $modules;
        $rectangles = [];
        foreach ($matrix as $y => $row) {
            foreach ($row as $x => $dark) {
                if ($dark) {
                    $rectangles[] = sprintf('<rect x="%s" y="%s" width="%s" height="%s"/>', $x * $cell, $y * $cell, $cell + 0.1, $cell + 0.1);
                }
            }
        }

        $svg = sprintf('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d"><rect width="100%%" height="100%%" fill="white"/><g fill="black">%s</g></svg>', $size, $size, $size, $size, implode('', $rectangles));

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    public static function makeVerificationPayload(Anggota $anggota): string
    {
        $nia = filled($anggota->nia) ? $anggota->nia : 'BELUM_TERSEDIA';
        $nama = filled($anggota->nama_lengkap) ? $anggota->nama_lengkap : 'ANGGOTA';
        $tahun = $anggota->tahun_daftar ?? $anggota->created_at?->format('Y') ?? date('Y');

        return "SIM-IMM:VERIFIED|NIA:{$nia}|NAMA:{$nama}|TAHUN:{$tahun}";
    }
}
