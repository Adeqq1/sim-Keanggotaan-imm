<?php

namespace App\Jobs;

use App\Http\Controllers\SertifikatController;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateCertificateJob implements ShouldQueue
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

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $kegiatan = $this->kegiatan ?? $this->presensi?->kegiatan;
        $anggota = $this->anggota ?? $this->presensi?->anggota;

        if ($kegiatan && $anggota) {
            SertifikatController::generateCertificateFile($kegiatan, $anggota, $this->instruktur);
        }
    }
}
