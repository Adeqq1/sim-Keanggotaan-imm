<?php

namespace App\Jobs;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Services\CertificateEligibility;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

#[DeleteWhenMissingModels]
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
            Log::warning('Legacy certificate claim job skipped.', ['presensi_id' => $this->presensi->getKey()]);
            return;
        } else {
            $kegiatan = $this->kegiatan?->fresh();
            $anggota = $this->anggota?->fresh(['user']);
        }

        if (! $kegiatan || ! $anggota || ! app(CertificateEligibility::class)->eligible($kegiatan, $anggota)) {
            Log::warning('Certificate generation skipped because attendance is no longer eligible.', [
                'kegiatan_id' => $kegiatan?->id,
                'anggota_id' => $anggota?->id,
            ]);
            return;
        }

        if (\App\Models\Sertifikat::where('kegiatan_id', $kegiatan->id)->where('anggota_id', $anggota->id)->exists()) return;

        \App\Http\Controllers\SertifikatController::generateCertificateFile($kegiatan, $anggota, $this->instruktur);
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
