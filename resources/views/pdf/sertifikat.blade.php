<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $anggota->nama_lengkap }}</title>
    <style>
        @font-face {
            font-family: 'Alex Brush';
            src: url('{{ public_path("fonts/AlexBrush-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('{{ public_path("fonts/Montserrat-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('{{ public_path("fonts/Montserrat-Medium.ttf") }}') format('truetype');
            font-weight: 500;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('{{ public_path("fonts/Montserrat-Bold.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @page {
            size: a4 landscape;
            margin: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #800000; /* Maroon outer border */
            font-family: 'Georgia', 'Times New Roman', Times, serif;
            box-sizing: border-box;
            overflow: hidden;
        }

        .page-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .inner-card {
            position: absolute;
            top: 25px;
            bottom: 25px;
            left: 25px;
            right: 25px;
            background-color: #faf6f0; /* Cream background like reference.jpg */
            overflow: hidden;
            box-sizing: border-box;
            z-index: 1;
        }

        @if($useBackground && $bgExists)
        .inner-card {
            background-color: rgba(255, 255, 255, 0.75) !important;
            border: none !important;
        }
        @endif
        
        /* ==========================================
           DEKORASI SUDUT & SISI (Pure CSS & DIV)
           ========================================== */
        
        /* Top-Right Gold Block */
        .decor-top-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 140px;
            height: 35px;
            background-color: #fcd34d; /* Gold */
        }
        .decor-top-right-line {
            position: absolute;
            top: 35px;
            right: 35px;
            width: 50px;
            height: 25px;
            border-left: 2px solid #800000;
            border-bottom: 2px solid #800000;
        }

        /* Middle-Left Gold Bar & Circles */
        .decor-left-bar {
            position: absolute;
            left: 0;
            top: 160px;
            width: 20px;
            height: 180px;
            background-color: #fcd34d;
        }
        .decor-left-circle-outer {
            position: absolute;
            left: -10px;
            top: 220px;
            width: 60px;
            height: 60px;
            border: 3px solid #800000;
            border-radius: 50%;
        }
        .decor-left-circle-inner {
            position: absolute;
            left: -4px;
            top: 226px;
            width: 48px;
            height: 48px;
            border: 2px solid #800000;
            border-radius: 50%;
        }

        /* Bottom-Right Gold Triangle */
        .decor-bottom-right-triangle {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0;
            height: 0;
            border-bottom: 80px solid #fcd34d;
            border-left: 80px solid transparent;
        }

        /* ==========================================
           KONTEN UTAMA
           ========================================== */
        .container {
            position: absolute;
            top: 40px;
            left: 80px;
            right: 80px;
            bottom: 40px;
            text-align: center;
            box-sizing: border-box;
        }
        .logo {
            height: 60px;
            margin-bottom: 8px;
        }
        .title {
            font-family: 'Georgia', 'Times New Roman', Times, serif;
            font-size: 28pt;
            font-weight: bold;
            color: #b45309; /* Orange-brown like reference.jpg */
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 5px 0 0 0;
        }
        .doc-number {
            font-family: 'Montserrat', 'Helvetica', sans-serif;
            font-size: 9pt;
            color: #555;
            letter-spacing: 1px;
            margin-top: 4px;
            margin-bottom: 20px;
        }
        .given-to {
            font-family: 'Montserrat', 'Helvetica', sans-serif;
            font-size: 11pt;
            font-weight: 500;
            color: #111;
            margin-bottom: 5px;
        }
        .recipient-name {
            font-family: 'Alex Brush', 'Georgia', cursive, serif;
            font-size: 42pt;
            font-style: italic; /* Menjaga keanggunan jika font Google gagal di-load */
            color: #b45309;
            margin: 5px 0;
            font-weight: normal;
        }
        .reason {
            font-family: 'Montserrat', 'Helvetica', sans-serif;
            font-size: 9.5pt;
            line-height: 1.8;
            color: #333;
            margin: 15px 40px 0 40px;
        }
        .reason strong {
            color: #800000;
        }

        /* ==========================================
           TANDA TANGAN (Normal flow inside container)
           ========================================== */
        .sig-table {
            width: 100%;
            margin-top: 60px; /* Memberi jarak aman dari teks deskripsi */
            border-collapse: collapse;
            border: none;
        }
        .sig-cell {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .sig-line {
            width: 180px;
            height: 1.5px;
            background-color: #333;
            margin: 0 auto 8px auto;
        }
        .sig-name {
            font-family: 'Montserrat', 'Helvetica', sans-serif;
            font-size: 10pt;
            font-weight: bold;
            color: #b45309;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sig-title {
            font-family: 'Montserrat', 'Helvetica', sans-serif;
            font-size: 9pt;
            color: #111;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    @if($useBackground && $bgExists)
        <img class="page-background" src="{{ public_path($bgPath) }}" alt="Certificate Background" />
    @endif

    <div class="inner-card">
        @if(! $useBackground || ! $bgExists)
            <!-- Ornamen Kiri (Pure CSS) -->
            <div class="decor-left-bar"></div>
            <div class="decor-left-circle-outer"></div>
            <div class="decor-left-circle-inner"></div>

            <!-- Ornamen Kanan Atas (Pure CSS) -->
            <div class="decor-top-right"></div>
            <div class="decor-top-right-line"></div>

            <!-- Ornamen Kanan Bawah (Pure CSS) -->
            <div class="decor-bottom-right-triangle"></div>
        @endif

        <!-- Konten Surat -->
        <div class="container">
            <!-- Kop Surat / Logo -->
            <div>
                <img class="logo" src="{{ public_path('images/sertificate-asset/logo.png') }}" alt="Logo IMM" />
            </div>

            <!-- Title & Nomor -->
            <h1 class="title">SERTIFIKAT</h1>
            <div class="doc-number">Nomor: {{ $nomorSertifikat }}</div>

            <!-- Penerima -->
            <div class="given-to">Dengan ini diberikan kepada:</div>
            <div class="recipient-name">{{ $anggota->nama_lengkap }}</div>

            <!-- Deskripsi -->
            <div class="reason">
                Sebagai penghargaan atas partisipasinya sebagai <strong>{{ $role }}</strong> dalam kegiatan<br>
                <strong>"{{ $kegiatan->nama_kegiatan }}"</strong> yang diselenggarakan pada tanggal<br>
                {{ $kegiatan->tanggal_waktu->translatedFormat('d F Y') }} bertempat di {{ $kegiatan->lokasi }}.
            </div>

            <!-- Bagian Tanda Tangan (Normal Flow di dalam container) -->
            <table class="sig-table">
                <tr>
                    <td class="sig-cell">
                        <div style="height: 12px; margin-bottom: 45px;"></div>
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ config('app.ketua_umum', 'Ade Rifqy Aulian, M. Kom') }}</div>
                        <div class="sig-title">Pimpinan Komisariat</div>
                    </td>
                    <td class="sig-cell">
                        <div style="font-size: 8.5pt; color: #555; margin-bottom: 45px;">Bungo, {{ now()->translatedFormat('d F Y') }}</div>
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $instruktur }}</div>
                        <div class="sig-title">Instruktur Pendamping</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
