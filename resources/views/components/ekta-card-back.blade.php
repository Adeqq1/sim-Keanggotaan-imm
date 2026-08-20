@props([
    'anggota',
    'roleLabel' => 'Kader',
    'qrCodeSrc' => null,
    'logoSrc' => null,
])

@php
    $activeYear = $anggota->tahun_daftar ?? $anggota->created_at?->format('Y') ?? 'BELUM TERSEDIA';
@endphp

<style>
    .ekta-card-back { width: 100%; aspect-ratio: 1.58 / 1; background: #fcfbfd; border: 1px solid #ded8e1; border-radius: 14px; box-shadow: 0 14px 30px rgba(61, 28, 28, .15); box-sizing: border-box; color: #302534; font-family: Helvetica, Arial, sans-serif; overflow: hidden; position: relative; padding: 12px 16px 10px; }
    .ekta-card-back__pattern { position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(128, 0, 0, .06) 1px, transparent 1.4px); background-size: 18px 18px; opacity: .35; }
    .ekta-card-back__content { position: relative; z-index: 1; }
    .ekta-card-back__header { border-bottom: 2px solid #600000; padding-bottom: 5px; margin-bottom: 7px; }
    .ekta-card-back__title { color: #600000; font-size: 11px; font-weight: 800; letter-spacing: .05em; }
    .ekta-card-back__subtitle { color: #725c75; font-size: 7.5px; font-weight: 700; letter-spacing: .08em; }
    .ekta-card-back__rules { color: #4a3b4f; font-size: 7.5px; line-height: 1.3; margin: 0; padding-left: 14px; }
    .ekta-card-back__rules li { margin-bottom: 2px; }
    .ekta-card-back__motto-box { background: rgba(96, 0, 0, .04); border-left: 3px solid #f2ce45; margin-top: 5px; padding: 3px 7px; }
    .ekta-card-back__motto { color: #600000; font-size: 7.5px; font-style: italic; font-weight: 700; }
    .ekta-card-back__competence { color: #725c75; font-size: 6.5px; }
    .ekta-card-back__footer { align-items: flex-end; bottom: 10px; display: table; left: 16px; position: absolute; right: 16px; z-index: 2; }
    .ekta-card-back__qr, .ekta-card-back__signature { display: table-cell; vertical-align: bottom; }
    .ekta-card-back__qr { text-align: center; width: 25%; }
    .ekta-card-back__qr img { background: #fff; border: 1px solid #ded8e1; display: block; height: 45px; margin: 0 auto; padding: 2px; width: 45px; }
    .ekta-card-back__qr-label { color: #600000; font-size: 5px; font-weight: 700; margin-top: 2px; }
    .ekta-card-back__signature { color: #4a3b4f; font-size: 6.5px; text-align: right; }
    .ekta-card-back__signature strong { color: #600000; display: block; }
    .ekta-card-back__signature small { color: #725c75; display: block; margin-bottom: 15px; }
    .ekta-card-back__line { background: #600000; bottom: 0; height: 5px; left: 0; position: absolute; right: 0; }
    .ekta-card-back__line::after { background: #f2ce45; content: ''; height: 5px; position: absolute; right: 0; width: 23%; }
</style>

<div {{ $attributes->merge(['class' => 'ekta-card-back']) }} data-testid="ekta-card-back">
    <div class="ekta-card-back__pattern" aria-hidden="true"></div>
    <div class="ekta-card-back__content">
        <div class="ekta-card-back__header">
            <div class="ekta-card-back__title">KETENTUAN KARTU ANGGOTA</div>
            <div class="ekta-card-back__subtitle">IKATAN MAHASISWA MUHAMMADIYAH</div>
        </div>
        <ol class="ekta-card-back__rules">
            <li>Kartu ini merupakan bukti sah keanggotaan Ikatan Mahasiswa Muhammadiyah.</li>
            <li>Pemegang kartu wajib menjunjung tinggi nama baik dan tujuan ikatan.</li>
            <li>Kartu tidak dapat dipindahtangankan dan berlaku selama menjadi kader aktif.</li>
            <li>Apabila kartu hilang atau ditemukan, harap diserahkan ke Pimpinan IMM terdekat.</li>
        </ol>
        <div class="ekta-card-back__motto-box">
            <div class="ekta-card-back__motto">"Anggun dalam Moral, Unggul dalam Intelektual"</div>
            <div class="ekta-card-back__competence">Tri Kompetensi: Religiusitas • Intelektualitas • Humanitas</div>
        </div>
    </div>
    <div class="ekta-card-back__footer">
        <div class="ekta-card-back__qr">
            @if ($qrCodeSrc)<img src="{{ $qrCodeSrc }}" alt="QR Code Verifikasi">@endif
            <div class="ekta-card-back__qr-label">VERIFIKASI RESMI</div>
        </div>
        <div class="ekta-card-back__signature">
            <strong>PENGESAHAN KARTU</strong>
            <small>Diterbitkan: {{ $activeYear }}</small>
            Pimpinan Komisariat / Cabang IMM
        </div>
    </div>
    <div class="ekta-card-back__line" aria-hidden="true"></div>
</div>
