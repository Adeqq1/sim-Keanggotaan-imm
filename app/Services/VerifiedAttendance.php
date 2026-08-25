<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use Illuminate\Support\Collection;

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
        return match ($kegiatan->jenis_pelaksanaan) {
            Kegiatan::SATU_SESI => $kegiatan->minimum_sesi_terverifikasi === 1 && $this->countFor($kegiatan, $anggota) >= 1,
            Kegiatan::MULTI_SESI => $kegiatan->minimum_sesi_terverifikasi !== null
                && $kegiatan->minimum_sesi_terverifikasi >= 3
                && $this->countFor($kegiatan, $anggota) >= $kegiatan->minimum_sesi_terverifikasi,
            default => false,
        };
    }

    public function eligibleAnggotaIdsFor(Kegiatan $kegiatan): Collection
    {
        return Presensi::query()
            ->terverifikasi()
            ->where('kegiatan_id', $kegiatan->id)
            ->whereRelation('sesiKegiatan', 'kegiatan_id', $kegiatan->id)
            ->select('anggota_id')
            ->selectRaw('COUNT(DISTINCT sesi_kegiatan_id) as verified_sessions')
            ->groupBy('anggota_id')
            ->havingRaw('COUNT(DISTINCT sesi_kegiatan_id) >= ?', [$kegiatan->minimum_sesi_terverifikasi])
            ->pluck('anggota_id');
    }

    public function eligibleKegiatanIds(Anggota $anggota): Collection
    {
        return Presensi::query()
            ->terverifikasi()
            ->join('kegiatan', 'kegiatan.id', '=', 'presensi.kegiatan_id')
            ->join('sesi_kegiatan', 'sesi_kegiatan.id', '=', 'presensi.sesi_kegiatan_id')
            ->where('presensi.anggota_id', $anggota->id)
            ->whereIn('kegiatan.jenis_pelaksanaan', [Kegiatan::SATU_SESI, Kegiatan::MULTI_SESI])
            ->whereColumn('sesi_kegiatan.kegiatan_id', 'kegiatan.id')
            ->whereNotNull('kegiatan.minimum_sesi_terverifikasi')
            ->select('presensi.kegiatan_id')
            ->selectRaw('COUNT(DISTINCT presensi.sesi_kegiatan_id) as verified_sessions')
            ->groupBy('presensi.kegiatan_id', 'kegiatan.minimum_sesi_terverifikasi')
            ->havingRaw('COUNT(DISTINCT presensi.sesi_kegiatan_id) >= MAX(kegiatan.minimum_sesi_terverifikasi)')
            ->pluck('presensi.kegiatan_id');
    }

    public function countEligibleActivities(Anggota $anggota): int
    {
        return $this->eligibleKegiatanIds($anggota)->count();
    }
}
