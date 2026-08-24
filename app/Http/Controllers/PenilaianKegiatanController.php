<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenilaianKegiatanRequest;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\PenilaianKegiatan;
use App\Services\VerifiedAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenilaianKegiatanController extends Controller
{
    public function index(Kegiatan $kegiatan, VerifiedAttendance $attendance): View
    {
        abort_unless($kegiatan->jenis_pelaksanaan === Kegiatan::MULTI_SESI && $kegiatan->minimum_sesi_terverifikasi >= 3, 404);

        $eligibleIds = $attendance->eligibleAnggotaIdsFor($kegiatan);
        $anggotas = Anggota::query()
            ->whereIn('id', $eligibleIds)
            ->where('status_aktif', true)
            ->whereRelation('user', 'role', 'kader')
            ->with('user')
            ->with(['penilaianKegiatans' => fn ($query) => $query->where('kegiatan_id', $kegiatan->id)])
            ->orderBy('nama_lengkap')
            ->get();
        $kegiatan->load('sesiKegiatans');

        return view('admin.kegiatan.penilaian', [
            'kegiatan' => $kegiatan,
            'anggotas' => $anggotas,
            'canManagePenilaian' => auth()->user()->role === 'instruktur',
            'nilaiLabels' => PenilaianKegiatan::NILAI_LABELS,
        ]);
    }

    public function update(PenilaianKegiatanRequest $request, Kegiatan $kegiatan, Anggota $anggota): RedirectResponse
    {
        DB::transaction(function () use ($request, $kegiatan, $anggota): void {
            PenilaianKegiatan::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'anggota_id' => $anggota->id],
                ['nilai' => $request->validated('nilai')],
            );
        });

        return to_route('admin.kegiatan.penilaian.index', $kegiatan)->with('success', 'Penilaian berhasil disimpan.');
    }
}
