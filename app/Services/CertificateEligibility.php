<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\PenilaianKegiatan;
use App\Models\Sertifikat;

class CertificateEligibility
{
    public function evaluate(Kegiatan $kegiatan, Anggota $anggota): ?array
    {
        if (! $anggota->status_aktif || $anggota->user?->role !== 'kader') {
            return null;
        }

        $minimum = (int) $kegiatan->minimum_sesi_terverifikasi;
        $type = match ($kegiatan->jenis_pelaksanaan) {
            Kegiatan::SATU_SESI => $minimum === 1 ? Sertifikat::SATU_SESI : null,
            Kegiatan::MULTI_SESI => $minimum >= 3 ? Sertifikat::MULTI_SESI : null,
            default => null,
        };

        if (! $type) {
            return null;
        }

        $attendance = app(VerifiedAttendance::class)->countFor($kegiatan, $anggota);
        if ($attendance < $minimum) {
            return null;
        }

        $assessment = $type === Sertifikat::MULTI_SESI
            ? $anggota->penilaianKegiatans()->where('kegiatan_id', $kegiatan->id)->latest('id')->first()
            : null;

        if ($type === Sertifikat::MULTI_SESI && ! $assessment instanceof PenilaianKegiatan) {
            return null;
        }

        $grade = $assessment?->nilai;
        if ($type === Sertifikat::MULTI_SESI && ! array_key_exists($grade, PenilaianKegiatan::NILAI_LABELS)) {
            return null;
        }

        return [
            'tipe_sertifikat' => $type,
            'nilai_snapshot' => $grade,
            'label_nilai' => $grade ? PenilaianKegiatan::NILAI_LABELS[$grade] : null,
        ];
    }

    public function eligible(Kegiatan $kegiatan, Anggota $anggota): bool
    {
        return $this->evaluate($kegiatan, $anggota) !== null;
    }
}
