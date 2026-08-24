<?php

namespace App\Http\Controllers;

use App\Http\Requests\SesiKegiatanRequest;
use App\Models\Kegiatan;
use App\Models\SesiKegiatan;
use Illuminate\Http\RedirectResponse;

class SesiKegiatanController extends Controller
{
    public function index(Kegiatan $kegiatan)
    {
        return view('admin.kegiatan.sesi.index', ['kegiatan' => $kegiatan, 'sesies' => $kegiatan->sesiKegiatans]);
    }

    public function store(SesiKegiatanRequest $request, Kegiatan $kegiatan): RedirectResponse
    {
        if ($kegiatan->jenis_pelaksanaan === Kegiatan::BELUM_DITETAPKAN) {
            return back()->withErrors(['sesi' => 'Tetapkan kebijakan pelaksanaan kegiatan terlebih dahulu.']);
        }

        if ($kegiatan->jenis_pelaksanaan === Kegiatan::SATU_SESI && $kegiatan->sesiKegiatans()->exists()) {
            return back()->withErrors(['sesi' => 'Kegiatan satu sesi hanya boleh memiliki satu sesi.']);
        }

        $kegiatan->sesiKegiatans()->create($request->validated());

        return back()->with('success', 'Sesi kegiatan berhasil ditambahkan.');
    }

    public function update(SesiKegiatanRequest $request, Kegiatan $kegiatan, SesiKegiatan $sesiKegiatan): RedirectResponse
    {
        if ($sesiKegiatan->presensis()->exists() && ($sesiKegiatan->urutan != $request->validated('urutan') || ! $sesiKegiatan->mulai_pada->equalTo($request->date('mulai_pada')))) {
            return back()->withErrors(['sesi' => 'Urutan dan waktu sesi yang sudah memiliki presensi tidak dapat diubah.']);
        }

        $sesiKegiatan->update($request->validated());

        return back()->with('success', 'Sesi kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan, SesiKegiatan $sesiKegiatan): RedirectResponse
    {
        if ($sesiKegiatan->presensis()->exists()) {
            return back()->withErrors(['sesi' => 'Sesi yang sudah memiliki presensi tidak dapat dihapus.']);
        }

        $sesiKegiatan->delete();

        return back()->with('success', 'Sesi kegiatan berhasil dihapus.');
    }
}
