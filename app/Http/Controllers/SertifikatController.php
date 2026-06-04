<?php

namespace App\Http\Controllers;

use App\Http\Requests\SertifikatRequest;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class SertifikatController extends Controller
{
    public function index()
    {
        $sertifikats = Sertifikat::with(['kegiatan', 'anggota'])->latest()->paginate(10);

        return view('admin.sertifikat.index', compact('sertifikats'));
    }

    public function create()
    {
        $kegiatans = Kegiatan::latest()->get();
        $anggotas = Anggota::where('status_aktif', true)->get();

        return view('admin.sertifikat.create', compact('kegiatans', 'anggotas'));
    }

    public static function generateCertificateFile(Kegiatan $kegiatan, Anggota $anggota): Sertifikat
    {
        $nomorSertifikat = 'CERT-'.$kegiatan->id.'-'.$anggota->id.'-'.now()->format('Ymd');

        // Generate PDF
        $pdf = Pdf::loadView('pdf.sertifikat', compact('kegiatan', 'anggota', 'nomorSertifikat'));
        $path = 'sertifikat/'.$nomorSertifikat.'.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return Sertifikat::updateOrCreate(
            ['kegiatan_id' => $kegiatan->id, 'anggota_id' => $anggota->id],
            [
                'nomor_sertifikat' => $nomorSertifikat,
                'file_sertifikat' => $path,
            ]
        );
    }

    public function generate(SertifikatRequest $request)
    {
        $validated = $request->validated();
        $kegiatan = Kegiatan::findOrFail($validated['kegiatan_id']);

        foreach ($validated['anggota_ids'] as $anggotaId) {
            $anggota = Anggota::findOrFail($anggotaId);
            self::generateCertificateFile($kegiatan, $anggota);
        }

        return redirect()->route('admin.sertifikat.index')->with('success', 'Sertifikat berhasil di-generate.');
    }

    public function mySertifikat()
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $sertifikats = Sertifikat::where('anggota_id', $anggota->id)->with('kegiatan')->latest()->get();

        return view('kader.sertifikat.index', compact('sertifikats'));
    }

    public function download(Sertifikat $sertifikat)
    {
        // Pastikan hanya pemilik atau admin yang bisa download
        if (auth()->user()->role !== 'admin' && auth()->user()->anggota->id !== $sertifikat->anggota_id) {
            abort(403);
        }

        return Storage::disk('public')->download($sertifikat->file_sertifikat);
    }

    public function klaim(Request $request, Presensi $presensi)
    {
        // Security check: only the owner of this attendance record can claim
        $anggota = auth()->user()->anggota;
        if (! $anggota || $presensi->anggota_id !== $anggota->id) {
            abort(403);
        }

        // Only allow claiming if status_klaim is null or ditolak
        if ($presensi->status_klaim !== null && $presensi->status_klaim !== 'ditolak') {
            return redirect()->back()->with('error', 'Sertifikat sedang diproses atau sudah disetujui.');
        }

        $request->validate([
            'bukti_kehadiran' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('bukti_kehadiran')) {
            $file = $request->file('bukti_kehadiran');

            Log::info('File upload debug', [
                'isValid' => $file->isValid(),
                'error' => $file->getError(),
                'path' => $file->getPathname(),
                'realPath' => $file->getRealPath(),
                'size' => $file->getSize(),
            ]);

            // Delete old file if exists
            if ($presensi->bukti_kehadiran) {
                Storage::disk('public')->delete($presensi->bukti_kehadiran);
            }

            $compressed = false;
            $path = null;

            if (extension_loaded('gd') && class_exists(ImageManager::class) && function_exists('imagejpeg')) {
                try {
                    $manager = new ImageManager(new Driver);
                    $image = $manager->decodePath($file->getPathname());

                    if ($image->width() > 1200 || $image->height() > 1200) {
                        $image->scale(width: 1200);
                    }

                    $encoded = $image->encodeUsingFormat(Format::JPEG, quality: 70);
                    $filename = Str::random(40).'.jpg';
                    $path = 'bukti_kehadiran/'.$filename;
                    Storage::disk('public')->put($path, (string) $encoded);
                    $compressed = true;
                } catch (\Throwable $e) {
                    Log::error('Intervention failed', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            if (! $compressed) {
                Log::info('Attempting fallback store for Windows compatibility', [
                    'pathname' => $file->getPathname(),
                    'originalName' => $file->getClientOriginalName(),
                ]);
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = Str::random(40).'.'.$extension;
                $path = 'bukti_kehadiran/'.$filename;

                $stream = fopen($file->getPathname(), 'r');
                Storage::disk('public')->put($path, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            $presensi->update([
                'bukti_kehadiran' => $path,
                'status_klaim' => 'pending',
            ]);

            return redirect()->back()->with('success', 'Klaim sertifikat berhasil diajukan. Menunggu verifikasi.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah bukti kehadiran.');
    }

    public function verifikasiIndex()
    {
        // Admins and instructors shared
        $pendingClaims = Presensi::where('status_klaim', 'pending')
            ->with(['kegiatan', 'anggota'])
            ->latest()
            ->paginate(10);

        return view('admin.sertifikat.verifikasi', compact('pendingClaims'));
    }

    public function setuju(Presensi $presensi)
    {
        if ($presensi->status_klaim !== 'pending') {
            return redirect()->back()->with('error', 'Klaim sertifikat tidak sedang pending.');
        }

        DB::transaction(function () use ($presensi) {
            $presensi->update([
                'status_klaim' => 'disetujui',
                'status_kehadiran' => 'hadir',
                'waktu_hadir' => $presensi->waktu_hadir ?? now(),
            ]);

            self::generateCertificateFile($presensi->kegiatan, $presensi->anggota);
        });

        return redirect()->route('admin.sertifikat.verifikasi.index')->with('success', 'Klaim sertifikat disetujui dan sertifikat berhasil diterbitkan.');
    }

    public function tolak(Presensi $presensi)
    {
        if ($presensi->status_klaim !== 'pending') {
            return redirect()->back()->with('error', 'Klaim sertifikat tidak sedang pending.');
        }

        if ($presensi->bukti_kehadiran) {
            Storage::disk('public')->delete($presensi->bukti_kehadiran);
        }

        $presensi->update([
            'status_klaim' => 'ditolak',
            'bukti_kehadiran' => null,
        ]);

        return redirect()->route('admin.sertifikat.verifikasi.index')->with('info', 'Klaim sertifikat telah ditolak.');
    }
}
