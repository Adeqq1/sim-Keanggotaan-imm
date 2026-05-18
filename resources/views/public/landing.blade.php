<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>IMM | Ikatan Mahasiswa Muhammadiyah</title>
    <meta name="description" content="Profil resmi Ikatan Mahasiswa Muhammadiyah (IMM). Bergabung, berkarya, dan berkembang bersama.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1e3a8a">
    <link rel="manifest" href="/manifest.json">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>

    <style>
        :root {
            --imm-blue: #1e3a8a;
            --imm-blue-mid: #2563eb;
            --imm-blue-light: #3b82f6;
            --imm-red: #dc2626;
            --imm-yellow: #f59e0b;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; }

        /* Navbar */
        .navbar-imm { background: rgba(255,255,255,0.97); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.08); }
        .navbar-imm .nav-link { color: #374151; font-weight: 500; transition: color .2s; }
        .navbar-imm .nav-link:hover { color: var(--imm-blue); }
        .navbar-brand-text { font-weight: 800; color: var(--imm-blue); font-size: 1.1rem; }

        /* Hero */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(30,58,138,0.92) 0%, rgba(37,99,235,0.85) 100%),
                        url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920&q=80') center/cover no-repeat;
            display: flex; align-items: center;
        }
        .hero-badge { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); border-radius: 50px; display: inline-block; }

        /* Section titles */
        .section-label { color: var(--imm-blue-light); font-weight: 700; font-size: .85rem; letter-spacing: .1em; text-transform: uppercase; }
        .section-title { font-weight: 800; color: #111827; line-height: 1.2; }

        /* Stats */
        .stats-section { background: linear-gradient(135deg, var(--imm-blue) 0%, var(--imm-blue-mid) 100%); }
        .stat-card { border-left: 3px solid rgba(255,255,255,0.3); }

        /* Program cards */
        .program-card { border: none; border-radius: 16px; overflow: hidden; transition: transform .3s, box-shadow .3s; }
        .program-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important; }
        .program-card .card-img-top { height: 200px; object-fit: cover; }
        .program-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }

        /* Visi Misi */
        .visi-misi-section { background-color: #f8fafc; }
        .visi-card { border: none; border-radius: 20px; border-top: 4px solid var(--imm-blue); }
        .misi-item { border-left: 3px solid var(--imm-blue-light); padding-left: 1rem; margin-bottom: .75rem; }

        /* CTA */
        .cta-section {
            background: linear-gradient(135deg, rgba(30,58,138,0.95) 0%, rgba(220,38,38,0.85) 100%),
                        url('https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=1920&q=80') center/cover no-repeat;
        }

        /* Footer */
        .footer-imm { background: #0f172a; }
        .footer-link { color: #94a3b8; text-decoration: none; transition: color .2s; }
        .footer-link:hover { color: #e2e8f0; }

        /* Btn custom */
        .btn-imm-primary { background: var(--imm-blue); border-color: var(--imm-blue); color: #fff; font-weight: 600; border-radius: 10px; padding: .65rem 1.5rem; }
        .btn-imm-primary:hover { background: var(--imm-blue-mid); border-color: var(--imm-blue-mid); color: #fff; }
        .btn-imm-outline { border: 2px solid #fff; color: #fff; font-weight: 600; border-radius: 10px; padding: .65rem 1.5rem; background: transparent; }
        .btn-imm-outline:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: #fff; }
    </style>
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="navbar navbar-imm navbar-expand-lg sticky-top shadow-sm" x-data="{ open: false }">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('landing') }}">
            <div style="width:36px;height:36px;background:var(--imm-blue);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <span style="color:#fff;font-weight:900;font-size:.9rem;">IMM</span>
            </div>
            <span class="navbar-brand-text d-none d-sm-inline">Ikatan Mahasiswa Muhammadiyah</span>
            <span class="navbar-brand-text d-inline d-sm-none">IMM</span>
        </a>

        <button class="navbar-toggler border-0" type="button" @click="open = !open" aria-label="Toggle navigation">
            <i class="bi" :class="open ? 'bi-x-lg' : 'bi-list'" style="font-size:1.4rem;color:var(--imm-blue);"></i>
        </button>

        <div class="collapse navbar-collapse" :class="{ 'show': open }" id="navbarNav">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link px-3" href="#tentang" @click="open=false">Tentang</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#program" @click="open=false">Program</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#visi-misi" @click="open=false">Visi & Misi</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#kontak" @click="open=false">Kontak</a></li>
            </ul>
            <div class="d-flex gap-2 mt-2 mt-lg-0">
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm px-3 fw-600" style="border-radius:8px;font-weight:600;">Masuk</a>
                <a href="{{ route('pendaftaran') }}" class="btn btn-imm-primary btn-sm px-3">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</nav>

{{-- ===== HERO ===== --}}
<section class="hero-section" id="beranda">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-7 text-white">
                <span class="hero-badge px-3 py-1 mb-4 d-inline-block" style="font-size:.85rem;font-weight:600;">
                    <i class="bi bi-stars me-1 text-warning"></i> Organisasi Mahasiswa Muhammadiyah
                </span>
                <h1 class="display-4 fw-bold mb-4 lh-sm">
                    Bersama IMM,<br>
                    <span style="color:#fbbf24;">Wujudkan Generasi</span><br>
                    Muslim Intelektual
                </h1>
                <p class="lead mb-5 opacity-90" style="max-width:520px;line-height:1.7;">
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
                <div class="row g-3" style="max-width:340px;">
                    <div class="col-12">
                        <div class="p-3 rounded-3 text-white d-flex align-items-center gap-3" style="background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.2);">
                            <div class="rounded-2 p-2" style="background:rgba(251,191,36,0.2);"><i class="bi bi-card-checklist fs-4 text-warning"></i></div>
                            <div><div class="fw-bold">Pendaftaran Online</div><div class="small opacity-75">Proses cepat & mudah</div></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3 text-white d-flex align-items-center gap-3" style="background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.2);">
                            <div class="rounded-2 p-2" style="background:rgba(52,211,153,0.2);"><i class="bi bi-person-badge fs-4 text-success"></i></div>
                            <div><div class="fw-bold">E-KTA Digital</div><div class="small opacity-75">Kartu anggota elektronik</div></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3 text-white d-flex align-items-center gap-3" style="background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.2);">
                            <div class="rounded-2 p-2" style="background:rgba(96,165,250,0.2);"><i class="bi bi-archive fs-4 text-info"></i></div>
                            <div><div class="fw-bold">Arsip Terintegrasi</div><div class="small opacity-75">Dokumen aman & terkelola</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== STATISTIK ===== --}}
<section class="stats-section py-5">
    <div class="container">
        <div class="row g-4 text-white text-center">
            <div class="col-6 col-md-3">
                <div class="stat-card px-3 py-2">
                    <div class="display-5 fw-bold" style="color:#fbbf24;">1000+</div>
                    <div class="mt-1 opacity-80 fw-500">Anggota Aktif</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card px-3 py-2">
                    <div class="display-5 fw-bold" style="color:#fbbf24;">50+</div>
                    <div class="mt-1 opacity-80 fw-500">Komisariat</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card px-3 py-2">
                    <div class="display-5 fw-bold" style="color:#fbbf24;">100+</div>
                    <div class="mt-1 opacity-80 fw-500">Kegiatan / Tahun</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card px-3 py-2">
                    <div class="display-5 fw-bold" style="color:#fbbf24;">60+</div>
                    <div class="mt-1 opacity-80 fw-500">Tahun Berkarya</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== TENTANG IMM ===== --}}
