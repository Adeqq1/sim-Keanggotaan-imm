<?php

namespace App\Http\Controllers;

use App\Http\Requests\KegiatanRequest;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::latest()->paginate(12);

        return view('admin.kegiatan.index', compact('kegiatans'));
    }

    public function create()
    {
        return view('admin.kegiatan.create');
    }

    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load('laporanKegiatan')->loadCount([
            'presensi',
            'presensi as hadir_count' => fn ($query) => $query->where('status_kehadiran', 'hadir'),
            'presensi as izin_count' => fn ($query) => $query->where('status_kehadiran', 'izin'),
            'presensi as alfa_count' => fn ($query) => $query->where('status_kehadiran', 'alfa'),
        ]);

        return view('admin.kegiatan.show', compact('kegiatan'));
    }

    public function store(KegiatanRequest $request)
    {
        $data = $request->validated();
        $newThumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $newThumbnailPath = $request->file('thumbnail')->store('kegiatan_thumbnails', 'public');
            $data['thumbnail'] = $newThumbnailPath;
        }

        try {
            Kegiatan::create($data);
        } catch (Throwable $exception) {
            $this->deleteFile('public', $newThumbnailPath, 'thumbnail kegiatan baru');

            throw $exception;
        }

        Cache::forget('kegiatan.terbaru');

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Kegiatan $kegiatan)
    {
        return view('admin.kegiatan.edit', compact('kegiatan'));
    }

    public function update(KegiatanRequest $request, Kegiatan $kegiatan)
    {
        $data = $request->validated();
        $oldThumbnailPath = $kegiatan->thumbnail;
        $newThumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $newThumbnailPath = $request->file('thumbnail')->store('kegiatan_thumbnails', 'public');
            $data['thumbnail'] = $newThumbnailPath;
        }

        try {
            DB::transaction(function () use ($kegiatan, $data) {
                $kegiatan->update($data);
            });
        } catch (Throwable $exception) {
            $this->deleteFile('public', $newThumbnailPath, 'thumbnail kegiatan baru');

            throw $exception;
        }

        if ($newThumbnailPath !== null && $oldThumbnailPath !== $newThumbnailPath) {
            $this->deleteFile('public', $oldThumbnailPath, "thumbnail kegiatan {$kegiatan->id}");
        }

        Cache::forget('kegiatan.terbaru');

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil diupdate.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        [$materiPaths, $laporanPath, $thumbnail] = DB::transaction(function () use ($kegiatan): array {
            $locked = Kegiatan::query()->lockForUpdate()->findOrFail($kegiatan->id);
            $materiPaths = $locked->materiKegiatans()->pluck('file_materi');
            $laporanPath = $locked->laporanKegiatan()->lockForUpdate()->value('file_lampiran');
            $thumbnail = $locked->thumbnail;
            $locked->delete();

            return [$materiPaths, $laporanPath, $thumbnail];
        });

        $this->deleteFile('public', $thumbnail, "thumbnail kegiatan {$kegiatan->id}");

        foreach ($materiPaths as $path) {
            $this->deleteFile('local', $path, "materi kegiatan {$kegiatan->id}");
        }

        $this->deleteFile('local', $laporanPath, "lampiran laporan kegiatan {$kegiatan->id}");

        Cache::forget('kegiatan.terbaru');

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    private function deleteFile(string $diskName, ?string $path, string $context): void
    {
        if (! $path) {
            return;
        }

        try {
            $disk = Storage::disk($diskName);

            if ($disk->exists($path) && ! $disk->delete($path)) {
                report(new RuntimeException("Gagal menghapus {$context}: {$path}"));
            }
        } catch (Throwable $exception) {
            report(new RuntimeException("Gagal menghapus {$context}: {$path}", previous: $exception));
        }
    }
}
