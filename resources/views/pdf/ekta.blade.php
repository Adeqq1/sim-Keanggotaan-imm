<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-KTA {{ $anggota->nama_lengkap ?: 'Anggota' }}</title>
    <style>
        @page {
            margin: 0;
            size: 240pt 152.25pt;
        }

        html,
        body {
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        .ekta-pdf-page {
            height: 145pt;
            margin: 0;
            padding: 0;
            page-break-inside: avoid;
            position: relative;
            width: 240pt;
        }

        .ekta-pdf-page--first { page-break-after: always; }
        .ekta-pdf-page .ekta-card-back--pdf { aspect-ratio: auto !important; border-radius: 5pt !important; height: 145pt !important; left: 0; position: absolute; top: 0; width: 240pt !important; }
        .ekta-card-back--pdf .ekta-card-back__title { font-size: 6.5pt; }
        .ekta-card-back--pdf .ekta-card-back__subtitle { font-size: 4pt; }
        .ekta-card-back--pdf .ekta-card-back__rules { font-size: 4pt; }
        .ekta-card-back--pdf .ekta-card-back__motto { font-size: 4.5pt; }
        .ekta-card-back--pdf .ekta-card-back__competence { font-size: 3.5pt; }
        .ekta-card-back--pdf .ekta-card-back__qr img { height: 28pt; width: 28pt; }
        .ekta-card-back--pdf .ekta-card-back__footer { bottom: 8pt; height: 42pt; left: 10pt; right: 10pt; }
        .ekta-card-back--pdf .ekta-card-back__qr { bottom: 0; left: 0; position: absolute; width: 25%; }
        .ekta-card-back--pdf .ekta-card-back__signature { bottom: 0; font-size: 4pt; position: absolute; right: 0; text-align: right; width: 72%; }

        .ekta-pdf-page .ekta-card--pdf {
            aspect-ratio: auto !important;
            border-radius: 5pt !important;
            height: 145pt !important;
            left: 0;
            page-break-inside: avoid;
            position: absolute;
            top: 0;
            width: 240pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__pattern {
            background-image: radial-gradient(circle, rgba(128, 0, 0, 0.06) 0.4pt, transparent 0.6pt) !important;
            background-size: 6.8pt 6.8pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__swoop-accent,
        .ekta-pdf-page .ekta-card--pdf .ekta-card__swoop {
            border-bottom-right-radius: 90pt !important;
            left: -21.6pt !important;
            top: -18pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__swoop-accent {
            height: 109.5pt !important;
            width: 148.8pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__swoop {
            height: 102pt !important;
            left: -26.4pt !important;
            width: 139.2pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__brand {
            width: 47% !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo-cell {
            padding-right: 3.4pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo-badge {
            border-width: 1pt !important;
            box-shadow: 0 0 0 0.75pt #600000 !important;
            height: 20pt !important;
            width: 20pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo {
            height: 16.5pt !important;
            width: 14.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__logo-fallback {
            font-size: 3pt !important;
            height: 15.5pt !important;
            width: 12pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__title {
            font-size: 7pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__organization {
            font-size: 3pt !important;
            margin-top: 1.9pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-note-label,
        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-note-value {
            font-size: 3.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__top-note-rule {
            height: 1.5pt !important;
            margin: 2pt 0 1.5pt auto !important;
            width: 22pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__photo-frame {
            border-width: 1pt !important;
            box-shadow: 0 0 0 1.3pt #ffffff, 0 0 0 2.4pt #a00000, 1.8pt 2.2pt 0 rgba(242, 206, 69, 0.92) !important;
            padding: 2pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__photo-fallback {
            font-size: 8.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__eyebrow {
            font-size: 3.5pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__name {
            font-size: 9.5pt !important;
            margin-top: 2pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__meta {
            margin-top: 4.8pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__meta td {
            font-size: 4.5pt !important;
            padding: 2pt 0 1.2pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__footer {
            font-size: 2pt !important;
        }

        .ekta-pdf-page .ekta-card--pdf .ekta-card__bottom-line,
        .ekta-pdf-page .ekta-card--pdf .ekta-card__bottom-line::after {
            height: 2pt !important;
        }
    </style>
</head>
@php($qrCodeSrc = $qrCodeSrc ?? null)
<body>
    <div class="ekta-pdf-page ekta-pdf-page--first">
        <x-ekta-card
            class="ekta-card--pdf"
            :anggota="$anggota"
            :role-label="$roleLabel"
            :photo-src="$photoSrc"
            :logo-src="$logoSrc"
        />
    </div>
    <div class="ekta-pdf-page">
        <x-ekta-card-back class="ekta-card-back--pdf" :anggota="$anggota" :role-label="$roleLabel" :qr-code-src="$qrCodeSrc" :logo-src="$logoSrc" />
    </div>
</body>
</html>
