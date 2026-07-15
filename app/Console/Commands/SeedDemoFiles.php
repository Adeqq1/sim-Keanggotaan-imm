<?php

namespace App\Console\Commands;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Services\DemoDataFiles;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('demo:seed-files')]
#[Description('Provision local deterministic dummy files (images/PDFs) for existing demo records.')]
class SeedDemoFiles extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(DemoDataFiles $service)
    {
        $this->info('Starting demo files provisioning...');

        // Guard: ensure the baseline seeded database exists
        if (Anggota::count() === 0 || Kegiatan::count() === 0 || Presensi::count() === 0 || Pendaftaran::count() === 0) {
            $this->error('Baseline demo records are missing.');
            $this->line('Please run <comment>docker compose exec app php artisan migrate:fresh --seed</comment> first to populate the database records before provisioning demo files.');
            return self::FAILURE;
        }

        try {
            $stats = $service->provisionFiles();
        } catch (\Exception $e) {
            $this->error('Failed to provision files: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Demo files successfully provisioned.');
        $this->table(
            ['Artifacts Created', 'Count'],
            [
                ['Public disk files (Photos, Proofs, Certs)', $stats['public_files']],
                ['Private local files (Archives)', $stats['private_files']],
                ['Database records updated/linked', $stats['records_updated']],
            ]
        );

        $this->newLine();
        $this->line('File links updated safely. Private archives are stored under <comment>storage/app/private/arsip</comment>.');
        $this->line('Ensure you run <comment>php artisan storage:link</comment> to make public disk artifacts accessible via the browser.');

        return self::SUCCESS;
    }
}
