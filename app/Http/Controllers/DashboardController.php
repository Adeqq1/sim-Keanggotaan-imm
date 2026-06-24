<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Sertifikat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        $now = now();

        $recent_kegiatans = Kegiatan::where('tanggal_waktu', '>=', $now)
            ->orderBy('tanggal_waktu', 'asc')
            ->take(5)
            ->get();

        $chartData = $this->getChartData($now);

        return view('admin.dashboard', compact('stats', 'recent_kegiatans', 'chartData'));
    }

    private function getChartData(Carbon $now): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $key = $date->format('Y-m');
            $months[$key] = [
                'label' => $date->format('M Y'),
                'count' => 0,
            ];
        }

        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            $dateSelect = "strftime('%Y-%m', created_at)";
            $kegiatanSelect = "strftime('%Y-%m', tanggal_waktu)";
            $presensiSelect = "strftime('%Y-%m', waktu_hadir)";
        } else {
            $dateSelect = "DATE_FORMAT(created_at, '%Y-%m')";
            $kegiatanSelect = "DATE_FORMAT(tanggal_waktu, '%Y-%m')";
            $presensiSelect = "DATE_FORMAT(waktu_hadir, '%Y-%m')";
        }

        $startLimit = $now->copy()->subMonths(11)->startOfMonth();

        $anggotaCounts = Anggota::selectRaw("$dateSelect as month, count(*) as total")
            ->where('created_at', '>=', $startLimit)
            ->groupBy('month')
            ->pluck('total', 'month');

        $kegiatanCounts = Kegiatan::selectRaw("$kegiatanSelect as month, count(*) as total")
            ->where('tanggal_waktu', '>=', $startLimit)
            ->groupBy('month')
            ->pluck('total', 'month');

        $presensiCounts = Presensi::selectRaw("$presensiSelect as month, count(*) as total")
            ->where('status_kehadiran', 'hadir')
            ->where('waktu_hadir', '>=', $startLimit)
            ->groupBy('month')
            ->pluck('total', 'month');

        $anggotaLabels = [];
        $anggotaData = [];
        $kegiatanLabels = [];
        $kegiatanData = [];
        $kehadiranLabels = [];
        $kehadiranData = [];

        foreach ($months as $key => $info) {
            $label = $info['label'];
            $anggotaLabels[] = $label;
            $anggotaData[] = (int) $anggotaCounts->get($key, 0);

            $kegiatanLabels[] = $label;
            $kegiatanData[] = (int) $kegiatanCounts->get($key, 0);

            $kehadiranLabels[] = $label;
            $kehadiranData[] = (int) $presensiCounts->get($key, 0);
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
