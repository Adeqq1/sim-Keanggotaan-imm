<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $anggota->nama_lengkap }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Georgia', 'Times New Roman', Times, serif;
        }
        .bg-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: -100;
        }
        .bg-image {
            width: 100%;
            height: 100%;
        }
        .container {
            position: absolute;
            top: 35px;
            left: 60px;
            right: 60px;
            bottom: 35px;
            text-align: center;
        }
        .header-section {
            margin-top: 5px;
        }
        .logo {
            height: 70px;
            margin-bottom: 5px;
        }
        .instansi-title-sub {
            font-size: 12px;
            font-weight: bold;
            color: #555;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .instansi-title {
            font-size: 19px;
            font-weight: bold;
            color: #800000;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .divider {
            width: 45%;
            height: 2px;
            background-color: #800000;
            margin: 12px auto 15px auto;
        }
        .title-section {
            margin-top: 20px;
        }
        .doc-title {
            font-size: 24px;
            font-weight: bold;
            color: #800000;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.2;
        }
        .doc-number {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }
        .recipient-section {
            margin-top: 30px;
        }
        .given-to {
            font-size: 14px;
            font-style: italic;
            color: #666;
            margin-bottom: 8px;
        }
        .recipient-name {
            font-size: 26px;
            font-weight: bold;
            color: #111;
            border-bottom: 2px solid #800000;
            display: inline-block;
            padding-bottom: 3px;
            min-width: 320px;
        }
        .content-section {
            margin-top: 25px;
            padding: 0 50px;
            font-size: 14px;
            line-height: 1.7;
            color: #333;
        }
        .content-section strong {
            color: #800000;
        }
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 50px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-id {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <!-- Background Wrapper to avoid GD imagecreatefromjpeg dependency crash in DomPDF -->
    @if($useBackground)
    <div class="bg-container">
        <img class="bg-image" src="{{ public_path('images/sertificate-asset/bg-sertificate.jpg') }}" alt="Background" />
    </div>
    @endif

    <div class="container">
        <!-- Heading / Kop Surat -->
        <div class="header-section">
            <img class="logo" src="{{ public_path('images/sertificate-asset/logo.png') }}" alt="Logo IMM" />
            <div class="instansi-title-sub">Ikatan Mahasiswa Muhammadiyah</div>
            <div class="instansi-title">{{ config('app.org_name', 'IMM Kabupaten Bungo') }}</div>
        </div>

        <div class="divider"></div>

        <!-- Judul Dokumen & Nomor -->
        <div class="title-section">
            <div class="doc-title">Sertifikat {{ $kegiatan->nama_kegiatan }}</div>
            <div class="doc-number">No: {{ $nomorSertifikat }}</div>
        </div>

        <!-- Penerima -->
        <div class="recipient-section">
            <div class="given-to">Diberikan kepada:</div>
            <div class="recipient-name">{{ $anggota->nama_lengkap }}</div>
        </div>

        <!-- Deskripsi Partisipasi -->
        <div class="content-section">
            Atas partisipasi sebagai <strong>{{ $role }}</strong> dalam <strong>{{ $kegiatan->nama_kegiatan }}</strong>,<br>
            yang diselenggarakan pada tanggal <strong>{{ $kegiatan->tanggal_waktu->translatedFormat('d F Y') }}</strong> bertempat di <strong>{{ $kegiatan->lokasi }}</strong>.
        </div>

        <!-- Tanda Tangan / Pengesahan -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td style="width: 65%;"></td>
                    <td style="width: 35%; text-align: center; font-size: 13px;">
                        <div>Bungo, {{ now()->translatedFormat('d F Y') }}</div>
                        <div class="signature-title">Instruktur,</div>
                        <div class="signature-name">{{ $instruktur }}</div>
                        <div class="signature-id">NBM. ----------------------</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
