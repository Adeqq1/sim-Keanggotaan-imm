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
            width: 240pt;
            height: 152.25pt;
            page-break-inside: avoid;
        }

        .ekta-pdf-page .ekta-card--pdf {
            height: 150pt !important;
            page-break-inside: avoid;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__content {
            height: auto !important;
            padding: 9pt 12pt 22.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__body {
            margin-top: 7.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__photo-column {
            width: 57pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__photo,
        .ekta-pdf-page .ekta-card--pdf .ekta-card__photo-fallback {
            width: 43.5pt !important;
            height: 54pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__photo-fallback {
            font-size: 16.5pt !important;
            line-height: 51pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__name {
            font-size: 8.25pt !important;
            line-height: 1.1 !important;
            margin-top: 1.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__meta {
            margin-top: 3.75pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__meta td {
            font-size: 5.25pt !important;
            line-height: 1.2 !important;
            padding: 0 !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__footer {
            height: 19.5pt !important;
            padding: 6pt 12pt 0 !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__footer-text {
            font-size: 4.5pt !important;
        }
    </style>
</head>
<body>
    <div class="ekta-pdf-page">
        <x-ekta-card
            class="ekta-card--pdf"
            :anggota="$anggota"
            :role-label="$roleLabel"
            :photo-src="$photoSrc"
        />
    </div>
</body>
</html>
