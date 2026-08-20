<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaporanKegiatanRequest;
use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use Illuminate\Http\Request;
use App\Support\SortParams;

class LaporanKegiatanController extends Controller
{
    public function index(Request $request): View
    {
        $options = ['nama' => 'Nama', 'tanggal' => 'Tanggal Kegiatan', 'laporan' => 'Status Laporan', 'peserta' => 'Peserta', 'hadir' => 'Hadir', 'izin' => 'Izin', 'alfa' => 'Alfa'];
        $sort = SortParams::resolve($request, array_keys($options), 'tanggal');
        $kegiatans = Kegiatan::query()
            ->with('laporanKegiatan')
            ->withCount([
                'presensi',
                'presensi as hadir_count' => fn ($query) => $query->where('status_kehadiran', 'hadir'),
                'presensi as izin_count' => fn ($query) => $query->where('status_kehadiran', 'izin'),
                'presensi as alfa_count' => fn ($query) => $query->where('status_kehadiran', 'alfa'),
            ])
            ->withExists('laporanKegiatan as laporan_exists')
            ->orderBy(['nama' => 'nama_kegiatan', 'tanggal' => 'tanggal_waktu', 'laporan' => 'laporan_exists', 'peserta' => 'presensi_count', 'hadir' => 'hadir_count', 'izin' => 'izin_count', 'alfa' => 'alfa_count'][$sort['key']], $sort['direction'])
            ->orderByDesc('kegiatan.id')->paginate(12)->withQueryString();

        return view('admin.kegiatan.laporan.index', compact('kegiatans', 'options', 'sort'));
    }

    public function create(Kegiatan $kegiatan): View|RedirectResponse
    {
        if ($laporanKegiatan = $kegiatan->laporanKegiatan) {
            return redirect()->route('admin.laporan-kegiatan.edit', $laporanKegiatan)
                ->with('info', 'Laporan kegiatan sudah tersedia. Silakan ubah laporan yang ada.');
        }

        $kegiatan->loadCount('presensi');

        return view('admin.kegiatan.laporan.create', compact('kegiatan'));
    }

    public function store(LaporanKegiatanRequest $request, Kegiatan $kegiatan): RedirectResponse
    {
        if ($existing = $kegiatan->laporanKegiatan()->first()) {
            return redirect()->route('admin.laporan-kegiatan.edit', $existing)
                ->with('info', 'Laporan kegiatan sudah tersedia.');
        }

        $data = $request->safe()->except('file_lampiran');
        $path = $request->hasFile('file_lampiran') ? $this->storeFile($request->file('file_lampiran')) : null;

        try {
            [$laporanKegiatan, $created] = DB::transaction(function () use ($kegiatan, $data, $path): array {
                $lockedKegiatan = Kegiatan::query()->lockForUpdate()->findOrFail($kegiatan->id);
                $existing = $lockedKegiatan->laporanKegiatan()->first();

                if ($existing) {
                    return [$existing, false];
                }

                return [$lockedKegiatan->laporanKegiatan()->create([...$data, 'file_lampiran' => $path]), true];
            });
        } catch (Throwable $exception) {
            $this->deleteFile($path, "lampiran baru kegiatan {$kegiatan->id}");
            throw $exception;
        }

        if (! $created) {
            $this->deleteFile($path, "lampiran duplikat kegiatan {$kegiatan->id}");

            return redirect()->route('admin.laporan-kegiatan.edit', $laporanKegiatan)
                ->with('info', 'Laporan kegiatan sudah tersedia.');
        }

        return redirect()->route('admin.laporan-kegiatan.show', $laporanKegiatan)
            ->with('success', 'Laporan kegiatan berhasil dibuat.');
    }

    public function show(LaporanKegiatan $laporanKegiatan): View
    {
        $laporanKegiatan->load(['kegiatan' => fn ($query) => $query->withCount([
            'presensi',
            'presensi as hadir_count' => fn ($query) => $query->where('status_kehadiran', 'hadir'),
            'presensi as izin_count' => fn ($query) => $query->where('status_kehadiran', 'izin'),
            'presensi as alfa_count' => fn ($query) => $query->where('status_kehadiran', 'alfa'),
        ])]);

        return view('admin.kegiatan.laporan.show', compact('laporanKegiatan'));
    }

