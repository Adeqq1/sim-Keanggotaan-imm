<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class EktaController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $anggota = $user->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $roleLabel = RoleEnum::labelFor($user->role);
        $photoSrc = $this->photoSource($anggota->foto_profil);

        return view('kader.ekta.show', compact('anggota', 'roleLabel', 'photoSrc'));
    }

    public function download()
    {
        $user = auth()->user();
        $anggota = $user->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $roleLabel = RoleEnum::labelFor($user->role);
        $photoSrc = $this->photoSource($anggota->foto_profil, true);

        $pdf = Pdf::loadView('pdf.ekta', compact('anggota', 'roleLabel', 'photoSrc'))
            ->setPaper([0, 0, 240, 152.25]);

        $filename = 'E-KTA_'.(filled($anggota->nia) ? $anggota->nia : $anggota->id).'.pdf';

        return $pdf->download($filename);
    }

    private function photoSource(?string $path, bool $forPdf = false): ?string
    {
        if (! filled($path) || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return $forPdf
            ? Storage::disk('public')->path($path)
            : Storage::disk('public')->url($path);
    }
}
