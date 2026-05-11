<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArsipRequest;
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
        $arsips = Arsip::latest()->paginate(10);

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

    public function download(Arsip $arsip)
    {
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
