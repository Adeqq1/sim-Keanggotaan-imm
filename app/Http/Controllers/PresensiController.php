<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresensiRequest;
use App\Http\Requests\VerifikasiPresensiRequest;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\SortParams;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $options = ['nama' => 'Nama', 'tanggal' => 'Tanggal Kegiatan', 'lokasi' => 'Lokasi', 'peserta' => 'Peserta', 'hadir' => 'Hadir', 'izin' => 'Izin', 'alfa' => 'Alfa'];
        $sort = SortParams::resolve($request, array_keys($options), 'tanggal');
        $kegiatans = Kegiatan::query()
            ->withCount([
                'presensi as presensi_count' => fn ($query) => $query->select(new Expression('count(distinct anggota_id)')),
                'presensi as hadir_count' => fn ($query) => $query->where('status_kehadiran', 'hadir')->select(new Expression('count(distinct anggota_id)')),
                'presensi as izin_count' => fn ($query) => $query->where('status_kehadiran', 'izin')->whereNotExists(fn ($subquery) => $subquery->from('presensi as hadir')->whereColumn('hadir.kegiatan_id', 'presensi.kegiatan_id')->whereColumn('hadir.anggota_id', 'presensi.anggota_id')->where('hadir.status_kehadiran', 'hadir'))->select(new Expression('count(distinct anggota_id)')),
                'presensi as alfa_count' => fn ($query) => $query->where('status_kehadiran', 'alfa')->whereNotExists(fn ($subquery) => $subquery->from('presensi as prior')->whereColumn('prior.kegiatan_id', 'presensi.kegiatan_id')->whereColumn('prior.anggota_id', 'presensi.anggota_id')->whereIn('prior.status_kehadiran', ['hadir', 'izin']))->select(new Expression('count(distinct anggota_id)')),
            ])
            ->orderBy(['nama' => 'nama_kegiatan', 'tanggal' => 'tanggal_waktu', 'lokasi' => 'lokasi', 'peserta' => 'presensi_count', 'hadir' => 'hadir_count', 'izin' => 'izin_count', 'alfa' => 'alfa_count'][$sort['key']], $sort['direction'])
            ->orderByDesc('kegiatan.id')->paginate(12)->withQueryString();

        return view('admin.kegiatan.rekap-presensi', compact('kegiatans', 'options', 'sort'));
    }

    public function create(Request $request, Kegiatan $kegiatan)
    {
        if (! $kegiatan->sesiKegiatans()->exists()) {
            return redirect()->route('admin.kegiatan.sesi.index', $kegiatan)->withErrors(['sesi' => 'Tambahkan sesi kegiatan terlebih dahulu.']);
        }

        $sesiKegiatan = $kegiatan->sesiKegiatans()->first();

        return redirect()->route('admin.presensi.sesi.show', [$kegiatan, $sesiKegiatan]);
    }

    public function showSession(Request $request, Kegiatan $kegiatan, \App\Models\SesiKegiatan $sesiKegiatan)
    {
        $kegiatan->load('tahunAngkatans');
        $options = ['nama' => 'Nama', 'nia' => 'NIA'];
        $sort = SortParams::resolve($request, array_keys($options), 'nama', 'asc');
        $targetYears = $kegiatan->tahunAngkatans()->pluck('tahun_daftar');
        $anggotas = Anggota::where('status_aktif', true)
            ->whereIn('tahun_daftar', $targetYears)
            ->whereRelation('user', 'role', 'kader')
            ->orderBy(['nama' => 'nama_lengkap', 'nia' => 'nia'][$sort['key']], $sort['direction'])->orderByDesc('id')->get();
        $presensis = $sesiKegiatan->presensis()->with(['anggota', 'pemeriksa'])->get();
        $presensiByAnggota = $presensis->keyBy('anggota_id');
        $hadirPresensis = $presensis->where('status_kehadiran', 'hadir');
        $pendingPresensis = $hadirPresensis->where('status_verifikasi', 'pending')->sortBy(fn ($presensi) => $presensi->anggota->nama_lengkap);
        $processedPresensis = $hadirPresensis->whereIn('status_verifikasi', ['terverifikasi', 'legacy'])->sortBy(fn ($presensi) => $presensi->anggota->nama_lengkap);
        $rejectedPresensis = $hadirPresensis->where('status_verifikasi', 'ditolak')->sortBy(fn ($presensi) => $presensi->anggota->nama_lengkap);
        $presensiStats = [
            'total' => $anggotas->count(),
            'hadir' => $hadirPresensis->count(),
            'izin' => $presensis->where('status_kehadiran', 'izin')->count(),
            'alfa' => $presensis->where('status_kehadiran', 'alfa')->count(),
            'pending' => $pendingPresensis->count(),
            'terverifikasi' => $processedPresensis->count(),
            'ditolak' => $rejectedPresensis->count(),
        ];
        $canManagePresensi = auth()->user()->role === 'instruktur';

        return view('admin.kegiatan.presensi', compact('kegiatan', 'sesiKegiatan', 'anggotas', 'presensis', 'presensiByAnggota', 'pendingPresensis', 'processedPresensis', 'rejectedPresensis', 'presensiStats', 'canManagePresensi', 'options', 'sort'));
    }

    public function store(PresensiRequest $request, Kegiatan $kegiatan, ?\App\Models\SesiKegiatan $sesiKegiatan = null)
    {
        $legacyRoute = $sesiKegiatan === null;
        $sesiKegiatan ??= $kegiatan->sesiKegiatans()->firstOrCreate(
            ['urutan' => 1],
            ['nama_sesi' => 'Sesi 1', 'mulai_pada' => $kegiatan->tanggal_waktu],
        );

        DB::transaction(function () use ($request, $kegiatan, $sesiKegiatan): void {
            $sesiKegiatan = \App\Models\SesiKegiatan::query()->lockForUpdate()->findOrFail($sesiKegiatan->id);
            foreach ($request->validated('presensi') as $data) {
                $status = $data['status_kehadiran'];
                $presensi = Presensi::firstOrNew(['sesi_kegiatan_id' => $sesiKegiatan->id, 'anggota_id' => $data['anggota_id']]);
                $attributes = ['kegiatan_id' => $kegiatan->id, 'status_kehadiran' => $status];
                if ($status === 'hadir') {
                    $attributes['waktu_hadir'] = $presensi->exists && $presensi->status_kehadiran === 'hadir' ? $presensi->waktu_hadir : now();
                } else {
                    $attributes += ['waktu_hadir' => null, 'status_verifikasi' => 'pending', 'pemeriksa_id' => null, 'diperiksa_pada' => null];
                }
                $presensi->fill($attributes)->save();
            }
        });

        return redirect()->route($legacyRoute ? 'admin.kegiatan.index' : 'admin.presensi.sesi.show', $legacyRoute ? [] : [$kegiatan, $sesiKegiatan])->with('success', 'Presensi berhasil disimpan.');
    }

    public function updateVerification(VerifikasiPresensiRequest $request, Kegiatan $kegiatan, \App\Models\SesiKegiatan $sesiKegiatan, Presensi $presensi)
    {
        $status = $request->validated('status_verifikasi');
        if ($status !== 'pending' && $presensi->status_kehadiran !== 'hadir') {
            return back()->withErrors(['status_verifikasi' => 'Hanya presensi hadir yang dapat diverifikasi atau ditolak.']);
        }

        if ($status === $presensi->status_verifikasi && $status !== 'pending') {
            return back()->with('success', 'Keputusan verifikasi sudah tersimpan.');
        }

        $presensi->update([
            'status_verifikasi' => $status,
            'pemeriksa_id' => $status === 'pending' ? null : auth()->id(),
            'diperiksa_pada' => $status === 'pending' ? null : now(),
        ]);

        return back()->with('success', 'Keputusan verifikasi berhasil disimpan.');
    }
}
