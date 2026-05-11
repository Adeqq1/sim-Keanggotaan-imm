<?php

namespace App\Http\Controllers;

use App\Models\Presensi;

class RiwayatKeaktifanController extends Controller
{
    public function index()
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $presensis = Presensi::where('anggota_id', $anggota->id)
            ->with('kegiatan')
            ->latest()
            ->get();

        $stats = [
            'hadir' => $presensis->where('status_kehadiran', 'hadir')->count(),
            'izin' => $presensis->where('status_kehadiran', 'izin')->count(),
            'alfa' => $presensis->where('status_kehadiran', 'alfa')->count(),
        ];

        return view('kader.riwayat.index', compact('presensis', 'stats'));
    }
}
