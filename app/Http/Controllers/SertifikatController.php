<?php

namespace App\Http\Controllers;

use App\Http\Requests\SertifikatRequest;
use App\Jobs\GenerateCertificateJob;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Sertifikat;
use App\Models\User;
use App\Services\CertificateEligibility;
use App\Services\VerifiedAttendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Support\SortParams;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class SertifikatController extends Controller
{
    public function index(Request $request)
    {
        $options = ['anggota' => 'Nama Anggota', 'kegiatan' => 'Nama Kegiatan', 'nomor' => 'Nomor', 'tanggal_kegiatan' => 'Tanggal Kegiatan', 'created' => 'Waktu Ditambahkan'];
        $sort = SortParams::resolve($request, array_keys($options), 'created');
        $columns = ['nomor' => 'nomor_sertifikat', 'created' => 'sertifikat.created_at'];
        $sertifikats = Sertifikat::with(['kegiatan', 'anggota'])
            ->when(! in_array($sort['key'], ['anggota', 'kegiatan', 'tanggal_kegiatan'], true), fn ($query) => $query->orderBy($columns[$sort['key']], $sort['direction']))
            ->when($sort['key'] === 'anggota', fn ($query) => $query->orderBy(Anggota::select('nama_lengkap')->whereColumn('anggota.id', 'sertifikat.anggota_id'), $sort['direction']))
            ->when(in_array($sort['key'], ['kegiatan', 'tanggal_kegiatan'], true), fn ($query) => $query->orderBy(Kegiatan::select($sort['key'] === 'kegiatan' ? 'nama_kegiatan' : 'tanggal_waktu')->whereColumn('kegiatan.id', 'sertifikat.kegiatan_id'), $sort['direction']))
            ->orderByDesc('sertifikat.id')->paginate(6)->withQueryString();

        return view('admin.sertifikat.index', compact('sertifikats', 'options', 'sort'));
    }

    public function create(Request $request)
    {
        $kegiatans = Kegiatan::latest()->get();
        $selectedKegiatanId = $request->validate([
            'kegiatan_id' => ['nullable', Rule::exists('kegiatan', 'id')],
        ])['kegiatan_id'] ?? old('kegiatan_id');
        $selectedKegiatan = $selectedKegiatanId ? $kegiatans->firstWhere('id', (int) $selectedKegiatanId) : null;
        $anggotas = collect();

        if ($selectedKegiatan) {
            $eligibleIds = app(VerifiedAttendance::class)->eligibleAnggotaIdsFor($selectedKegiatan);
            $candidates = Anggota::query()
                ->with('user')
                ->where('status_aktif', true)
                ->whereIn('id', $eligibleIds)
                ->whereHas('user', fn ($query) => $query->where('role', 'kader'))
                ->whereDoesntHave('sertifikat', fn ($query) => $query->where('kegiatan_id', $selectedKegiatan->id))
                ->orderBy('nama_lengkap')
                ->get();
            $eligibility = app(CertificateEligibility::class);
            $anggotas = $candidates->filter(fn (Anggota $anggota): bool => $eligibility->eligible($selectedKegiatan, $anggota))->values();
        }

        return view('admin.sertifikat.create', compact('kegiatans', 'anggotas', 'selectedKegiatan', 'selectedKegiatanId'));
    }

    public static function generateCertificateFile(Kegiatan $kegiatan, Anggota $anggota, ?string $instruktur = null): Sertifikat
    {
        $issuedAt = now();
        $nomorSertifikat = 'CERT-'.$kegiatan->id.'-'.$anggota->id.'-'.$issuedAt->format('Ymd');
        $role = $anggota->user ? ucfirst($anggota->user->role) : 'Kader';
        $instruktur = $instruktur ?? User::where('role', 'instruktur')->first()?->name ?? 'Pimpinan Cabang';

        $eligibility = app(CertificateEligibility::class)->evaluate($kegiatan, $anggota);
        if (! $eligibility) {
            throw new \RuntimeException('Anggota tidak memenuhi syarat sertifikat.');
        }

        $useBackground = self::useBackground();
        $pdf = Pdf::loadView('pdf.sertifikat', compact('kegiatan', 'anggota', 'nomorSertifikat', 'role', 'instruktur', 'issuedAt', 'useBackground') + $eligibility)
            ->setPaper('a4', 'landscape');
        $path = 'sertifikat/'.$nomorSertifikat.'-'.(string) \Illuminate\Support\Str::uuid().'.pdf';
        try {
            $stored = Storage::disk('public')->put($path, $pdf->output());

            if (! $stored) {
                throw new \RuntimeException('Gagal menyimpan file sertifikat.');
            }
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        try {
            return Sertifikat::create([
                'kegiatan_id' => $kegiatan->id,
                'anggota_id' => $anggota->id,
                'nomor_sertifikat' => $nomorSertifikat,
                'file_sertifikat' => $path,
                'tipe_sertifikat' => $eligibility['tipe_sertifikat'],
                'nilai_snapshot' => $eligibility['nilai_snapshot'],
                'created_at' => $issuedAt,
                'updated_at' => $issuedAt,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);

            if ($exception instanceof QueryException) {
                $existing = Sertifikat::where('kegiatan_id', $kegiatan->id)
                    ->where('anggota_id', $anggota->id)
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            throw $exception;
        }
    }

    public function generate(SertifikatRequest $request)
    {
        $validated = $request->validated();
        $kegiatan = Kegiatan::with([])->findOrFail($validated['kegiatan_id']);
        $instruktur = User::where('role', 'instruktur')->first()?->name ?? 'Pimpinan Cabang';
        $anggotaIds = $validated['anggota_ids'];
        $anggotas = Anggota::with('user')->whereIn('id', $anggotaIds)->get()->keyBy('id');

        abort_unless($anggotas->count() === count($anggotaIds), 422);

        $eligibility = app(CertificateEligibility::class);
        abort_unless($anggotas->every(fn (Anggota $anggota): bool => $eligibility->eligible($kegiatan, $anggota)), 422);

        $jobs = collect($anggotaIds)->map(function (int $anggotaId) use ($anggotas, $kegiatan, $instruktur) {
            return new GenerateCertificateJob(null, $kegiatan, $anggotas->get($anggotaId), $instruktur);
        })->all();
        $batch = Bus::batch($jobs)
            ->name('sertifikat-generation:user-'.auth()->id())
            ->withOption('user_id', auth()->id())
            ->withOption('kegiatan_id', $kegiatan->id)
            ->withOption('anggota_ids', array_values($anggotaIds))
            ->allowFailures()
            ->dispatch();

        return redirect()->route('admin.sertifikat.index', ['generation' => $batch->id])->with('success', 'Sertifikat sedang dibuat di latar belakang.');
    }

    public function generationStatus(string $batchId)
    {
        $batch = Bus::findBatch($batchId);
        $ownedByCurrentAdmin = $batch && (
            (int) ($batch->options['user_id'] ?? 0) === (int) auth()->id()
            || $batch->name === 'sertifikat-generation:user-'.auth()->id()
        );
        abort_unless($ownedByCurrentAdmin, 404);

        $requestedIds = collect($batch->options['anggota_ids'] ?? [])->map(fn ($id) => (int) $id);
        $createdCount = $requestedIds->isEmpty() || ! $batch->options['kegiatan_id']
            ? 0
            : Sertifikat::query()
                ->where('kegiatan_id', $batch->options['kegiatan_id'])
                ->whereIn('anggota_id', $requestedIds)
                ->count();
        $legacyBatch = ! array_key_exists('user_id', $batch->options);
        $queuedBatchJobs = \Illuminate\Support\Facades\DB::table('jobs')->where('payload', 'like', '%'.$batch->id.'%')->exists();
        $outputComplete = $requestedIds->isNotEmpty() && $createdCount >= $requestedIds->count();
        $legacyBatchComplete = $legacyBatch && ! $queuedBatchJobs;

        if (! $batch->finished() && ($outputComplete || $legacyBatchComplete) && $batch->pendingJobs > 0) {
            app(\Illuminate\Bus\BatchRepository::class)->markAsFinished($batch->id);
            $batch = $batch->fresh();
        }

        $processed = $legacyBatchComplete ? $batch->totalJobs : max($batch->processedJobs(), $createdCount);
        $finished = $batch->finished() || $outputComplete || $legacyBatchComplete;

        return response()->json([
            'status' => $finished ? ($batch->failedJobs > 0 ? 'finished_with_failures' : 'finished') : 'processing',
            'total' => $batch->totalJobs,
            'processed' => $processed,
            'pending' => max(0, $batch->totalJobs - $processed),
            'failed' => $batch->failedJobs,
            'progress' => $batch->totalJobs > 0 ? min(100, (int) round(($processed / $batch->totalJobs) * 100)) : 0,
            'finished' => $finished,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function mySertifikat(Request $request)
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $options = ['kegiatan' => 'Nama Kegiatan', 'tanggal_kegiatan' => 'Tanggal Kegiatan', 'nomor' => 'Nomor', 'created' => 'Waktu Ditambahkan'];
        $sort = SortParams::resolve($request, array_keys($options), 'created');
        $columns = ['nomor' => 'nomor_sertifikat', 'created' => 'sertifikat.created_at'];
        $sertifikats = Sertifikat::where('anggota_id', $anggota->id)->with('kegiatan')
            ->when(! in_array($sort['key'], ['kegiatan', 'tanggal_kegiatan'], true), fn ($query) => $query->orderBy($columns[$sort['key']], $sort['direction']))
            ->when(in_array($sort['key'], ['kegiatan', 'tanggal_kegiatan'], true), fn ($query) => $query->orderBy(Kegiatan::select($sort['key'] === 'kegiatan' ? 'nama_kegiatan' : 'tanggal_waktu')->whereColumn('kegiatan.id', 'sertifikat.kegiatan_id'), $sort['direction']))
            ->orderByDesc('sertifikat.id')->paginate(6)->withQueryString();
        $verifiedAttendance = app(VerifiedAttendance::class);
        $eligibleKegiatanIds = $verifiedAttendance->eligibleKegiatanIds($anggota);
        $jumlahKegiatanHadir = $eligibleKegiatanIds->count();
        $canDownloadSertifikat = $jumlahKegiatanHadir > 0;

        return view('kader.sertifikat.index', compact(
            'sertifikats',
            'jumlahKegiatanHadir',
            'canDownloadSertifikat',
            'eligibleKegiatanIds',
            'options', 'sort',
        ))->with('minimumKegiatanHadir', 1);
    }

    public function download(Sertifikat $sertifikat)
    {
        if (auth()->user()->role === 'admin') {
            abort_unless(Storage::disk('public')->exists($sertifikat->file_sertifikat), 404);
            return Storage::disk('public')->download($sertifikat->file_sertifikat);
        }

        $anggota = auth()->user()->anggota;

        if (! $anggota || (int) $sertifikat->anggota_id !== $anggota->id) {
            abort(403);
        }

        if (! app(VerifiedAttendance::class)->meetsRequirement($sertifikat->kegiatan, $anggota)) {
            abort(403);
        }

        abort_unless(Storage::disk('public')->exists($sertifikat->file_sertifikat), 404);
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
