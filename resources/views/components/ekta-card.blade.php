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
    $role = \Illuminate\Support\Str::upper($roleLabel);
@endphp

<style>
    .ekta-card {
        width: 100%;
        max-width: 100%;
        aspect-ratio: 1.58 / 1;
        background: #fcfbfd;
        border: 1px solid #ded8e1;
        border-radius: 14px;
        box-shadow: 0 14px 30px rgba(61, 28, 28, 0.15);
        box-sizing: border-box;
        color: #302534;
        font-family: Helvetica, Arial, sans-serif;
        overflow: hidden;
        position: relative;
    }

    .ekta-card__pattern {
        background-image: radial-gradient(circle, rgba(128, 0, 0, 0.06) 1px, transparent 1.4px);
        background-size: 18px 18px;
        bottom: 0;
        left: 0;
        opacity: 0.35;
        position: absolute;
        right: 0;
        top: 0;
    }

    .ekta-card__swoop-accent,
    .ekta-card__swoop {
        border-bottom-right-radius: 100% 78%;
        left: -9%;
        position: absolute;
        top: -12%;
    }

    .ekta-card__swoop-accent {
        background: #a00000;
        height: 73%;
        width: 62%;
    }

    .ekta-card__swoop {
        background: #600000;
        height: 68%;
        left: -11%;
        width: 58%;
    }

    .ekta-card__brand {
        display: table;
        left: 5%;
        position: absolute;
        top: 7%;
        width: 47%;
        z-index: 2;
    }

    .ekta-card__logo-cell,
    .ekta-card__brand-copy {
        display: table-cell;
        vertical-align: middle;
    }

    .ekta-card__logo-cell {
        padding-right: 9px;
        width: 22%;
    }

    .ekta-card__logo-badge {
        align-items: center;
        background: #ffffff;
        border: 3px solid #f2ce45;
        border-radius: 50%;
        box-shadow: 0 0 0 2px #600000;
        display: flex;
        height: 58px;
        justify-content: center;
        width: 58px;
    }

    .ekta-card__logo {
        display: block;
        height: 48px;
        object-fit: contain;
        width: 42px;
    }

    .ekta-card__logo-fallback {
        align-items: center;
        background: #f2ce45;
        border-radius: 7px;
        color: #600000;
        display: flex;
        font-size: 8px;
        font-weight: 800;
        height: 45px;
        justify-content: center;
        width: 34px;
    }

    .ekta-card__title {
        color: #ffffff;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 0.035em;
        line-height: 1.18;
        overflow-wrap: anywhere;
    }

    .ekta-card__organization {
        color: rgba(255, 255, 255, 0.74);
        font-size: 8px;
        letter-spacing: 0.08em;
        line-height: 1.35;
        margin-top: 5px;
    }

    .ekta-card__top-note {
        color: #6b536d;
        position: absolute;
        right: 5%;
        text-align: right;
        top: 8%;
        z-index: 2;
    }

    .ekta-card__top-note-label {
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.16em;
    }

    .ekta-card__top-note-rule {
        background: #f2ce45;
        height: 4px;
        margin: 5px 0 4px auto;
        width: 58px;
    }

    .ekta-card__top-note-value {
        color: #800000;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.14em;
    }

    .ekta-card__photo-frame {
        background: #ffffff;
        border: 3px solid #600000;
        border-radius: 50%;
        bottom: 9%;
        box-shadow: 0 0 0 4px #ffffff, 0 0 0 7px #a00000, 5px 6px 0 rgba(242, 206, 69, 0.92);
        box-sizing: border-box;
        height: 39%;
        left: 5%;
        overflow: hidden;
        padding: 5px;
        position: absolute;
        width: 24%;
        z-index: 4;
    }

    .ekta-card__photo-inner {
        border-radius: 50%;
        height: 100%;
        overflow: hidden;
        width: 100%;
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
        background: #600000;
        color: #ffffff;
        display: flex;
        font-size: 30px;
        font-weight: 800;
        justify-content: center;
    }

    .ekta-card__details {
        left: 36%;
        position: absolute;
        right: 5%;
        top: 45%;
        z-index: 3;
    }

    .ekta-card__eyebrow {
        color: #a00000;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.17em;
        line-height: 1.2;
    }

    .ekta-card__name {
        color: #600000;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: 0.025em;
        line-height: 1.12;
        margin-top: 5px;
        max-width: 100%;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .ekta-card__meta {
        border-collapse: collapse;
        margin-top: 12px;
        width: 100%;
    }

    .ekta-card__meta td {
        border-top: 1px solid #e4dce7;
        font-size: 11px;
        line-height: 1.25;
        padding: 5px 0 3px;
        vertical-align: top;
    }

    .ekta-card__meta td:first-child {
        color: #725c75;
        font-weight: 700;
        letter-spacing: 0.08em;
        width: 47%;
    }

    .ekta-card__meta td:last-child {
        color: #600000;
        font-weight: 800;
        overflow-wrap: anywhere;
        text-align: right;
        word-break: break-word;
    }

    .ekta-card__bottom-line {
        background: #600000;
        bottom: 0;
        height: 6px;
        left: 0;
        position: absolute;
        right: 0;
        z-index: 5;
    }

    .ekta-card__bottom-line::after {
        background: #f2ce45;
        content: "";
        height: 6px;
        position: absolute;
        right: 0;
        width: 23%;
    }

    .ekta-card__footer {
        bottom: 3.2%;
        color: #8b7a8d;
        font-size: 5px;
        font-weight: 700;
        letter-spacing: 0.13em;
        position: absolute;
        right: 5%;
        text-align: right;
        z-index: 3;
    }

    @media (max-width: 360px) {
        .ekta-card__logo-badge {
            height: 50px;
            width: 50px;
        }

        .ekta-card__logo {
            height: 41px;
            width: 36px;
        }

        .ekta-card__title {
            font-size: 15px;
        }

        .ekta-card__organization,
        .ekta-card__top-note-label,
        .ekta-card__top-note-value {
            font-size: 5px;
        }

        .ekta-card__name {
            font-size: 19px;
        }

        .ekta-card__meta td {
            font-size: 9px;
        }
    }
</style>

<div {{ $attributes->merge(['class' => 'ekta-card']) }} data-testid="ekta-card">
    <div class="ekta-card__pattern" aria-hidden="true"></div>
    <div class="ekta-card__swoop-accent" aria-hidden="true"></div>
    <div class="ekta-card__swoop" aria-hidden="true"></div>

    <div class="ekta-card__brand">
        <div class="ekta-card__logo-cell">
            @if($logoSrc)
                <span class="ekta-card__logo-badge">
                    <img src="{{ $logoSrc }}" alt="Logo IMM" class="ekta-card__logo">
                </span>
            @else
                <span class="ekta-card__logo-badge">
                    <span class="ekta-card__logo-fallback" aria-label="Logo IMM">IMM</span>
                </span>
            @endif
        </div>
        <div class="ekta-card__brand-copy">
            <div class="ekta-card__title">KARTU TANDA {{ $role }}</div>
            <div class="ekta-card__organization">IKATAN MAHASISWA MUHAMMADIYAH</div>
        </div>
    </div>

    <div class="ekta-card__top-note">
        <div class="ekta-card__top-note-label">IDENTITAS DIGITAL</div>
        <div class="ekta-card__top-note-rule"></div>
        <div class="ekta-card__top-note-value">E-KTA IMM</div>
    </div>

    <div class="ekta-card__photo-frame">
        <div class="ekta-card__photo-inner">
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
    </div>

    <div class="ekta-card__details">
        <div class="ekta-card__eyebrow">ANGGOTA {{ $role }}</div>
        <div class="ekta-card__name">{{ $displayName }}</div>

        <table class="ekta-card__meta" aria-label="Biodata anggota">
            <tr>
                <td>NIA</td>
                <td>{{ $displayNia }}</td>
            </tr>
            <tr>
                <td>AKTIF SEJAK</td>
                <td>{{ $activeYear }}</td>
            </tr>
        </table>
    </div>

    <div class="ekta-card__footer">KARTU ANGGOTA RESMI</div>
    <div class="ekta-card__bottom-line" aria-hidden="true"></div>
</div>
