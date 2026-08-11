<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-KTA {{ $anggota->nama_lengkap ?: 'Anggota' }}</title>
    <style>
        @page {
            margin: 0;
        }

        html,
        body {
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        .ekta-pdf-page {
            width: 320px;
            height: 203px;
        }

        .ekta-pdf-page .ekta-card {
            height: 203px;
        }
    </style>
</head>
<body>
    <div class="ekta-pdf-page">
        <x-ekta-card
            :anggota="$anggota"
            :role-label="$roleLabel"
            :photo-src="$photoSrc"
        />
    </div>
</body>
</html>
