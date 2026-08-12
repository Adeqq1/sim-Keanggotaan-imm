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
            aspect-ratio: auto !important;
            height: 146pt !important;
            page-break-inside: avoid;
            width: 240pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__content {
            height: auto !important;
            padding: 0 !important;
            position: static !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-band {
            height: 54pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-band::after {
            bottom: -4pt !important;
            height: 8pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-accent {
            height: 3pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__header {
            left: 12pt;
            position: absolute;
            right: 12pt;
            top: 8pt;
            width: auto;
            z-index: 2;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__body {
            left: 12pt;
            position: absolute;
            right: 12pt;
            top: 48pt;
            width: auto;
            z-index: 2;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo-cell {
            padding-right: 7.5pt !important;
            width: 31.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo {
            height: 37.5pt !important;
            width: 24pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo-fallback {
            font-size: 6.75pt !important;
            height: 31.5pt !important;
            width: 24pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__title {
            font-size: 8.25pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__organization {
            font-size: 4.5pt !important;
            margin-top: 2.25pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__header-badge {
            width: 40.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__header-badge span {
            font-size: 4.5pt !important;
            padding: 3pt 4.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__body {
            margin-top: 0 !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__photo-column {
            width: 49pt !important;
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

        .ekta-pdf-page .ekta-card--pdf .ekta-card__status {
            font-size: 4.5pt !important;
            margin-top: 4.5pt !important;
            width: 45pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__photo-frame {
            border-width: 2.25pt !important;
            border-radius: 10.5pt !important;
            box-shadow: 3.75pt 3.75pt 0 #f2ce45 !important;
            height: 58pt !important;
            width: 45pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__eyebrow {
            font-size: 5.25pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__name {
            font-size: 7.5pt !important;
            line-height: 1.1 !important;
            margin-top: 1.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__meta {
            margin-top: 3.75pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__meta td {
            font-size: 5.25pt !important;
            line-height: 1.2 !important;
            padding: 3pt 0 1.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__footer {
            height: 17pt !important;
            padding: 4.5pt 12pt 0 !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__footer-text,
        .ekta-pdf-page .ekta-card--pdf .ekta-card__footer-role {
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
            :logo-src="$logoSrc"
        />
    </div>
</body>
</html>