    public function edit(LaporanKegiatan $laporanKegiatan): View
    {
        $laporanKegiatan->load('kegiatan');

        return view('admin.kegiatan.laporan.edit', compact('laporanKegiatan'));
    }

    public function update(LaporanKegiatanRequest $request, LaporanKegiatan $laporanKegiatan): RedirectResponse
    {
        $data = $request->safe()->except('file_lampiran');
        $newPath = $request->hasFile('file_lampiran') ? $this->storeFile($request->file('file_lampiran')) : null;
        $oldPath = null;

        try {
            DB::transaction(function () use ($laporanKegiatan, $data, $newPath, &$oldPath): void {
                $locked = LaporanKegiatan::query()->lockForUpdate()->findOrFail($laporanKegiatan->id);
                $oldPath = $locked->file_lampiran;
                $locked->update($newPath ? [...$data, 'file_lampiran' => $newPath] : $data);
            });
        } catch (Throwable $exception) {
            $this->deleteFile($newPath, "lampiran pengganti laporan {$laporanKegiatan->id}");
            throw $exception;
        }

        if ($newPath && $oldPath !== $newPath) {
            $this->deleteFile($oldPath, "lampiran laporan {$laporanKegiatan->id}");
        }

        return redirect()->route('admin.laporan-kegiatan.show', $laporanKegiatan)
            ->with('success', 'Laporan kegiatan berhasil diperbarui.');
    }

    public function destroy(LaporanKegiatan $laporanKegiatan): RedirectResponse
    {
        $id = $laporanKegiatan->id;
        $path = DB::transaction(function () use ($id): ?string {
            $locked = LaporanKegiatan::query()->lockForUpdate()->findOrFail($id);
            $path = $locked->file_lampiran;
            $locked->delete();

            return $path;
        });

        $this->deleteFile($path, "lampiran laporan {$id}");

        return redirect()->route('admin.laporan-kegiatan.index')
            ->with('success', 'Laporan kegiatan berhasil dihapus.');
    }

    public function downloadLampiran(LaporanKegiatan $laporanKegiatan): StreamedResponse
    {
        $path = $laporanKegiatan->file_lampiran;

        if (! is_string($path) || $path === '' || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $name = Str::slug($laporanKegiatan->kegiatan->nama_kegiatan) ?: "kegiatan-{$laporanKegiatan->kegiatan_id}";

        return Storage::disk('local')->download($path, "{$name}-lampiran.{$extension}", [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function downloadPdf(LaporanKegiatan $laporanKegiatan): mixed
    {
        $laporanKegiatan->load(['kegiatan' => fn ($query) => $query->withCount([
            'presensi',
            'presensi as hadir_count' => fn ($query) => $query->where('status_kehadiran', 'hadir'),
            'presensi as izin_count' => fn ($query) => $query->where('status_kehadiran', 'izin'),
            'presensi as alfa_count' => fn ($query) => $query->where('status_kehadiran', 'alfa'),
        ])]);

        $kegiatan = $laporanKegiatan->kegiatan;
        $pdf = Pdf::loadView('pdf.laporan-kegiatan', compact('laporanKegiatan', 'kegiatan'))
            ->setPaper('a4', 'portrait');
        $slug = Str::slug($kegiatan->nama_kegiatan) ?: "kegiatan-{$kegiatan->id}";
        $date = $kegiatan->tanggal_waktu->format('Ymd');

        return $pdf->download("laporan-kegiatan-{$slug}-{$date}.pdf");
    }

    private function storeFile(?UploadedFile $file): string
    {
        try {
            $path = $file?->store('laporan_kegiatan', 'local');
        } catch (Throwable) {
            $path = null;
        }

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'file_lampiran' => 'File lampiran gagal disimpan. Silakan coba lagi.',
            ]);
        }

        return $path;
    }

    private function deleteFile(?string $path, string $context): void
    {
        if (! $path) {
            return;
        }

        try {
            $disk = Storage::disk('local');

            if ($disk->exists($path) && ! $disk->delete($path)) {
                report(new RuntimeException("Gagal menghapus {$context}: {$path}"));
            }
        } catch (Throwable $exception) {
            report(new RuntimeException("Gagal menghapus {$context}: {$path}", previous: $exception));
        }
    }
}
