<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $anggota->nama_lengkap }}</title>
    <style>
        body {
            font-family: 'Times-Roman', serif;
            margin: 0;
            padding: 50px;
            border: 20px solid #1e3a8a;
            height: 800px;
        }
        .container {
            text-align: center;
            border: 2px solid #1e3a8a;
            padding: 50px;
            height: 100%;
        }
        .header {
            font-size: 40px;
            color: #1e3a8a;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .sub-header {
            font-size: 20px;
            margin-bottom: 40px;
        }
        .given-to {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .name {
            font-size: 30px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
        }
        .reason {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 50px;
        }
        .kegiatan {
            font-weight: bold;
            color: #1e3a8a;
        }
        .footer {
            margin-top: 100px;
            text-align: right;
        }
        .nomor {
            font-size: 12px;
            color: #777;
            text-align: left;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">SERTIFIKAT</div>
        <div class="sub-header">PENGHARGAAN / PARTISIPASI</div>
        
        <div class="given-to">Diberikan kepada:</div>
        <div class="name">{{ $anggota->nama_lengkap }}</div>
        
        <div class="reason">
            Atas partisipasinya sebagai Peserta dalam kegiatan:<br>
            <span class="kegiatan">"{{ $kegiatan->nama_kegiatan }}"</span><br>
            Yang dilaksanakan pada tanggal {{ $kegiatan->tanggal_waktu->format('d F Y') }}<br>
            bertempat di {{ $kegiatan->lokasi }}.
        </div>
        
        <div class="footer">
            Jakarta, {{ now()->format('d F Y') }}<br><br><br><br>
            <strong>Panitia Pelaksana</strong>
        </div>
        
        <div class="nomor">No: {{ $nomorSertifikat }}</div>
    </div>
</body>
</html>
