<?php

namespace App\Jobs;

use App\Http\Controllers\SertifikatController;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Services\VerifiedAttendance;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateCertificateJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?Presensi $presensi = null,
        public ?Kegiatan $kegiatan = null,
        public ?Anggota $anggota = null,
        public ?string $instruktur = null
    ) {
        //
    }

    public function uniqueId(): string
    {
        $kegiatanId = $this->kegiatan?->getKey() ?? $this->presensi?->kegiatan_id;
        $anggotaId = $this->anggota?->getKey() ?? $this->presensi?->anggota_id;

        return "sertifikat:{$kegiatanId}:{$anggotaId}";
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->presensi) {
            $presensi = Presensi::with(['kegiatan', 'anggota'])->find($this->presensi->getKey());

            if (! $presensi || ! $presensi->anggota || ! app(VerifiedAttendance::class)->meetsRequirement($presensi->kegiatan, $presensi->anggota)) {
                return;
            }

            $kegiatan = $presensi->kegiatan;
            $anggota = $presensi->anggota;
        } else {
            $kegiatan = $this->kegiatan;
            $anggota = $this->anggota;
        }

        if ($kegiatan && $anggota && app(VerifiedAttendance::class)->meetsRequirement($kegiatan, $anggota)) {
            SertifikatController::generateCertificateFile($kegiatan, $anggota, $this->instruktur);
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Certificate generation job failed.', [
            'kegiatan_id' => $this->kegiatan?->getKey() ?? $this->presensi?->kegiatan_id,
            'anggota_id' => $this->anggota?->getKey() ?? $this->presensi?->anggota_id,
            'message' => $exception?->getMessage(),
        ]);
    }
}
