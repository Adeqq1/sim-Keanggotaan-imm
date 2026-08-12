<?php

namespace App\Http\Controllers;

use App\Http\Requests\SertifikatRequest;
use App\Jobs\GenerateCertificateJob;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Sertifikat;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class SertifikatController extends Controller
{
    public function index()
    {
        $sertifikats = Sertifikat::with(['kegiatan', 'anggota'])->latest()->paginate(6);

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

        // Generate PDF
        $pdf = Pdf::loadView('pdf.sertifikat', compact('kegiatan', 'anggota', 'nomorSertifikat', 'role', 'instruktur'))
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

        return redirect()->route('admin.sertifikat.index')->with('success', 'Sertifikat sedang dibuat di latar belakang.');
    }

    public function mySertifikat()
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $sertifikats = Sertifikat::where('anggota_id', $anggota->id)->with('kegiatan')->latest()->paginate(6);

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

                    return redirect()->back()->with('error', 'Gagal memproses gambar latar belakang: '.$e->getMessage());
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

                    return redirect()->back()->with('error', 'Gagal menyimpan gambar latar belakang: '.$e->getMessage());
                }
            }
        }

        return redirect()->route('admin.sertifikat.settings')->with('success', 'Pengaturan sertifikat berhasil diperbarui.');
    }
}
