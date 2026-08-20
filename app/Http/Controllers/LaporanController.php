<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Http\Requests\LaporanRequest;
use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\SortParams;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        return view('admin.laporan.index');
    }

    public function exportPdf(LaporanRequest $request)
    {
        $validated = $request->validated();
        $jenis = $validated['jenis_laporan'];
        $mulai = $validated['tanggal_mulai'];
        $selesai = $validated['tanggal_selesai'];

        $sort = $this->resolveSort($request, $jenis);
        $data = $this->getData($jenis, $mulai, $selesai, $sort);

        $pdf = Pdf::loadView('pdf.laporan', [
            'data' => $data,
            'jenis' => $jenis,
            'mulai' => $mulai,
            'selesai' => $selesai,
        ]);

        return $pdf->download('Laporan_'.$jenis.'_'.now()->format('Ymd').'.pdf');
    }

    public function exportExcel(LaporanRequest $request)
    {
        $validated = $request->validated();
        $jenis = $validated['jenis_laporan'];
        $mulai = $validated['tanggal_mulai'];
        $selesai = $validated['tanggal_selesai'];

        $sort = $this->resolveSort($request, $jenis);
        $data = $this->getData($jenis, $mulai, $selesai, $sort);

        return Excel::download(
            new LaporanExport($data, $jenis),
            'Laporan_'.$jenis.'_'.now()->format('Ymd').'.xlsx'
        );
    }

    private function getData($jenis, $mulai, $selesai, array $sort)
    {
        $columns = [
            'kegiatan' => ['nama' => 'nama_kegiatan', 'tanggal' => 'tanggal_waktu', 'lokasi' => 'lokasi', 'created' => 'created_at'],
            'anggota' => ['nama' => 'nama_lengkap', 'nia' => 'nia', 'status' => 'status_aktif', 'created' => 'created_at'],
            'pendaftaran' => ['nama' => 'nama_lengkap', 'email' => 'email', 'tanggal' => 'tanggal_daftar', 'created' => 'created_at'],
            'arsip' => ['judul' => 'judul_dokumen', 'nomor' => 'nomor_dokumen', 'kategori' => 'kategori_arsip', 'tanggal' => 'tanggal_unggah', 'created' => 'created_at'],
        ];
        $order = $columns[$jenis][$sort['key']];

        switch ($jenis) {
            case 'kegiatan':
                return Kegiatan::whereBetween('tanggal_waktu', [$mulai.' 00:00:00', $selesai.' 23:59:59'])->orderBy($order, $sort['direction'])->orderByDesc('id')->get();
            case 'anggota':
                return Anggota::whereBetween('created_at', [$mulai.' 00:00:00', $selesai.' 23:59:59'])->orderBy($order, $sort['direction'])->orderByDesc('id')->get();
            case 'pendaftaran':
                return Pendaftaran::whereBetween('tanggal_daftar', [$mulai, $selesai])->orderBy($order, $sort['direction'])->orderByDesc('id')->get();
            case 'arsip':
                return Arsip::whereBetween('tanggal_unggah', [$mulai, $selesai])->orderBy($order, $sort['direction'])->orderByDesc('id')->get();
            default:
                return collect();
        }
    }

    private function resolveSort(Request $request, string $jenis): array
    {
        $options = [
            'kegiatan' => ['nama', 'tanggal', 'lokasi', 'created'],
            'anggota' => ['nama', 'nia', 'status', 'created'],
            'pendaftaran' => ['nama', 'email', 'tanggal', 'created'],
            'arsip' => ['judul', 'nomor', 'kategori', 'tanggal', 'created'],
        ];

        return SortParams::resolve($request, $options[$jenis], 'created');
    }
}
