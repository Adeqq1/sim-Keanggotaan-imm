<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class EktaController extends Controller
{
    public function show()
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        return view('kader.ekta.show', compact('anggota'));
    }

    public function download()
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $pdf = Pdf::loadView('pdf.ekta', compact('anggota'));

        $filename = 'E-KTA_'.($anggota->nia ?? $anggota->id).'.pdf';

        return $pdf->download($filename);
    }
}
