<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresensiRequest;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use App\Support\SortParams;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $options = ['nama' => 'Nama', 'tanggal' => 'Tanggal Kegiatan', 'lokasi' => 'Lokasi', 'peserta' => 'Peserta', 'hadir' => 'Hadir', 'izin' => 'Izin', 'alfa' => 'Alfa'];
        $sort = SortParams::resolve($request, array_keys($options), 'tanggal');
        $kegiatans = Kegiatan::query()
            ->withCount([
                'presensi',
                'presensi as hadir_count' => fn ($query) => $query->where('status_kehadiran', 'hadir'),
                'presensi as izin_count' => fn ($query) => $query->where('status_kehadiran', 'izin'),
                'presensi as alfa_count' => fn ($query) => $query->where('status_kehadiran', 'alfa'),
            ])
            ->orderBy(['nama' => 'nama_kegiatan', 'tanggal' => 'tanggal_waktu', 'lokasi' => 'lokasi', 'peserta' => 'presensi_count', 'hadir' => 'hadir_count', 'izin' => 'izin_count', 'alfa' => 'alfa_count'][$sort['key']], $sort['direction'])
            ->orderByDesc('kegiatan.id')->paginate(12)->withQueryString();

        return view('admin.kegiatan.rekap-presensi', compact('kegiatans', 'options', 'sort'));
    }

    public function create(Request $request, Kegiatan $kegiatan)
    {
        $options = ['nama' => 'Nama', 'nia' => 'NIA'];
        $sort = SortParams::resolve($request, array_keys($options), 'nama', 'asc');
        $anggotas = Anggota::where('status_aktif', true)
            ->orderBy(['nama' => 'nama_lengkap', 'nia' => 'nia'][$sort['key']], $sort['direction'])->orderByDesc('id')->get();
        $presensis = $kegiatan->presensi;
        $canManagePresensi = auth()->user()->role === 'instruktur';

        return view('admin.kegiatan.presensi', compact('kegiatan', 'anggotas', 'presensis', 'canManagePresensi', 'options', 'sort'));
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
