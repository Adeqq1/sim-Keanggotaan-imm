<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Sertifikat;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $stats = [
            'total_anggota' => Anggota::where('status_aktif', true)->count(),
            'total_kegiatan' => Kegiatan::count(),
            'pendaftar_pending' => Pendaftaran::where('status_validasi', 'pending')->count(),
            'total_arsip' => Arsip::count(),
        ];

        $recent_kegiatans = Kegiatan::where('tanggal_waktu', '>=', now())
            ->orderBy('tanggal_waktu', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_kegiatans'));
    }

    public function kaderDashboard()
    {
        $user = auth()->user();
        $anggota = $user->anggota;

        if (! $anggota) {
            return redirect()->route('pendaftaran')->with('error', 'Data anggota tidak ditemukan. Silakan mendaftar terlebih dahulu.');
        }

        $stats = [
            'total_kehadiran' => Presensi::where('anggota_id', $anggota->id)->where('status_kehadiran', 'hadir')->count(),
            'total_sertifikat' => Sertifikat::where('anggota_id', $anggota->id)->count(),
        ];

        $kegiatan_terdekat = Kegiatan::where('tanggal_waktu', '>', now())
            ->orderBy('tanggal_waktu')
            ->take(3)
            ->get();

        return view('kader.dashboard', compact('stats', 'kegiatan_terdekat'));
    }
}
