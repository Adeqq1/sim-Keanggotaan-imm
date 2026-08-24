<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use App\Support\SortParams;
use App\Services\VerifiedAttendance;

class RiwayatKeaktifanController extends Controller
{
    public function index(Request $request)
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota) {
            return redirect()->route('kader.dashboard')->with('error', 'Data anggota tidak ditemukan.');
        }

        $presensiQuery = Presensi::where('anggota_id', $anggota->id);
        $options = ['kegiatan' => 'Nama Kegiatan', 'tanggal_kegiatan' => 'Tanggal Kegiatan', 'status' => 'Status', 'waktu_hadir' => 'Waktu Hadir', 'created' => 'Waktu Ditambahkan'];
        $sort = SortParams::resolve($request, array_keys($options), 'created');

        $presensiQuery = (clone $presensiQuery)->with(['kegiatan', 'sesiKegiatan']);
        $presensis = $presensiQuery
            ->when(in_array($sort['key'], ['kegiatan', 'tanggal_kegiatan'], true), fn ($query) => $query->orderBy(
                Kegiatan::select($sort['key'] === 'kegiatan' ? 'nama_kegiatan' : 'tanggal_waktu')->whereColumn('kegiatan.id', 'presensi.kegiatan_id'), $sort['direction']
            ))
            ->when(! in_array($sort['key'], ['kegiatan', 'tanggal_kegiatan'], true), fn ($query) => $query->orderBy([
                'status' => 'status_kehadiran', 'waktu_hadir' => 'waktu_hadir', 'created' => 'presensi.created_at',
            ][$sort['key']], $sort['direction']))
            ->orderByDesc('presensi.id')->paginate(6)->withQueryString();

        $sertifikats = Sertifikat::where('anggota_id', $anggota->id)
            ->get()
            ->keyBy('kegiatan_id');

        $stats = [
            'hadir' => (clone $presensiQuery)->where('status_kehadiran', 'hadir')->count(),
            'izin' => (clone $presensiQuery)->where('status_kehadiran', 'izin')->count(),
            'alfa' => (clone $presensiQuery)->where('status_kehadiran', 'alfa')->count(),
        ];
        $eligibleKegiatanIds = app(VerifiedAttendance::class)->eligibleKegiatanIds($anggota);
        $jumlahKegiatanHadir = $eligibleKegiatanIds->count();
        $canClaimSertifikat = $jumlahKegiatanHadir > 0;
        $minimumKegiatanHadir = 1;

        return view('kader.riwayat.index', compact(
            'presensis',
            'sertifikats',
            'stats',
            'jumlahKegiatanHadir',
            'canClaimSertifikat',
            'eligibleKegiatanIds',
            'minimumKegiatanHadir',
            'options', 'sort',
        ));
    }
}
