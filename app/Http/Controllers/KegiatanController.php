<?php

namespace App\Http\Controllers;

use App\Http\Requests\KegiatanRequest;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Support\SortParams;
use RuntimeException;
use Throwable;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $options = ['nama' => 'Nama', 'tanggal' => 'Tanggal Kegiatan', 'lokasi' => 'Lokasi', 'created' => 'Waktu Ditambahkan'];
        $sort = SortParams::resolve($request, array_keys($options), 'created');
        $columns = ['nama' => 'nama_kegiatan', 'tanggal' => 'tanggal_waktu', 'lokasi' => 'lokasi', 'created' => 'created_at'];
        $kegiatans = Kegiatan::orderBy($columns[$sort['key']], $sort['direction'])->orderByDesc('id')->paginate(12)->withQueryString();

        return view('admin.kegiatan.index', compact('kegiatans', 'options', 'sort'));
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

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $path = 'kegiatan_thumbnails/'.$file->hashName();
            $stream = fopen($file->getPathname(), 'r');
            Storage::disk('public')->put($path, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            $data['thumbnail'] = $path;
        }

        Kegiatan::create($data);
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

        if ($request->hasFile('thumbnail')) {
            if ($kegiatan->thumbnail) {
                Storage::disk('public')->delete($kegiatan->thumbnail);
            }
            $file = $request->file('thumbnail');
            $path = 'kegiatan_thumbnails/'.$file->hashName();
            $stream = fopen($file->getPathname(), 'r');
            Storage::disk('public')->put($path, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            $data['thumbnail'] = $path;
        }

        $kegiatan->update($data);
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
