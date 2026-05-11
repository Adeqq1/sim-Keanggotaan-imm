<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function create(Kegiatan $kegiatan)
    {
        $anggotas = Anggota::where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $presensis = $kegiatan->presensis;

        return view('admin.kegiatan.presensi', compact('kegiatan', 'anggotas', 'presensis'));
    }

    public function store(Request $request, Kegiatan $kegiatan)
    {
        $presensiData = $request->input('presensi', []);

        foreach ($presensiData as $anggotaId => $status) {
            Presensi::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'anggota_id' => $anggotaId],
                [
                    'status_kehadiran' => $status,
                    'waktu_hadir' => $status === 'hadir' ? now() : null,
                ]
            );
        }

        return redirect()->route('admin.kegiatan.index')->with('success', 'Presensi berhasil disimpan.');
    }
}
