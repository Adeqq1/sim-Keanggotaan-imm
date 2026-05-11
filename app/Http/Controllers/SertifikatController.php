<?php

namespace App\Http\Controllers;

use App\Http\Requests\SertifikatRequest;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Sertifikat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{
    public function index()
    {
        $sertifikats = Sertifikat::with(['kegiatan', 'anggota'])->latest()->paginate(10);

        return view('admin.sertifikat.index', compact('sertifikats'));
    }

    public function create()
    {
        $kegiatans = Kegiatan::latest()->get();
        $anggotas = Anggota::where('status_aktif', true)->get();

        return view('admin.sertifikat.create', compact('kegiatans', 'anggotas'));
    }

    public function generate(SertifikatRequest $request)
    {
        $validated = $request->validated();
        $kegiatan = Kegiatan::findOrFail($validated['kegiatan_id']);

        foreach ($validated['anggota_ids'] as $anggotaId) {
            $anggota = Anggota::findOrFail($anggotaId);
            $nomorSertifikat = 'CERT-'.$kegiatan->id.'-'.$anggota->id.'-'.now()->format('Ymd');

            // Generate PDF
            $pdf = Pdf::loadView('pdf.sertifikat', compact('kegiatan', 'anggota', 'nomorSertifikat'));
            $path = 'sertifikat/'.$nomorSertifikat.'.pdf';
            Storage::disk('public')->put($path, $pdf->output());

            Sertifikat::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'anggota_id' => $anggota->id],
                [
                    'nomor_sertifikat' => $nomorSertifikat,
                    'file_sertifikat' => $path,
                ]
            );
        }

        return redirect()->route('admin.sertifikat.index')->with('success', 'Sertifikat berhasil di-generate.');
    }

    public function mySertifikat()
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $sertifikats = Sertifikat::where('anggota_id', $anggota->id)->with('kegiatan')->latest()->get();

        return view('kader.sertifikat.index', compact('sertifikats'));
    }

    public function download(Sertifikat $sertifikat)
    {
        // Pastikan hanya pemilik atau admin yang bisa download
        if (auth()->user()->role !== 'admin' && auth()->user()->anggota->id !== $sertifikat->anggota_id) {
            abort(403);
        }

        return Storage::disk('public')->download($sertifikat->file_sertifikat);
    }
}
