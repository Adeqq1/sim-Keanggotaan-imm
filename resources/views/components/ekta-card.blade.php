@props([
    'anggota',
    'roleLabel',
    'photoSrc' => null,
])

@php
    $name = trim((string) $anggota->nama_lengkap);
    $displayName = $name !== '' ? \Illuminate\Support\Str::upper($name) : 'NAMA BELUM TERSEDIA';
    $initial = $name !== '' ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($name, 0, 1)) : '?';
    $displayNia = filled($anggota->nia) ? (string) $anggota->nia : 'BELUM TERSEDIA';
    $activeYear = $anggota->created_at?->format('Y') ?? 'BELUM TERSEDIA';
@endphp

<style>
    .ekta-card {
        width: 100%;
        max-width: 100%;
        aspect-ratio: 1.58 / 1;
        box-sizing: border-box;
        overflow: hidden;
        position: relative;
        background: #ffffff;
        border: 1px solid rgba(128, 0, 0, 0.16);
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(46, 24, 24, 0.12);
        color: #252525;
        font-family: Helvetica, Arial, sans-serif;
    }

    .ekta-card__content {
        height: 100%;
        box-sizing: border-box;
        padding: 20px 22px 42px;
        position: relative;
        z-index: 2;
    }

    .ekta-card__header,
    .ekta-card__body {
        display: table;
        table-layout: fixed;
        width: 100%;
    }

    .ekta-card__header-cell,
    .ekta-card__brand,
    .ekta-card__photo-column,
    .ekta-card__details {
        display: table-cell;
        vertical-align: top;
    }

    .ekta-card__brand {
        width: 48px;
        text-align: right;
    }

    .ekta-card__title {
        color: #800000;
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0.06em;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .ekta-card__organization {
        color: #6b4a4a;
        font-size: 8px;
        letter-spacing: 0.08em;
        line-height: 1.4;
        margin-top: 3px;
    }

    .ekta-card__brand-mark {
        background: #800000;
        border-radius: 50%;
        color: #ffffff;
        display: inline-block;
        font-size: 10px;
        font-weight: 800;
        height: 34px;
        letter-spacing: 0.04em;
        line-height: 34px;
        text-align: center;
        width: 34px;
    }

    .ekta-card__body {
        margin-top: 24px;
    }

    .ekta-card__photo-column {
        vertical-align: middle;
        width: 84px;
    }

    .ekta-card__photo,
    .ekta-card__photo-fallback {
        border: 2px solid #800000;
        border-radius: 10px;
        box-sizing: border-box;
        height: 86px;
        width: 70px;
    }

    .ekta-card__photo {
        display: block;
        object-fit: cover;
    }

    .ekta-card__photo-fallback {
        background: #800000;
        color: #ffffff;
        display: block;
        font-size: 28px;
        font-weight: 800;
        line-height: 82px;
        text-align: center;
    }

    .ekta-card__details {
        min-width: 0;
        vertical-align: middle;
    }

    .ekta-card__label {
        color: #806c6c;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 0.12em;
        line-height: 1.2;
        text-transform: uppercase;
    }

    .ekta-card__name {
        color: #252525;
        font-size: 15px;
        font-weight: 800;
        line-height: 1.2;
        margin-top: 4px;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .ekta-card__meta {
        border-collapse: collapse;
        margin-top: 12px;
        width: 100%;
    }

    .ekta-card__meta td {
        font-size: 9px;
        line-height: 1.4;
        padding: 1px 0;
        vertical-align: top;
    }

    .ekta-card__meta td:first-child {
        color: #806c6c;
        font-weight: 700;
        letter-spacing: 0.08em;
        width: 42%;
    }

    .ekta-card__meta td:last-child {
        color: #800000;
        font-weight: 800;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .ekta-card__footer {
        background: #800000;
        bottom: 0;
        box-sizing: border-box;
        color: #ffffff;
        height: 30px;
        left: 0;
        padding: 10px 22px 0;
        position: absolute;
        right: 0;
        z-index: 3;
    }

    .ekta-card__footer::after {
        background: #a00000;
        content: "";
        height: 7px;
        position: absolute;
        right: 0;
        top: -7px;
        width: 34%;
    }

    .ekta-card__footer-text {
        font-size: 7px;
        font-weight: 700;
        letter-spacing: 0.16em;
        opacity: 0.88;
    }

    @media (max-width: 360px) {
        .ekta-card__content {
            padding-left: 16px;
            padding-right: 16px;
        }

        .ekta-card__body {
            margin-top: 16px;
        }

        .ekta-card__name {
            font-size: 13px;
        }

        .ekta-card__photo-column {
            width: 78px;
        }
    }
</style>

<div {{ $attributes->merge(['class' => 'ekta-card']) }} data-testid="ekta-card">
    <div class="ekta-card__content">
        <div class="ekta-card__header">
            <div class="ekta-card__header-cell">
                <div class="ekta-card__title">KARTU TANDA {{ \Illuminate\Support\Str::upper($roleLabel) }}</div>
                <div class="ekta-card__organization">IKATAN MAHASISWA MUHAMMADIYAH</div>
            </div>
            <div class="ekta-card__brand" aria-hidden="true">
                <span class="ekta-card__brand-mark">IMM</span>
            </div>
        </div>

        <div class="ekta-card__body">
            <div class="ekta-card__photo-column">
                @if($photoSrc)
                    <img
                        src="{{ $photoSrc }}"
                        alt="Foto profil {{ $anggota->nama_lengkap ?: 'anggota' }}"
                        class="ekta-card__photo"
                    >
                @else
                    <div class="ekta-card__photo-fallback" data-testid="ekta-photo-fallback" aria-label="Inisial anggota">
                        {{ $initial }}
                    </div>
                @endif
            </div>
            <div class="ekta-card__details">
                <div class="ekta-card__label">Nama Lengkap</div>
                <div class="ekta-card__name">{{ $displayName }}</div>

                <table class="ekta-card__meta" aria-label="Biodata anggota">
                    <tr>
                        <td>NIA</td>
                        <td>{{ $displayNia }}</td>
                    </tr>
                    <tr>
                        <td>Aktif Sejak</td>
                        <td>{{ $activeYear }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="ekta-card__footer" aria-hidden="true">
        <span class="ekta-card__footer-text">KARTU ANGGOTA RESMI</span>
    </div>
</div>
