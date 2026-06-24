<?php

namespace App\Services;

use App\Models\Anggota;
use Illuminate\Support\Facades\DB;

class NiaGenerator
{
    /**
     * Kode wilayah IMM Kabupaten Bungo.
     */
    private const KODE_WILAYAH = '24';

    /**
     * Generate NIA untuk satu anggota dan simpan ke database.
     * Hanya memproses anggota yang belum memiliki NIA.
     *
     * @throws \RuntimeException bila anggota sudah memiliki NIA
     */
    public function generateForAnggota(Anggota $anggota): string
    {
        if (! empty($anggota->nia)) {
            throw new \RuntimeException('Anggota sudah memiliki NIA: '.$anggota->nia);
        }

        return DB::transaction(function () use ($anggota): string {
            $nia = $this->generateNextNia($this->getTahunDariAnggota($anggota));

            $anggota->update(['nia' => $nia]);

            return $nia;
        });
    }

    /**
     * Generate NIA berikutnya untuk tahun tertentu.
     * Format: KWYYNNNN (KW=kode wilayah, YY=tahun 2 digit, NNNN=nomor urut 4 digit).
     */
    public function generateNextNia(string $tahunDuaDigit): string
    {
        $prefix = self::KODE_WILAYAH.$tahunDuaDigit;

        /** @var string|null $lastNia */
        $lastNia = Anggota::where('nia', 'like', $prefix.'%')
            ->orderByDesc('nia')
            ->value('nia');

        $lastSequence = $lastNia ? (int) substr($lastNia, -4) : 0;
        $nextSequence = str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);

        return $prefix.$nextSequence;
    }

    /**
     * Ambil 2 digit tahun dari created_at anggota.
     */
    public function getTahunDariAnggota(Anggota $anggota): string
    {
        return $anggota->created_at->format('y');
    }
}