<section class="py-6 py-md-7" id="tentang" style="padding-top:5rem;padding-bottom:5rem;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img
                        src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=800&q=80"
                        alt="Kegiatan diskusi anggota IMM"
                        class="img-fluid rounded-4 shadow-lg w-100"
                        style="height:420px;object-fit:cover;"
                        loading="lazy"
                    >
                    <div class="position-absolute bottom-0 start-0 m-3 p-3 rounded-3 text-white" style="background:var(--imm-blue);max-width:220px;">
                        <div class="fw-bold">Berdiri sejak</div>
                        <div class="display-6 fw-bold" style="color:#fbbf24;">1964</div>
                        <div class="small opacity-80">14 Maret 1964, Yogyakarta</div>
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
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="d-flex align-items-start gap-2">
                            <div class="rounded-2 p-2 flex-shrink-0" style="background:#eff6ff;">
                                <i class="bi bi-book-half text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Religiusitas</div>
                                <div class="text-muted" style="font-size:.8rem;">Iman & akhlak mulia</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-start gap-2">
                            <div class="rounded-2 p-2 flex-shrink-0" style="background:#f0fdf4;">
                                <i class="bi bi-lightbulb text-success"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Intelektualitas</div>
                                <div class="text-muted" style="font-size:.8rem;">Ilmu & wawasan luas</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-start gap-2">
                            <div class="rounded-2 p-2 flex-shrink-0" style="background:#fef3c7;">
                                <i class="bi bi-people text-warning"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Humanitas</div>
                                <div class="text-muted" style="font-size:.8rem;">Peduli sesama</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-start gap-2">
                            <div class="rounded-2 p-2 flex-shrink-0" style="background:#fdf2f8;">
                                <i class="bi bi-award text-danger"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Kaderisasi</div>
                                <div class="text-muted" style="font-size:.8rem;">Pemimpin masa depan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== PROGRAM KERJA ===== --}}
