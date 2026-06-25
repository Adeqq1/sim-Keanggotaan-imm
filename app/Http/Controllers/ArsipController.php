<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArsipRequest;
use App\Http\Requests\KaderArsipRequest;
use App\Models\Arsip;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function index()
    {
        $arsips = Arsip::with('anggota')->latest()->paginate(10);

        return view('admin.arsip.index', compact('arsips'));
    }

    public function kaderIndex()
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Profil anggota Anda belum dibuat. Silakan hubungi admin.');
        }

        $arsips = Arsip::where('anggota_id', $anggota->id)->latest()->paginate(10);

        return view('kader.arsip.index', compact('arsips'));
    }

    public function store(ArsipRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_arsip')) {
            $path = $request->file('file_arsip')->store('arsip', 'public');
            $validated['file_arsip'] = $path;
        }

        Arsip::create($validated);

        return redirect()->back()->with('success', 'Arsip berhasil diunggah.');
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

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
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
