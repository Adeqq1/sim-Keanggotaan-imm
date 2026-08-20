<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Support\QrCodeHelper;
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
        $logoSrc = $this->logoSource();
        $qrCodeSrc = QrCodeHelper::generateDataUri(QrCodeHelper::makeVerificationPayload($anggota));

        return view('kader.ekta.show', compact('anggota', 'roleLabel', 'photoSrc', 'logoSrc', 'qrCodeSrc'));
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
        $logoSrc = $this->logoSource(true);
        $qrCodeSrc = QrCodeHelper::generateDataUri(QrCodeHelper::makeVerificationPayload($anggota));

        $pdf = Pdf::loadView('pdf.ekta', compact('anggota', 'roleLabel', 'photoSrc', 'logoSrc', 'qrCodeSrc'))
            ->setPaper([0, 0, 240, 152.25]);

        $filename = 'E-KTA_'.(filled($anggota->nia) ? $anggota->nia : $anggota->id).'.pdf';

        return $pdf->download($filename);
    }

    private function photoSource(?string $path, bool $forPdf = false): ?string
    {
        $disk = Storage::disk('public');

        if (! filled($path) || ! $disk->exists($path)) {
            return null;
        }

        if ($forPdf) {
            return $disk->path($path);
        }

        $mimeType = $disk->mimeType($path);
        if (! is_string($mimeType) || ! str_starts_with($mimeType, 'image/')) {
            $mimeType = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
                'gif' => 'image/gif',
                'png' => 'image/png',
                'webp' => 'image/webp',
                default => 'image/jpeg',
            };
        }

        return 'data:'.$mimeType.';base64,'.base64_encode($disk->get($path));
    }

    private function logoSource(bool $forPdf = false): ?string
    {
        $path = public_path('images/logo.png');

        if (! is_file($path)) {
            return null;
        }

        return $forPdf ? $path : asset('images/logo.png');
    }
}
