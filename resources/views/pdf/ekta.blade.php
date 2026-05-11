<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-KTA {{ $anggota->nama_lengkap }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
        }
        .card {
            width: 320px;
            height: 200px;
            background: #1e3a8a;
            color: white;
            padding: 15px;
            border-radius: 10px;
            position: relative;
        }
        .header {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .sub-header {
            font-size: 8px;
            opacity: 0.7;
            margin-bottom: 15px;
        }
        .photo {
            width: 60px;
            height: 75px;
            background: #eee;
            float: left;
            margin-right: 15px;
            border: 1px solid white;
        }
        .info {
            float: left;
        }
        .name {
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
        }
        .nia {
            font-size: 10px;
            color: #fbbf24;
            font-weight: bold;
            margin-top: 2px;
        }
        .footer {
            position: absolute;
            bottom: 10px;
            right: 15px;
            font-size: 6px;
            opacity: 0.5;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">KADER IMM</div>
        <div class="sub-header">IKATAN MAHASISWA MUHAMMADIYAH</div>
        
        <div class="photo">
            @if($anggota->foto_profil)
                <img src="{{ public_path('storage/' . $anggota->foto_profil) }}" width="60" height="75">
            @endif
        </div>
        
        <div class="info">
            <div class="name">{{ strtoupper($anggota->nama_lengkap) }}</div>
            <div class="nia">NIA: {{ $anggota->nia }}</div>
            <div style="font-size: 8px; margin-top: 10px;">
                AKTIF SEJAK: {{ $anggota->created_at->format('Y') }}
            </div>
        </div>
        
        <div class="clear"></div>
        <div class="footer">OFFICIAL MEMBERSHIP CARD</div>
    </div>
</body>
</html>
