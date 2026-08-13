<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresensiRequest;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;

class PresensiController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::query()
            ->withCount([
                'presensi',
                'presensi as hadir_count' => fn ($query) => $query->where('status_kehadiran', 'hadir'),
                'presensi as izin_count' => fn ($query) => $query->where('status_kehadiran', 'izin'),
                'presensi as alfa_count' => fn ($query) => $query->where('status_kehadiran', 'alfa'),
            ])
            ->latest('tanggal_waktu')
            ->paginate(12);

        return view('admin.kegiatan.rekap-presensi', compact('kegiatans'));
    }

    public function create(Kegiatan $kegiatan)
    {
        $anggotas = Anggota::where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $presensis = $kegiatan->presensi;
        $canManagePresensi = auth()->user()->role === 'instruktur';

        return view('admin.kegiatan.presensi', compact('kegiatan', 'anggotas', 'presensis', 'canManagePresensi'));
    }

    public function store(PresensiRequest $request, Kegiatan $kegiatan)
    {
        foreach ($request->validated('presensi') as $data) {
            $status = $data['status_kehadiran'];

            Presensi::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'anggota_id' => $data['anggota_id']],
                [
                    'status_kehadiran' => $status,
                    'waktu_hadir' => $status === 'hadir' ? now() : null,
                ]
            );
        }

        return redirect()->route('admin.kegiatan.index')->with('success', 'Presensi berhasil disimpan.');
    }
}
