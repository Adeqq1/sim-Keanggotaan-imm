<footer class="footer-imm py-5" id="kontak">
    <div class="container">
        <div class="row g-4 mb-5">

            {{-- Brand & Sosmed --}}
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="footer-logo-box" style="rgb(255, 255, 255);">
                        <img src="{{ asset('images/logo.png') }}" alt="IMM Logo" class="navbar-logo-img" style="max-height:2.5rem;">
                    </div>
                    <span style="color:#e2e8f0;font-weight:700;font-size:1rem;">Ikatan Mahasiswa Muhammadiyah</span>
                </div>
                <p class="footer-link mb-3" style="font-size:.9rem;line-height:1.7;">
                    Organisasi otonom Muhammadiyah yang bergerak di bidang kemahasiswaan. Mencetak kader berakhlak mulia, intelektual, dan humanis sejak 1964.
                </p>
                <div class="d-flex gap-2">
                    @foreach(config('landing.social_links') as $social)
                        @if($social['url'])
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                           class="footer-social-btn" aria-label="{{ $social['label'] }}">
                            <i class="bi {{ $social['icon'] }}"></i>
                        </a>
                        @endif
                    @endforeach
                    {{-- TODO: Isi URL sosmed di config/landing.php untuk menampilkan ikon --}}
                </div>
            </div>

            {{-- Navigasi --}}
            <div class="col-6 col-lg-2 offset-lg-1">
                <h2 class="h6 mb-3" style="color:#e2e8f0;font-weight:700;">Navigasi</h2>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="{{ route('landing') }}" class="footer-link small">Beranda</a></li>
                    <li class="mb-2"><a href="{{ route('landing') }}#tentang" class="footer-link small">Tentang IMM</a></li>
                    <li class="mb-2"><a href="{{ route('landing') }}#kegiatan" class="footer-link small">Kegiatan</a></li>
                    <li class="mb-2"><a href="{{ route('landing') }}#visi-misi" class="footer-link small">Visi &amp; Misi</a></li>
                </ul>
            </div>

            {{-- Sistem --}}
            <div class="col-6 col-lg-2">
                <h2 class="h6 mb-3" style="color:#e2e8f0;font-weight:700;">Sistem</h2>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="{{ route('pendaftaran') }}" class="footer-link small">Daftar Anggota</a></li>
                    <li class="mb-2"><a href="{{ route('login') }}" class="footer-link small">Login Anggota</a></li>
                </ul>
            </div>

            {{-- Kontak --}}
            <div class="col-lg-3">
                <h2 class="h6 mb-3" style="color:#e2e8f0;font-weight:700;">Kontak</h2>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt text-primary mt-1 flex-shrink-0" aria-hidden="true"></i>
                        {{-- TODO: Konfirmasi alamat resmi DPP IMM --}}
                        <span class="footer-link small">{{ config('landing.contact.address') }}</span>
                    </li>
                    <li class="mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-envelope text-primary flex-shrink-0" aria-hidden="true"></i>
                        {{-- TODO: Konfirmasi email resmi DPP IMM --}}
                        <a href="mailto:{{ config('landing.contact.email') }}" class="footer-link small">
                            {{ config('landing.contact.email') }}
                        </a>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-globe text-primary flex-shrink-0" aria-hidden="true"></i>
                        <a href="{{ config('landing.contact.website') }}" target="_blank" rel="noopener noreferrer" class="footer-link small">
                            {{ config('landing.contact.website') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <hr style="border-color:rgba(255,255,255,0.1);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <p class="mb-0 small" style="color:#64748b;">&copy; {{ date('Y') }} Ikatan Mahasiswa Muhammadiyah. All rights reserved.</p>
            <p class="mb-0 small" style="color:#64748b;">Powered by Adeqq &mdash; Teknologi Informasi 2022</p>
        </div>
    </div>
</footer>
