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

        $data = $this->getData($jenis, $mulai, $selesai);

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

        $data = $this->getData($jenis, $mulai, $selesai);

        return Excel::download(
            new LaporanExport($data, $jenis),
            'Laporan_'.$jenis.'_'.now()->format('Ymd').'.xlsx'
        );
    }

    private function getData($jenis, $mulai, $selesai)
    {
        switch ($jenis) {
            case 'kegiatan':
                return Kegiatan::whereBetween('tanggal_waktu', [$mulai.' 00:00:00', $selesai.' 23:59:59'])->get();
            case 'anggota':
                return Anggota::whereBetween('created_at', [$mulai.' 00:00:00', $selesai.' 23:59:59'])->get();
            case 'pendaftaran':
                return Pendaftaran::whereBetween('tanggal_daftar', [$mulai, $selesai])->get();
            case 'arsip':
                return Arsip::whereBetween('tanggal_unggah', [$mulai, $selesai])->get();
            default:
                return collect();
        }
    }
}
