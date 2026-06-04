<nav class="navbar navbar-imm navbar-expand-lg sticky-top shadow-sm" x-data="{ open: false }">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('landing') }}">
                <img src="{{ asset('images/logo.png') }}" alt="IMM Logo" class="navbar-logo-img" style="max-height:3.6rem;">
            <span class="navbar-brand-text d-none d-sm-inline">Ikatan Mahasiswa Muhammadiyah</span>
            <span class="navbar-brand-text d-inline d-sm-none">IMM</span>
        </a>

        <button class="navbar-toggler border-0" type="button" @click="open = !open" aria-label="Toggle navigation" aria-expanded="false" :aria-expanded="open.toString()">
            <i class="bi" :class="open ? 'bi-x-lg' : 'bi-list'" style="font-size:1.4rem;color:var(--imm-primary);"></i>
        </button>

        <div class="collapse navbar-collapse" :class="{ 'show': open }" id="navbarNav">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link px-3" href="{{ route('landing') }}#tentang" @click="open=false">Tentang</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ route('landing') }}#kegiatan" @click="open=false">Kegiatan</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ route('landing') }}#visi-misi" @click="open=false">Visi &amp; Misi</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="{{ route('landing') }}#kontak" @click="open=false">Kontak</a></li>
            </ul>
            <div class="d-flex gap-2 mt-2 mt-lg-0">
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center" style="border-radius:8px;">Masuk</a>
                <a href="{{ route('pendaftaran') }}" class="btn btn-imm-primary btn-sm px-3">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</nav>
