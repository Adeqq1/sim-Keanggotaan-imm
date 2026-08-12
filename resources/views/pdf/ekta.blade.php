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
            height: 152.25pt;
            page-break-inside: avoid;
            width: 240pt;
        }

        .ekta-pdf-page .ekta-card--pdf {
            aspect-ratio: auto !important;
            border-radius: 8pt !important;
            height: 146pt !important;
            page-break-inside: avoid;
            width: 240pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__pattern {
            background-image: radial-gradient(circle, rgba(128, 0, 0, 0.07) 0.7pt, transparent 1pt) !important;
            background-size: 13pt 13pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__swoop-accent,
        .ekta-pdf-page .ekta-card--pdf .ekta-card__swoop {
            border-bottom-right-radius: 100% 78% !important;
            left: -22pt !important;
            top: -18pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__swoop-accent {
            height: 106pt !important;
            width: 149pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__swoop {
            height: 99pt !important;
            left: -26pt !important;
            width: 139pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__brand {
            left: 12pt !important;
            top: 8pt !important;
            width: 112pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo-cell {
            padding-right: 7pt !important;
            width: 33pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo-badge {
            border-width: 2pt !important;
            box-shadow: 0 0 0 1.5pt #600000 !important;
            height: 42pt !important;
            width: 42pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo {
            height: 34pt !important;
            width: 30pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo-fallback {
            font-size: 6pt !important;
            height: 34pt !important;
            width: 26pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__title {
            font-size: 10pt !important;
            line-height: 1.1 !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__organization {
            font-size: 4.8pt !important;
            margin-top: 2pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-note {
            right: 12pt !important;
            top: 11pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-note-label,
        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-note-value {
            font-size: 5.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-note-rule {
            height: 2.5pt !important;
            margin: 3pt 0 2.5pt auto !important;
            width: 36pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__photo-frame {
            border-width: 2pt !important;
            bottom: 14pt !important;
            box-shadow: 0 0 0 2.5pt #ffffff, 0 0 0 4.5pt #a00000, 3pt 4pt 0 rgba(242, 206, 69, 0.92) !important;
            height: 57pt !important;
            left: 14pt !important;
            padding: 3pt !important;
            width: 57pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__details {
            left: 88pt !important;
            right: 12pt !important;
            top: 68pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__eyebrow {
            font-size: 5.8pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__name {
            font-size: 11pt !important;
            line-height: 1.08 !important;
            margin-top: 2pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__meta {
            margin-top: 5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__meta td {
            font-size: 6.8pt !important;
            line-height: 1.15 !important;
            padding: 2.5pt 0 1pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__footer {
            bottom: 7pt !important;
            font-size: 4pt !important;
            right: 12pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__bottom-line {
            height: 4pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__bottom-line::after {
            height: 4pt !important;
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
