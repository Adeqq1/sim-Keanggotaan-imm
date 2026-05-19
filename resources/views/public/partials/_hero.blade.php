<section class="hero-section" id="beranda">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-7 text-white">
                <span class="hero-badge px-3 py-1 mb-4 d-inline-block" style="font-size:.85rem;font-weight:600;">
                    <i class="bi bi-stars me-1 text-warning"></i> Organisasi Mahasiswa Muhammadiyah
                </span>

                {{-- Satu kalimat utuh untuk screen reader, visual break via CSS --}}
                <h1 class="display-4 fw-bold mb-4 lh-sm">
                    Bersama IMM, Wujudkan Generasi <span class="text-warning">Muslim Intelektual</span>
                </h1>

                <p class="lead mb-5" style="max-width:520px;line-height:1.7;opacity:.9;">
                    Ikatan Mahasiswa Muhammadiyah hadir sebagai wadah pengembangan diri mahasiswa yang berakhlak mulia, intelektual, dan humanis — siap berkontribusi untuk umat dan bangsa.
                </p>

                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('pendaftaran') }}" class="btn btn-light btn-lg fw-bold px-4 py-3" style="border-radius:12px;color:var(--imm-blue);">
                        <i class="bi bi-person-plus me-2"></i>Daftar Anggota
                    </a>
                    <a href="#tentang" class="btn btn-imm-outline btn-lg px-4 py-3">
                        <i class="bi bi-play-circle me-2"></i>Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <div class="d-flex flex-column gap-3" style="max-width:340px;width:100%;">
                    <div class="hero-glass-card d-flex align-items-center gap-3">
                        <div class="hero-glass-icon" style="background:rgba(251,191,36,0.2);">
                            <i class="bi bi-card-checklist fs-4 text-warning"></i>
                        </div>
                        <div class="text-white">
                            <div class="fw-bold">Pendaftaran Online</div>
                            <div class="small" style="opacity:.75;">Proses cepat &amp; mudah</div>
                        </div>
                    </div>
                    <div class="hero-glass-card d-flex align-items-center gap-3">
                        <div class="hero-glass-icon" style="background:rgba(52,211,153,0.2);">
                            <i class="bi bi-person-badge fs-4 text-success"></i>
                        </div>
                        <div class="text-white">
                            <div class="fw-bold">E-KTA Digital</div>
                            <div class="small" style="opacity:.75;">Kartu anggota elektronik</div>
                        </div>
                    </div>
                    <div class="hero-glass-card d-flex align-items-center gap-3">
                        <div class="hero-glass-icon" style="background:rgba(96,165,250,0.2);">
                            <i class="bi bi-archive fs-4 text-info"></i>
                        </div>
                        <div class="text-white">
                            <div class="fw-bold">Arsip Terintegrasi</div>
                            <div class="small" style="opacity:.75;">Dokumen aman &amp; terkelola</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
