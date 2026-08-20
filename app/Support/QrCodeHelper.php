<?php

namespace App\Support;

use App\Models\Anggota;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeHelper
{
    public static function generateDataUri(string $text, int $size = 120): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'svgViewBoxSize' => $size,
            'addQuietzone' => true,
            'quietzoneSize' => 4,
        ]);

        return 'data:image/svg+xml;base64,'.base64_encode((new QRCode($options))->render($text));
    }

    public static function makeVerificationPayload(Anggota $anggota): string
    {
        $nia = filled($anggota->nia) ? $anggota->nia : 'BELUM_TERSEDIA';
        $nama = filled($anggota->nama_lengkap) ? $anggota->nama_lengkap : 'ANGGOTA';
        $tahun = $anggota->tahun_daftar ?? $anggota->created_at?->format('Y') ?? date('Y');

        return "SIM-IMM:VERIFIED|NIA:{$nia}|NAMA:{$nama}|TAHUN:{$tahun}";
    }
}
