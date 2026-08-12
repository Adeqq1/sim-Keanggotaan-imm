@props([
    'anggota',
    'roleLabel',
    'photoSrc' => null,
    'logoSrc' => null,
])

@php
    $name = \Illuminate\Support\Str::of((string) $anggota->nama_lengkap)->trim()->toString();
    $displayName = filled($name) ? \Illuminate\Support\Str::upper($name) : 'NAMA BELUM TERSEDIA';
    $initial = filled($name) ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($name, 0, 1)) : '?';
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
        background: #fffaf5;
        border: 1px solid #e8d9c9;
        border-radius: 22px;
        box-shadow: 0 14px 28px rgba(61, 28, 28, 0.16);
        color: #2d2323;
        font-family: Helvetica, Arial, sans-serif;
    }

    .ekta-card__top-band {
        background: #800000;
        height: 38%;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
    }

    .ekta-card__top-band::after {
        background: #a00000;
        bottom: -8px;
        content: "";
        height: 16px;
        left: -3%;
        position: absolute;
        transform: skewX(-22deg);
        width: 58%;
    }

    .ekta-card__top-accent {
        background: #f2ce45;
        height: 5px;
        position: absolute;
        right: 0;
        top: 0;
        width: 28%;
    }

    .ekta-card__content {
        height: 100%;
        box-sizing: border-box;
        padding: 16px 22px 34px;
        position: relative;
        z-index: 2;
    }

    .ekta-card__header,
    .ekta-card__body {
        display: table;
        table-layout: fixed;
        width: 100%;
    }

    .ekta-card__logo-cell,
    .ekta-card__header-info,
    .ekta-card__header-badge,
    .ekta-card__photo-column,
    .ekta-card__details {
        display: table-cell;
        vertical-align: top;
    }

    .ekta-card__logo-cell {
        padding-right: 10px;
        width: 42px;
    }

    .ekta-card__logo {
        display: block;
        height: 50px;
        object-fit: contain;
        width: 32px;
    }

    .ekta-card__logo-fallback {
        align-items: center;
        background: #f2ce45;
        border-radius: 8px;
        color: #800000;
        display: flex;
        font-size: 9px;
        font-weight: 800;
        height: 42px;
        justify-content: center;
        width: 32px;
    }

    .ekta-card__header-info {
        vertical-align: middle;
    }

    .ekta-card__title {
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 0.06em;
        line-height: 1.15;
        overflow-wrap: anywhere;
    }

    .ekta-card__organization {
        color: rgba(255, 255, 255, 0.76);
        font-size: 7px;
        letter-spacing: 0.08em;
        line-height: 1.4;
        margin-top: 4px;
    }

    .ekta-card__header-badge {
        text-align: right;
        vertical-align: middle;
        width: 54px;
    }

    .ekta-card__header-badge span {
        border: 1px solid rgba(255, 255, 255, 0.58);
        border-radius: 999px;
        color: #ffffff;
        display: inline-block;
        font-size: 7px;
        font-weight: 700;
        letter-spacing: 0.12em;
        padding: 5px 7px;
    }

    .ekta-card__body {
        margin-top: 20px;
    }

    .ekta-card__photo-column {
        vertical-align: middle;
        width: 96px;
    }

    .ekta-card__photo-frame {
        background: #f1e5d8;
        border: 3px solid #800000;
        border-radius: 14px;
        box-shadow: 5px 5px 0 #f2ce45;
        box-sizing: border-box;
        height: 96px;
        overflow: hidden;
        width: 76px;
    }

    .ekta-card__photo,
    .ekta-card__photo-fallback {
        box-sizing: border-box;
        height: 100%;
        width: 100%;
    }

    .ekta-card__photo {
        display: block;
        object-fit: cover;
    }

    .ekta-card__photo-fallback {
        align-items: center;
        background: #800000;
        color: #ffffff;
        display: flex;
        font-size: 30px;
        font-weight: 800;
        justify-content: center;
    }

    .ekta-card__status {
        color: #800000;
        font-size: 6px;
        font-weight: 800;
        letter-spacing: 0.12em;
        margin-top: 8px;
        text-align: center;
        width: 76px;
    }

    .ekta-card__details {
        min-width: 0;
        vertical-align: middle;
    }

    .ekta-card__eyebrow {
        color: #a00000;
        font-size: 7px;
        font-weight: 800;
        letter-spacing: 0.16em;
        line-height: 1.2;
    }

    .ekta-card__name {
        color: #2d2323;
        font-size: 17px;
        font-weight: 800;
        line-height: 1.16;
        margin-top: 4px;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .ekta-card__meta {
        border-collapse: collapse;
        margin-top: 13px;
        width: 100%;
    }

    .ekta-card__meta td {
        border-top: 1px solid #eaded2;
        font-size: 8px;
        line-height: 1.3;
        padding: 5px 0 3px;
        vertical-align: top;
    }

    .ekta-card__meta td:first-child {
        color: #806c6c;
        font-weight: 700;
        letter-spacing: 0.08em;
        width: 48%;
    }

    .ekta-card__meta td:last-child {
        color: #800000;
        font-weight: 800;
        overflow-wrap: anywhere;
        text-align: right;
        word-break: break-word;
    }

    .ekta-card__footer {
        background: #800000;
        border-top: 4px solid #f2ce45;
        bottom: 0;
        box-sizing: border-box;
        color: #ffffff;
        height: 25px;
        left: 0;
        padding: 6px 22px 0;
        position: absolute;
        right: 0;
        z-index: 3;
    }

    .ekta-card__footer-content {
        display: table;
        width: 100%;
    }

    .ekta-card__footer-text,
    .ekta-card__footer-role {
        display: table-cell;
        font-size: 6px;
        font-weight: 700;
        letter-spacing: 0.14em;
    }

    .ekta-card__footer-role {
        opacity: 0.78;
        text-align: right;
    }

    @media (max-width: 360px) {
        .ekta-card__content {
            padding-left: 16px;
            padding-right: 16px;
        }

        .ekta-card__body {
            margin-top: 16px;
        }

        .ekta-card__photo-column {
            width: 86px;
        }

        .ekta-card__photo-frame {
            height: 88px;
            width: 70px;
        }

        .ekta-card__status {
            width: 70px;
        }

        .ekta-card__name {
            font-size: 14px;
        }
    }
</style>

<div {{ $attributes->merge(['class' => 'ekta-card']) }} data-testid="ekta-card">
    <div class="ekta-card__top-band" aria-hidden="true">
        <div class="ekta-card__top-accent"></div>
    </div>

    <div class="ekta-card__content">
        <div class="ekta-card__header">
            <div class="ekta-card__logo-cell">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo IMM" class="ekta-card__logo">
                @else
                    <span class="ekta-card__logo-fallback" aria-label="Logo IMM">IMM</span>
                @endif
            </div>
            <div class="ekta-card__header-info">
                <div class="ekta-card__title">KARTU TANDA {{ \Illuminate\Support\Str::upper($roleLabel) }}</div>
                <div class="ekta-card__organization">IKATAN MAHASISWA MUHAMMADIYAH</div>
            </div>
            <div class="ekta-card__header-badge">
                <span>E-KTA</span>
            </div>
        </div>

        <div class="ekta-card__body">
            <div class="ekta-card__photo-column">
                <div class="ekta-card__photo-frame">
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
                <div class="ekta-card__status">ANGGOTA AKTIF</div>
            </div>

            <div class="ekta-card__details">
                <div class="ekta-card__eyebrow">IDENTITAS ANGGOTA</div>
                <div class="ekta-card__name">{{ $displayName }}</div>

                <table class="ekta-card__meta" aria-label="Biodata anggota">
                    <tr>
                        <td>NOMOR IDENTITAS</td>
                        <td>{{ $displayNia }}</td>
                    </tr>
                    <tr>
                        <td>AKTIF SEJAK</td>
                        <td>{{ $activeYear }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="ekta-card__footer" aria-hidden="true">
        <div class="ekta-card__footer-content">
            <span class="ekta-card__footer-text">KARTU ANGGOTA RESMI</span>
            <span class="ekta-card__footer-role">{{ \Illuminate\Support\Str::upper($roleLabel) }} IMM</span>
        </div>
    </div>
</div>
