<section class="section-padding" id="tentang">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    {{-- TODO: Ganti dengan foto resmi kegiatan IMM --}}
                    <img
                        src="/images/landing/about.jpg"
                        alt="Kegiatan diskusi anggota IMM"
                        class="img-fluid rounded-4 shadow-lg w-100"
                        style="height:420px;object-fit:cover;"
                        loading="lazy"
                        width="800"
                        height="420"
                    >
                    <div class="about-year-badge">
                        <div class="fw-bold">Berdiri sejak</div>
                        <div class="display-6 fw-bold text-warning">1964</div>
                        <div class="small" style="opacity:.8;">14 Maret 1964, Yogyakarta</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <span class="section-label">Tentang Kami</span>
                <h2 class="section-title display-6 mt-2 mb-4">Mengenal Ikatan Mahasiswa Muhammadiyah</h2>
                <p class="text-muted mb-3" style="line-height:1.8;">
                    Ikatan Mahasiswa Muhammadiyah (IMM) adalah organisasi otonom Muhammadiyah yang bergerak di bidang kemahasiswaan. Berdiri pada 14 Maret 1964 di Yogyakarta, IMM berkomitmen mencetak kader yang berakhlak mulia, intelektual, dan humanis.
                </p>
                <p class="text-muted mb-4" style="line-height:1.8;">
                    Dengan tiga pilar utama — <strong>Religiusitas</strong>, <strong>Intelektualitas</strong>, dan <strong>Humanitas</strong> — IMM hadir sebagai gerakan mahasiswa yang tidak hanya fokus pada akademik, tetapi juga aktif berkontribusi dalam kehidupan sosial kemasyarakatan.
                </p>

                <div class="row g-3">
                    @foreach(config('landing.pillars') as $pillar)
                    <div class="col-6">
                        <div class="d-flex align-items-start gap-2">
                            <div class="pillar-icon-box" style="background:{{ $pillar['bg'] }};">
                                <i class="bi {{ $pillar['icon'] }} {{ $pillar['color'] }}"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">{{ $pillar['title'] }}</div>
                                <div class="text-muted" style="font-size:.8rem;">{{ $pillar['desc'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
