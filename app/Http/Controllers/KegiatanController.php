<?php

namespace App\Http\Controllers;

use App\Http\Requests\KegiatanRequest;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Expression;
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
        $baseQuery = Kegiatan::with('tahunAngkatans')
            ->orderBy($columns[$sort['key']], $sort['direction'])
            ->orderByDesc('id');
        $upcomingKegiatans = (clone $baseQuery)->where('tanggal_waktu', '>', now())->paginate(6, ['*'], 'upcoming_page')->withQueryString();
        $pastKegiatans = (clone $baseQuery)->where('tanggal_waktu', '<=', now())->paginate(6, ['*'], 'past_page')->withQueryString();

        $allKegiatans = $upcomingKegiatans->getCollection()->concat($pastKegiatans->getCollection());

        return view('admin.kegiatan.index', compact('upcomingKegiatans', 'pastKegiatans', 'allKegiatans', 'options', 'sort'));
    }

    public function create()
    {
        return view('admin.kegiatan.create');
    }

    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load('tahunAngkatans');
        $kegiatan->load('laporanKegiatan')->loadCount([
            'presensi as presensi_count' => fn ($query) => $query->select(new Expression('count(distinct anggota_id)')),
            'presensi as hadir_count' => fn ($query) => $query->where('status_kehadiran', 'hadir')->select(new Expression('count(distinct anggota_id)')),
            'presensi as izin_count' => fn ($query) => $query->where('status_kehadiran', 'izin')->whereNotExists(fn ($subquery) => $subquery->from('presensi as hadir')->whereColumn('hadir.kegiatan_id', 'presensi.kegiatan_id')->whereColumn('hadir.anggota_id', 'presensi.anggota_id')->where('hadir.status_kehadiran', 'hadir'))->select(new Expression('count(distinct anggota_id)')),
            'presensi as alfa_count' => fn ($query) => $query->where('status_kehadiran', 'alfa')->whereNotExists(fn ($subquery) => $subquery->from('presensi as prior')->whereColumn('prior.kegiatan_id', 'presensi.kegiatan_id')->whereColumn('prior.anggota_id', 'presensi.anggota_id')->whereIn('prior.status_kehadiran', ['hadir', 'izin']))->select(new Expression('count(distinct anggota_id)')),
        ]);

        return view('admin.kegiatan.show', compact('kegiatan'));
    }

    public function store(KegiatanRequest $request)
    {
        $data = $request->validated();
        $tahunAngkatan = $data['tahun_angkatan'];
        unset($data['tahun_angkatan']);
        $newThumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $newThumbnailPath = $request->file('thumbnail')->store('kegiatan_thumbnails', 'public');
            $data['thumbnail'] = $newThumbnailPath;
        }

        try {
            DB::transaction(function () use ($data, $tahunAngkatan, &$kegiatan): void {
                $kegiatan = Kegiatan::create($data);
                $kegiatan->tahunAngkatans()->createMany(collect($tahunAngkatan)->map(fn ($tahun) => ['tahun_daftar' => $tahun])->all());
                $kegiatan->sesiKegiatans()->create([
                    'urutan' => 1,
                    'nama_sesi' => 'Sesi 1',
                    'mulai_pada' => $kegiatan->tanggal_waktu,
                ]);
            });
        } catch (Throwable $exception) {
            $this->deleteFile('public', $newThumbnailPath, 'thumbnail kegiatan baru');

            throw $exception;
        }

        Cache::forget('kegiatan.terbaru');

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Kegiatan $kegiatan)
    {
        $kegiatan->load('tahunAngkatans');
        return view('admin.kegiatan.edit', compact('kegiatan'));
    }

    public function update(KegiatanRequest $request, Kegiatan $kegiatan)
    {
        $data = $request->validated();
        $tahunAngkatan = collect($data['tahun_angkatan'])->map(fn ($tahun) => (int) $tahun)->sort()->values()->all();
        unset($data['tahun_angkatan']);
        $currentTahunAngkatan = $kegiatan->tahunAngkatans()->pluck('tahun_daftar')->map(fn ($tahun) => (int) $tahun)->sort()->values()->all();
        if ($kegiatan->presensi()->exists() && $currentTahunAngkatan !== $tahunAngkatan) {
            return back()->withErrors(['tahun_angkatan' => 'Target angkatan tidak dapat diubah setelah presensi tercatat.'])->withInput();
        }
        if ($kegiatan->presensi()->exists() && ($kegiatan->jenis_pelaksanaan !== $data['jenis_pelaksanaan'] || $kegiatan->minimum_sesi_terverifikasi !== (int) $data['minimum_sesi_terverifikasi'])) {
            return back()->withErrors(['jenis_pelaksanaan' => 'Kebijakan kegiatan tidak dapat diubah setelah presensi tercatat.'])->withInput();
        }
        $oldThumbnailPath = $kegiatan->thumbnail;
        $newThumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $newThumbnailPath = $request->file('thumbnail')->store('kegiatan_thumbnails', 'public');
            $data['thumbnail'] = $newThumbnailPath;
        }

        try {
            DB::transaction(function () use ($kegiatan, $data, $tahunAngkatan) {
                $kegiatan->update($data);
                $kegiatan->tahunAngkatans()->delete();
                $kegiatan->tahunAngkatans()->createMany(collect($tahunAngkatan)->map(fn ($tahun) => ['tahun_daftar' => $tahun])->all());
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
