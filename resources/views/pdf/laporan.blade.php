<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ ucfirst($jenis) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN DATA {{ strtoupper($jenis) }}</div>
        <div>Periode: {{ $mulai }} s/d {{ $selesai }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                @if($jenis === 'kegiatan')
                    <th>Nama Kegiatan</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                @elseif($jenis === 'anggota')
                    <th>NIA</th>
                    <th>Nama Lengkap</th>
                    <th>Tanggal Terdaftar</th>
                @elseif($jenis === 'pendaftaran')
                    <th>Nama Pendaftar</th>
                    <th>Email</th>
                    <th>Tanggal Daftar</th>
                @elseif($jenis === 'arsip')
                    <th>Judul Dokumen</th>
                    <th>Kategori</th>
                    <th>Tanggal Unggah</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @if($jenis === 'kegiatan')
                        <td>{{ $item->nama_kegiatan }}</td>
                        <td>{{ $item->tanggal_waktu->format('d/m/Y') }}</td>
                        <td>{{ $item->lokasi }}</td>
                    @elseif($jenis === 'anggota')
                        <td>{{ $item->nia }}</td>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    @elseif($jenis === 'pendaftaran')
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->tanggal_daftar->format('d/m/Y') }}</td>
                    @elseif($jenis === 'arsip')
                        <td>{{ $item->judul_dokumen }}</td>
                        <td>{{ $item->kategori_arsip }}</td>
                        <td>{{ $item->tanggal_unggah->format('d/m/Y') }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right;">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
