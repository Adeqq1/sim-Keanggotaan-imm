<?php

namespace App\Http\Controllers;

use App\Http\Requests\KegiatanRequest;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Cache;
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
        $materiPaths = $kegiatan->materiKegiatans()->pluck('file_materi');

        if ($kegiatan->thumbnail) {
            Storage::disk('public')->delete($kegiatan->thumbnail);
        }

        $kegiatan->delete();

        foreach ($materiPaths as $path) {
            try {
                if (Storage::disk('local')->exists($path) && ! Storage::disk('local')->delete($path)) {
                    report(new RuntimeException("Gagal menghapus materi kegiatan {$kegiatan->id}: {$path}"));
                }
            } catch (Throwable $exception) {
                report(new RuntimeException("Gagal menghapus materi kegiatan {$kegiatan->id}: {$path}", previous: $exception));
            }
        }

        Cache::forget('kegiatan.terbaru');

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