<section class="py-5 bg-light" id="program" style="padding-top:5rem;padding-bottom:5rem;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Apa yang Kami Lakukan</span>
            <h2 class="section-title display-6 mt-2">Program Unggulan IMM</h2>
            <p class="text-muted mt-3 mx-auto" style="max-width:520px;">Berbagai program dirancang untuk membentuk kader yang kompeten, berkarakter, dan siap berkontribusi nyata.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card program-card shadow-sm h-100">
                    <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=600&q=80"
                         alt="Kajian rutin IMM" class="card-img-top" loading="lazy">
                    <div class="card-body p-4">
                        <div class="program-icon mb-3" style="background:#eff6ff;">
                            <i class="bi bi-journal-text text-primary fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Kajian Rutin</h5>
                        <p class="text-muted small mb-0" style="line-height:1.7;">
                            Forum diskusi dan kajian keislaman yang diselenggarakan secara rutin untuk memperdalam pemahaman agama dan isu-isu kontemporer.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card program-card shadow-sm h-100">
                    <img src="https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=600&q=80"
                         alt="Pengabdian masyarakat IMM" class="card-img-top" loading="lazy">
                    <div class="card-body p-4">
                        <div class="program-icon mb-3" style="background:#f0fdf4;">
                            <i class="bi bi-heart text-success fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Pengabdian Masyarakat</h5>
                        <p class="text-muted small mb-0" style="line-height:1.7;">
                            Kegiatan sosial dan bakti masyarakat sebagai wujud nyata kepedulian IMM terhadap lingkungan sekitar dan masyarakat yang membutuhkan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card program-card shadow-sm h-100">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80"
                         alt="Pelatihan kader IMM" class="card-img-top" loading="lazy">
                    <div class="card-body p-4">
                        <div class="program-icon mb-3" style="background:#fef3c7;">
                            <i class="bi bi-mortarboard text-warning fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Pelatihan Kader</h5>
                        <p class="text-muted small mb-0" style="line-height:1.7;">
                            Program kaderisasi berjenjang mulai dari Darul Arqam Dasar hingga Darul Arqam Madya untuk membentuk pemimpin yang tangguh dan berintegritas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== VISI & MISI ===== --}}
