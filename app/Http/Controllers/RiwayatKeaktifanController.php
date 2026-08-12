<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Sertifikat;

class RiwayatKeaktifanController extends Controller
{
    public function index()
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $presensiQuery = Presensi::where('anggota_id', $anggota->id);

        $presensis = (clone $presensiQuery)
            ->with('kegiatan')
            ->latest()
            ->paginate(6);

        $sertifikats = Sertifikat::where('anggota_id', $anggota->id)
            ->get()
            ->keyBy('kegiatan_id');

        $stats = [
            'hadir' => (clone $presensiQuery)->where('status_kehadiran', 'hadir')->count(),
            'izin' => (clone $presensiQuery)->where('status_kehadiran', 'izin')->count(),
            'alfa' => (clone $presensiQuery)->where('status_kehadiran', 'alfa')->count(),
        ];
        $jumlahKegiatanHadir = $anggota->jumlahKegiatanHadir();
        $canClaimSertifikat = $jumlahKegiatanHadir >= Sertifikat::MINIMUM_KEGIATAN_HADIR;
        $minimumKegiatanHadir = Sertifikat::MINIMUM_KEGIATAN_HADIR;

        return view('kader.riwayat.index', compact(
            'presensis',
            'sertifikats',
            'stats',
            'jumlahKegiatanHadir',
            'canClaimSertifikat',
            'minimumKegiatanHadir',
        ));
    }
}
