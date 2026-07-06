<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArsipRequest;
use App\Http\Requests\KaderArsipRequest;
use App\Models\Anggota;
use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = Arsip::with('anggota')->latest();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('judul_dokumen', 'like', "%{$q}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_arsip', $request->input('kategori'));
        }

        return view('admin.arsip.index', [
            'arsips' => $query->paginate(10)->withQueryString(),
            'kategori' => Arsip::KATEGORI,
        ]);
    }

    public function create()
    {
        return view('admin.arsip.create', [
            'anggotas' => Anggota::orderBy('nama_lengkap')->get(),
            'kategori' => Arsip::KATEGORI,
        ]);
    }

    public function kaderIndex(Request $request)
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Profil anggota Anda belum dibuat. Silakan hubungi admin.');
        }

        $query = Arsip::where('anggota_id', $anggota->id)->latest();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('judul_dokumen', 'like', "%{$q}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_arsip', $request->input('kategori'));
        }

        return view('kader.arsip.index', [
            'arsips' => $query->paginate(10)->withQueryString(),
            'kategori' => Arsip::KATEGORI,
        ]);
    }

    public function kaderCreate()
    {
        return view('kader.arsip.create', [
            'kategori' => Arsip::KATEGORI,
        ]);
    }

    public function store(ArsipRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_arsip')) {
            $path = $request->file('file_arsip')->store('arsip', 'public');
            $validated['file_arsip'] = $path;
        }

        $validated['tanggal_unggah'] = now()->toDateString();

        Arsip::create($validated);

        return redirect()->route('admin.arsip.index')->with('success', 'Arsip berhasil diunggah.');
    }

    public function kaderStore(KaderArsipRequest $request)
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->back()->with('error', 'Data anggota Anda tidak ditemukan.');
        }

        $validated = $request->validated();

        if ($request->hasFile('file_arsip')) {
            $path = $request->file('file_arsip')->store('arsip', 'public');
            $validated['file_arsip'] = $path;
        }

        $validated['anggota_id'] = $anggota->id;
        $validated['tanggal_unggah'] = now()->toDateString();

        Arsip::create($validated);

        return redirect()->route('kader.arsip.index')->with('success', 'Dokumen berhasil diunggah.');
    }

    public function download(Arsip $arsip)
    {
        $extension = pathinfo($arsip->file_arsip, PATHINFO_EXTENSION);
        $filename = str_replace(' ', '_', $arsip->judul_dokumen).'.'.$extension;

        return Storage::disk('public')->download($arsip->file_arsip, $filename);
    }

    public function kaderDownload(Arsip $arsip)
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota || $arsip->anggota_id !== $anggota->id) {
            abort(403, 'Anda tidak memiliki akses ke arsip ini.');
        }

        $extension = pathinfo($arsip->file_arsip, PATHINFO_EXTENSION);
        $filename = str_replace(' ', '_', $arsip->judul_dokumen).'.'.$extension;

        return Storage::disk('public')->download($arsip->file_arsip, $filename);
    }

    public function destroy(Arsip $arsip)
    {
        if ($arsip->file_arsip) {
            Storage::disk('public')->delete($arsip->file_arsip);
        }

        $arsip->delete();

        return redirect()->back()->with('success', 'Arsip berhasil dihapus.');
    }
}