<section class="visi-misi-section py-5" id="visi-misi" style="padding-top:5rem;padding-bottom:5rem;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Arah & Tujuan</span>
            <h2 class="section-title display-6 mt-2">Visi & Misi IMM</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card visi-card shadow-sm h-100 p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 p-3" style="background:var(--imm-blue);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                        <h3 class="fw-bold mb-0 fs-4">Visi</h3>
                    </div>
                    <p class="text-muted" style="line-height:1.8;font-size:1.05rem;">
                        Terbentuknya akademisi Islam yang berakhlak mulia dalam rangka mencapai tujuan Muhammadiyah.
                    </p>
                    <div class="mt-4 p-3 rounded-3" style="background:#eff6ff;border-left:4px solid var(--imm-blue);">
                        <p class="mb-0 small text-primary fw-500" style="font-style:italic;">
                            "Mengutamakan iman, ilmu, dan amal sebagai landasan gerak organisasi."
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow-sm h-100 p-4" style="border:none;border-radius:20px;border-top:4px solid var(--imm-yellow);">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 p-3" style="background:var(--imm-yellow);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                            </svg>
                        </div>
                        <h3 class="fw-bold mb-0 fs-4">Misi</h3>
                    </div>
                    <div>
                        @php
                            $misi = [
                                ['icon' => 'bi-book', 'text' => 'Membina dan mengembangkan kemampuan akademik mahasiswa yang berlandaskan nilai-nilai Islam.'],
                                ['icon' => 'bi-people', 'text' => 'Membentuk kader yang memiliki kepribadian muslim, cakap, dan percaya diri.'],
                                ['icon' => 'bi-globe', 'text' => 'Mengembangkan potensi kreatif, keilmuan, sosial, dan budaya mahasiswa.'],
                                ['icon' => 'bi-shield-check', 'text' => 'Memperkuat ukhuwah islamiyah dan solidaritas antar mahasiswa Muslim.'],
                                ['icon' => 'bi-arrow-up-circle', 'text' => 'Berperan aktif dalam pembangunan masyarakat yang adil, makmur, dan beradab.'],
                            ];
                        @endphp
                        @foreach($misi as $item)
                        <div class="misi-item d-flex align-items-start gap-3 mb-3">
                            <i class="bi {{ $item['icon'] }} text-primary mt-1 flex-shrink-0"></i>
                            <p class="mb-0 text-muted small" style="line-height:1.7;">{{ $item['text'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== CTA DAFTAR ===== --}}
<section class="cta-section py-5 text-white text-center" style="padding-top:5rem;padding-bottom:5rem;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <span class="hero-badge px-3 py-1 mb-4 d-inline-block" style="font-size:.85rem;font-weight:600;">
                    <i class="bi bi-rocket-takeoff me-1 text-warning"></i> Bergabung Sekarang
                </span>
                <h2 class="display-5 fw-bold mb-4">Siap Berkarya Bersama IMM?</h2>
                <p class="lead mb-5 opacity-90">
                    Jadilah bagian dari gerakan mahasiswa yang membawa perubahan. Daftarkan diri Anda sekarang dan mulai perjalanan bersama ribuan kader IMM di seluruh Indonesia.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('pendaftaran') }}" class="btn btn-light btn-lg fw-bold px-5 py-3" style="border-radius:12px;color:var(--imm-blue);">
                        <i class="bi bi-person-plus me-2"></i>Daftar Anggota Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-imm-outline btn-lg px-5 py-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sudah Anggota? Masuk
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== FOOTER ===== --}}
<footer class="footer-imm py-5" id="kontak">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width:40px;height:40px;background:var(--imm-blue-light);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <span style="color:#fff;font-weight:900;font-size:.9rem;">IMM</span>
                    </div>
                    <span style="color:#e2e8f0;font-weight:700;font-size:1rem;">Ikatan Mahasiswa Muhammadiyah</span>
                </div>
                <p class="footer-link mb-3" style="font-size:.9rem;line-height:1.7;">
                    Organisasi otonom Muhammadiyah yang bergerak di bidang kemahasiswaan. Mencetak kader berakhlak mulia, intelektual, dan humanis sejak 1964.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm rounded-2" style="background:rgba(255,255,255,0.1);color:#94a3b8;" aria-label="Instagram IMM">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-sm rounded-2" style="background:rgba(255,255,255,0.1);color:#94a3b8;" aria-label="Twitter IMM">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <a href="#" class="btn btn-sm rounded-2" style="background:rgba(255,255,255,0.1);color:#94a3b8;" aria-label="YouTube IMM">
                        <i class="bi bi-youtube"></i>
                    </a>
                    <a href="#" class="btn btn-sm rounded-2" style="background:rgba(255,255,255,0.1);color:#94a3b8;" aria-label="Facebook IMM">
                        <i class="bi bi-facebook"></i>
                    </a>
                </div>
            </div>
            <div class="col-6 col-lg-2 offset-lg-1">
                <h6 style="color:#e2e8f0;font-weight:700;" class="mb-3">Navigasi</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#beranda" class="footer-link small">Beranda</a></li>
                    <li class="mb-2"><a href="#tentang" class="footer-link small">Tentang IMM</a></li>
                    <li class="mb-2"><a href="#program" class="footer-link small">Program Kerja</a></li>
                    <li class="mb-2"><a href="#visi-misi" class="footer-link small">Visi & Misi</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 style="color:#e2e8f0;font-weight:700;" class="mb-3">Sistem</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="{{ route('pendaftaran') }}" class="footer-link small">Daftar Anggota</a></li>
                    <li class="mb-2"><a href="{{ route('login') }}" class="footer-link small">Login Anggota</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6 style="color:#e2e8f0;font-weight:700;" class="mb-3">Kontak</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt text-primary mt-1 flex-shrink-0"></i>
                        <span class="footer-link small">Jl. Cik Di Tiro No.23, Yogyakarta, Indonesia</span>
                    </li>
                    <li class="mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-envelope text-primary flex-shrink-0"></i>
                        <a href="mailto:info@imm.or.id" class="footer-link small">info@imm.or.id</a>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-globe text-primary flex-shrink-0"></i>
                        <a href="https://imm.or.id" target="_blank" rel="noopener" class="footer-link small">imm.or.id</a>
                    </li>
                </ul>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,0.1);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <p class="mb-0 small" style="color:#64748b;">&copy; {{ date('Y') }} Ikatan Mahasiswa Muhammadiyah. All rights reserved.</p>
            <p class="mb-0 small" style="color:#64748b;">Powered by SIM-IMM &mdash; Sistem Informasi Manajemen Keanggotaan</p>
        </div>
    </div>
</footer>

</body>
</html>
