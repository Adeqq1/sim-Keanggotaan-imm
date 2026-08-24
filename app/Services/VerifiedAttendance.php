<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;

class VerifiedAttendance
{
    public function countFor(Kegiatan $kegiatan, Anggota $anggota): int
    {
        return Presensi::query()
            ->terverifikasi()
            ->where('kegiatan_id', $kegiatan->id)
            ->where('anggota_id', $anggota->id)
            ->whereRelation('sesiKegiatan', 'kegiatan_id', $kegiatan->id)
            ->distinct('sesi_kegiatan_id')
            ->count('sesi_kegiatan_id');
    }

    public function meetsRequirement(Kegiatan $kegiatan, Anggota $anggota): bool
    {
        return in_array($kegiatan->jenis_pelaksanaan, [Kegiatan::SATU_SESI, Kegiatan::MULTI_SESI], true)
            && $kegiatan->minimum_sesi_terverifikasi !== null
            && $this->countFor($kegiatan, $anggota) >= $kegiatan->minimum_sesi_terverifikasi;
    }
}
