<?php

namespace App\Jobs;

use App\Http\Controllers\SertifikatController;
use App\Models\Presensi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateCertificateJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Presensi $presensi)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SertifikatController::generateCertificateFile($this->presensi->kegiatan, $this->presensi->anggota);
    }
}
