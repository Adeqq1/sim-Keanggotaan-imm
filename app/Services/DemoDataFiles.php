<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use App\Models\MateriKegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Sertifikat;
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
        $approvedClaims = Presensi::query()
            ->where('status_klaim', 'disetujui')
            ->where('bukti_kehadiran', 'like', 'bukti_kehadiran/demo/%')
            ->get(['id', 'bukti_kehadiran']);

        $this->cleanDemoNamespaces();

        $stats = [
            'public_files' => 0,
            'private_files' => 0,
            'records_updated' => 0,
        ];

        // Only records explicitly owned by the demo namespace are modified.
        $anggotas = Anggota::where('foto_profil', 'like', 'foto_profil/demo/%')->get();
        foreach ($anggotas as $anggota) {
            $path = $anggota->foto_profil;
            $this->copyImageToPublic($path);
            $stats['public_files']++;
        }

        $kegiatans = Kegiatan::where('thumbnail', 'like', 'kegiatan_thumbnails/demo/%')->get();
        foreach ($kegiatans as $kegiatan) {
            $path = $kegiatan->thumbnail;
            $this->copyImageToPublic($path);
            $stats['public_files']++;
        }
        Cache::forget('kegiatan.terbaru');

        foreach (Pendaftaran::where('file_persyaratan', 'like', 'pendaftaran/demo/%')->get() as $pendaftaran) {
            $path = $pendaftaran->file_persyaratan;
            if (str_ends_with($path, '.png')) {
                $this->copyImageToDisk('local', $path);
            } else {
                $this->createSimplePdfToDisk('local', $path, 'Dokumen Persyaratan: '.$pendaftaran->nama_lengkap);
            }
            $stats['private_files']++;
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

        foreach ($approvedClaims as $claim) {
            $this->copyImageToPublic($claim->bukti_kehadiran);
            $stats['public_files']++;
        }

        foreach (MateriKegiatan::where('file_materi', 'like', 'materi_kegiatan/demo/%')->get() as $materi) {
            $this->createSimplePdfToDisk('local', $materi->file_materi, 'Materi: '.$materi->judul);
            $stats['private_files']++;
        }

        foreach (LaporanKegiatan::where('file_lampiran', 'like', 'laporan_kegiatan/demo/%')->get() as $laporan) {
            $this->createSimplePdfToDisk('local', $laporan->file_lampiran, 'Lampiran Laporan: '.$laporan->kegiatan->nama_kegiatan);
            $stats['private_files']++;
        }

        foreach (Arsip::where('file_arsip', 'like', 'arsip/demo/%')->get() as $arsip) {
            $this->createSimplePdfToDisk('local', $arsip->file_arsip, 'Arsip: '.$arsip->judul_dokumen);
            $stats['private_files']++;
        }

        foreach (Sertifikat::where('file_sertifikat', 'like', 'sertifikat/demo/%')->get() as $sertifikat) {
            $this->createSimplePdfToDisk('public', $sertifikat->file_sertifikat, 'Sertifikat: '.$sertifikat->anggota->nama_lengkap);
            $stats['public_files']++;
        }

        if (! Storage::disk('local')->exists('sertifikat_settings.json')) {
            Storage::disk('local')->put('sertifikat_settings.json', json_encode([
                'use_background' => true,
            ]));
        }

        return $stats;
    }

    private function cleanDemoNamespaces(): void
    {
        $publicPaths = [
            'foto_profil/demo',
            'kegiatan_thumbnails/demo',
            'bukti_kehadiran/demo',
            'sertifikat/demo',
        ];

        foreach ($publicPaths as $dir) {
            Storage::disk('public')->deleteDirectory($dir);
        }

        foreach (['arsip/demo', 'pendaftaran/demo', 'materi_kegiatan/demo', 'laporan_kegiatan/demo'] as $dir) {
            Storage::disk('local')->deleteDirectory($dir);
        }
    }

    private function copyImageToPublic(string $destinationPath): void
    {
        $this->copyImageToDisk('public', $destinationPath);
    }

    private function copyImageToDisk(string $disk, string $destinationPath): void
    {
        $sourcePath = public_path('images/placeholder-kegiatan.png');

        if (file_exists($sourcePath)) {
            Storage::disk($disk)->put($destinationPath, file_get_contents($sourcePath));
        } else {
            // Fallback 1x1 PNG bytes if the placeholder was deleted
            $fallbackPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
            Storage::disk($disk)->put($destinationPath, $fallbackPng);
        }
    }

    private function createSimplePdfToDisk(string $disk, string $path, string $text): void
    {
        // For CLI environment, a minimal valid PDF byte sequence is faster and safer
        // than invoking domPDF just for generic dummy attachment files.
        // Certificates still use DomPDF.
        $pdfBytes = "%PDF-1.4\n%âãÏÓ\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 612 792] /Contents 5 0 R >>\nendobj\n4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n5 0 obj\n<< /Length 44 >>\nstream\nBT /F1 24 Tf 100 700 Td (".str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $text).") Tj ET\nendstream\nendobj\nxref\n0 6\n0000000000 65535 f \n0000000015 00000 n \n0000000064 00000 n \n0000000121 00000 n \n0000000227 00000 n \n0000000315 00000 n \ntrailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n408\n%%EOF\n";

        Storage::disk($disk)->put($path, $pdfBytes);
    }
}
