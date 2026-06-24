<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Sertifikat;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $stats = [
            'total_anggota' => Anggota::where('status_aktif', true)->count(),
            'total_kegiatan' => Kegiatan::count(),
            'pendaftar_pending' => Pendaftaran::where('status_validasi', 'pending')->count(),
            'total_arsip' => Arsip::count(),
            'sertifikat_pending' => Presensi::where('status_klaim', 'pending')->count(),
        ];

        $recent_kegiatans = Kegiatan::where('tanggal_waktu', '>=', now())
            ->orderBy('tanggal_waktu', 'asc')
            ->take(5)
            ->get();

        $chartData = $this->getChartData();

        return view('admin.dashboard', compact('stats', 'recent_kegiatans', 'chartData'));
    }

    private function getChartData(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $months[$key] = [
                'label' => $date->format('M Y'),
                'count' => 0,
            ];
        }

        $anggotaCounts = Anggota::where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->get(['created_at'])
            ->groupBy(fn ($item) => $item->created_at->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $kegiatanCounts = Kegiatan::where('tanggal_waktu', '>=', now()->subMonths(11)->startOfMonth())
            ->get(['tanggal_waktu'])
            ->groupBy(fn ($item) => Carbon::parse($item->tanggal_waktu)->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $presensiCounts = Presensi::where('status_kehadiran', 'hadir')
            ->where('waktu_hadir', '>=', now()->subMonths(11)->startOfMonth())
            ->get(['waktu_hadir'])
            ->groupBy(fn ($item) => Carbon::parse($item->waktu_hadir)->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $anggotaLabels = [];
        $anggotaData = [];
        $kegiatanLabels = [];
        $kegiatanData = [];
        $kehadiranLabels = [];
        $kehadiranData = [];

        foreach ($months as $key => $info) {
            $label = $info['label'];
            $anggotaLabels[] = $label;
            $anggotaData[] = $anggotaCounts->get($key, 0);

            $kegiatanLabels[] = $label;
            $kegiatanData[] = $kegiatanCounts->get($key, 0);

            $kehadiranLabels[] = $label;
            $kehadiranData[] = $presensiCounts->get($key, 0);
        }

        return [
            'anggota_per_bulan' => [
                'labels' => $anggotaLabels,
                'data' => $anggotaData,
            ],
            'kegiatan_per_bulan' => [
                'labels' => $kegiatanLabels,
                'data' => $kegiatanData,
            ],
            'kehadiran_per_bulan' => [
                'labels' => $kehadiranLabels,
                'data' => $kehadiranData,
            ],
        ];
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
