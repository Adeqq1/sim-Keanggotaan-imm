<?php

namespace App\Http\Controllers;

use App\Http\Requests\SertifikatRequest;
use App\Jobs\GenerateCertificateJob;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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

    public static function generateCertificateFile(Kegiatan $kegiatan, Anggota $anggota, ?string $instruktur = null): Sertifikat
    {
        $nomorSertifikat = 'CERT-'.$kegiatan->id.'-'.$anggota->id.'-'.now()->format('Ymd');
        $role = $anggota->user ? ucfirst($anggota->user->role) : 'Kader';
        $instruktur = $instruktur ?? User::where('role', 'instruktur')->first()?->name ?? 'Pimpinan Cabang';
        $useBackground = self::useBackground();

        // Generate PDF
        $pdf = Pdf::loadView('pdf.sertifikat', compact('kegiatan', 'anggota', 'nomorSertifikat', 'role', 'instruktur', 'useBackground'))
            ->setPaper('a4', 'landscape');
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
        $instruktur = User::where('role', 'instruktur')->first()?->name ?? 'Pimpinan Cabang';

        foreach ($validated['anggota_ids'] as $anggotaId) {
            $anggota = Anggota::findOrFail($anggotaId);
            GenerateCertificateJob::dispatch(null, $kegiatan, $anggota, $instruktur);
        }

        return redirect()->route('admin.sertifikat.index')->with('success', 'Sertifikat sedang di-generate di latar belakang.');
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

        $presensi->approveClaim();

        GenerateCertificateJob::dispatch($presensi);

        return redirect()->route('admin.sertifikat.verifikasi.index')->with('success', 'Klaim sertifikat disetujui dan sertifikat berhasil diterbitkan.');
    }

    public function tolak(Presensi $presensi)
    {
        if ($presensi->status_klaim !== 'pending') {
            return redirect()->back()->with('error', 'Klaim sertifikat tidak sedang pending.');
        }

        $presensi->rejectClaim();

        return redirect()->route('admin.sertifikat.verifikasi.index')->with('info', 'Klaim sertifikat telah ditolak.');
    }

    public static function useBackground(): bool
    {
        if (Storage::disk('local')->exists('sertifikat_settings.json')) {
            $settings = json_decode(Storage::disk('local')->get('sertifikat_settings.json'), true);

            return (bool) ($settings['use_background'] ?? true);
        }

        return true;
    }

    public function settings()
    {
        $bgPath = 'images/sertificate-asset/bg-sertificate.jpg';
        $bgExists = file_exists(public_path($bgPath));
        $useBackground = self::useBackground();

        return view('admin.sertifikat.settings', compact('bgExists', 'bgPath', 'useBackground'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'use_background' => ['nullable', 'boolean'],
        ]);

        // Save use_background setting
        $useBackground = $request->has('use_background');
        Storage::disk('local')->put('sertifikat_settings.json', json_encode([
            'use_background' => $useBackground,
        ]));

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $destinationPath = public_path('images/sertificate-asset/bg-sertificate.jpg');

            // Ensure directory exists
            if (! file_exists(dirname($destinationPath))) {
                mkdir(dirname($destinationPath), 0755, true);
            }

            if (extension_loaded('gd') && class_exists(ImageManager::class) && function_exists('imagejpeg')) {
                try {
                    $manager = new ImageManager(new Driver);
                    $image = $manager->decodePath($file->getPathname());

                    // Resize & Crop dynamically to A4 Landscape dimension (1122x794 pixels)
                    $image->cover(1122, 794);

                    $encoded = $image->encodeUsingFormat(Format::JPEG, quality: 90);
                    file_put_contents($destinationPath, (string) $encoded);
                } catch (\Throwable $e) {
                    Log::error('Failed to resize certificate background', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    return redirect()->back()->with('error', 'Gagal memproses gambar background: '.$e->getMessage());
                }
            } else {
                // Fallback: Save directly if GD / imagejpeg is not available
                try {
                    copy($file->getRealPath(), $destinationPath);
                } catch (\Throwable $e) {
                    Log::error('Failed to save certificate background directly', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    return redirect()->back()->with('error', 'Gagal menyimpan gambar background: '.$e->getMessage());
                }
            }
        }

        return redirect()->route('admin.sertifikat.settings')->with('success', 'Pengaturan sertifikat berhasil diperbarui.');
    }
}
