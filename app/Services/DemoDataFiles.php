<?php

namespace App\Services;

use App\Http\Controllers\SertifikatController;
use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class DemoDataFiles
{
    /**
     * Delete and recreate demo files and database paths based on existing seeded records.
     * Must be safe to rerun.
     */
    public function provisionFiles(): array
    {
        $this->cleanDemoNamespaces();

        $stats = [
            'public_files' => 0,
            'private_files' => 0,
            'records_updated' => 0,
        ];

        // 1. Provision profile photos (Anggota)
        $anggotas = Anggota::take(3)->get();
        foreach ($anggotas as $anggota) {
            $path = 'foto_profil/demo/anggota-'.$anggota->id.'.png';
            $this->copyImageToPublic($path);
            $anggota->update(['foto_profil' => $path]);
            $stats['public_files']++;
            $stats['records_updated']++;
        }

        // 2. Provision activity thumbnails (Kegiatan)
        $kegiatans = Kegiatan::take(3)->get();
        foreach ($kegiatans as $kegiatan) {
            $path = 'kegiatan_thumbnails/demo/kegiatan-'.$kegiatan->id.'.png';
            $this->copyImageToPublic($path);
            $kegiatan->update(['thumbnail' => $path]);
            $stats['public_files']++;
            $stats['records_updated']++;
        }
        Cache::forget('kegiatan.terbaru');

        // 3. Provision registration document (Pendaftaran - pending)
        $pendaftaran = Pendaftaran::where('status_validasi', 'pending')->first();
        if ($pendaftaran) {
            $path = 'pendaftaran/demo/syarat-'.$pendaftaran->id.'.pdf';
            $this->createSimplePdfToDisk('local', $path, 'Dokumen Persyaratan: '.$pendaftaran->nama_lengkap);
            $pendaftaran->update([
                'file_persyaratan' => $path,
                'jenis_dokumen_identitas' => 'ktp',
            ]);
            $stats['private_files']++;
            $stats['records_updated']++;
        }

        // 4. Provision attendance claim states
        // Find a pending claim
        $pendingClaim = Presensi::where('status_klaim', 'pending')->first();
        if ($pendingClaim) {
            $path = 'bukti_kehadiran/demo/bukti-'.$pendingClaim->id.'.png';
            $this->copyImageToPublic($path);
            $pendingClaim->update(['bukti_kehadiran' => $path]);
            $stats['public_files']++;
            $stats['records_updated']++;
        }

        // 5. Provision generated certificates for approved claims
        $approvedClaims = Presensi::where('status_klaim', 'disetujui')->with(['kegiatan', 'anggota'])->take(2)->get();
        $adminForSignature = User::where('role', 'admin')->first() ?? User::first();

        // Make sure background exists for generation if it's missing in development
        if (! Storage::disk('local')->exists('sertifikat_settings.json')) {
            Storage::disk('local')->put('sertifikat_settings.json', json_encode([
                'use_background' => true,
            ]));
        }

        $sertifikatController = app(SertifikatController::class);
        foreach ($approvedClaims as $claim) {
            $path = 'bukti_kehadiran/demo/bukti-'.$claim->id.'.png';
            $this->copyImageToPublic($path);
            $claim->update(['bukti_kehadiran' => $path]);
            $stats['public_files']++;

            // Trigger the real application generation logic
            $sertifikatController->generateCertificateFile($claim->kegiatan, $claim->anggota, $adminForSignature->nama_lengkap ?? 'Admin');
            $stats['public_files']++;
            $stats['records_updated']+=2; // claim proof + certificate creation
        }

        // 6. Provision private archives (Arsip)
        // Find kader members to assign private archives
        $arsipOwners = Anggota::whereHas('user', fn ($q) => $q->where('role', 'kader'))->take(2)->get();

        foreach ($arsipOwners as $index => $anggota) {
            $path = 'arsip/demo/arsip-'.$anggota->id.'.pdf';
            $this->createSimplePdfToDisk('local', $path, 'Arsip Pribadi: '.$anggota->nama_lengkap);

            $arsip = Arsip::where('judul_dokumen', 'Dokumen Arsip Demo '.($index + 1))->first();
            if ($arsip) {
                $arsip->update([
                    'anggota_id' => $anggota->id,
                    'file_arsip' => $path,
                ]);
            } else {
                Arsip::create([
                    'nomor_dokumen' => 'DEMO/ARSIP/'.date('Y').'/00'.($index + 1),
                    'anggota_id' => $anggota->id,
                    'judul_dokumen' => 'Dokumen Arsip Demo '.($index + 1),
                    'kategori_arsip' => 'lainnya',
                    'file_arsip' => $path,
                    'tanggal_unggah' => now(),
                ]);
            }

            $stats['private_files']++;
            $stats['records_updated']++;
        }

        return $stats;
    }

    private function cleanDemoNamespaces(): void
    {
        $publicPaths = [
            'foto_profil/demo',
            'kegiatan_thumbnails/demo',
            'pendaftaran/demo',
            'bukti_kehadiran/demo',
            'sertifikat/demo' // Any manually created demo certs
        ];

        foreach ($publicPaths as $dir) {
            Storage::disk('public')->deleteDirectory($dir);
        }

        foreach (['arsip/demo', 'pendaftaran/demo'] as $dir) {
            Storage::disk('local')->deleteDirectory($dir);
        }
    }

    private function copyImageToPublic(string $destinationPath): void
    {
        $sourcePath = public_path('images/placeholder-kegiatan.png');

        if (file_exists($sourcePath)) {
            Storage::disk('public')->put($destinationPath, file_get_contents($sourcePath));
        } else {
            // Fallback 1x1 PNG bytes if the placeholder was deleted
            $fallbackPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
            Storage::disk('public')->put($destinationPath, $fallbackPng);
        }
    }

    private function createSimplePdfToDisk(string $disk, string $path, string $text): void
    {
        // For CLI environment, a minimal valid PDF byte sequence is faster and safer
        // than invoking domPDF just for generic dummy attachment files.
        // Certificates still use DomPDF.
        $pdfBytes = "%PDF-1.4\n%âãÏÓ\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 612 792] /Contents 5 0 R >>\nendobj\n4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n5 0 obj\n<< /Length 44 >>\nstream\nBT /F1 24 Tf 100 700 Td (" . str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $text) . ") Tj ET\nendstream\nendobj\nxref\n0 6\n0000000000 65535 f \n0000000015 00000 n \n0000000064 00000 n \n0000000121 00000 n \n0000000227 00000 n \n0000000315 00000 n \ntrailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n408\n%%EOF\n";

        Storage::disk($disk)->put($path, $pdfBytes);
    }
}
