<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kegiatan</title>
    <style>
        @page { margin: 28px; }
        body { color: #222; font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.5; }
        h1 { font-size: 20px; margin: 0 0 18px; text-align: center; }
        h2 { border-bottom: 1px solid #333; font-size: 14px; margin: 20px 0 8px; padding-bottom: 4px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #bbb; padding: 7px; text-align: left; vertical-align: top; }
        th { background: #f1f1f1; width: 25%; }
        .summary th { width: auto; text-align: center; }
        .summary td { text-align: center; font-size: 16px; }
        .section { page-break-inside: avoid; }
        .muted { color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <h1>LAPORAN KEGIATAN</h1>

    <div class="section">
        <h2>Identitas Kegiatan</h2>
        <table>
            <tr><th>Nama Kegiatan</th><td>{{ $kegiatan->nama_kegiatan }}</td></tr>
            <tr><th>Tanggal dan Waktu</th><td>{{ $kegiatan->tanggal_waktu->translatedFormat('d F Y, H:i') }}</td></tr>
            <tr><th>Lokasi</th><td>{{ $kegiatan->lokasi ?: '-' }}</td></tr>
            <tr><th>Deskripsi</th><td>{!! nl2br(e($kegiatan->deskripsi ?: '-')) !!}</td></tr>
        </table>
    </div>

    <div class="section">
        <h2>Isi Laporan</h2>
        <table>
            @foreach([
                'Tujuan' => $laporanKegiatan->tujuan,
                'Ringkasan Pelaksanaan' => $laporanKegiatan->ringkasan,
                'Agenda' => $laporanKegiatan->agenda,
                'Narasumber/Instruktur' => $laporanKegiatan->narasumber,
                'Hasil' => $laporanKegiatan->hasil,
                'Kendala' => $laporanKegiatan->kendala,
                'Tindak Lanjut' => $laporanKegiatan->tindak_lanjut,
            ] as $label => $value)
                <tr><th>{{ $label }}</th><td>{!! nl2br(e($value ?: '-')) !!}</td></tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Ringkasan Presensi</h2>
        <table class="summary">
            <tr><th>Jumlah Peserta</th><th>Hadir</th><th>Izin</th><th>Alfa</th></tr>
            <tr><td>{{ $kegiatan->presensi_count }}</td><td>{{ $kegiatan->hadir_count }}</td><td>{{ $kegiatan->izin_count }}</td><td>{{ $kegiatan->alfa_count }}</td></tr>
        </table>
    </div>

    <p class="muted">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}</p>
</body>
</html>
