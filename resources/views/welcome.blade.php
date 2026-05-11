<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>SIM-IMM | Keanggotaan & Kearsipan</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#1e3a8a">
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="https://ui-avatars.com/api/?name=IMM&background=1e3a8a&color=fff&size=192">
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js');
                });
            }
        </script>
        
        <style>
            body {
                background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
                min-height: 100vh;
                color: white;
                font-family: 'Inter', sans-serif;
            }
            .hero-section {
                padding-top: 80px;
                padding-bottom: 40px;
            }
            .btn-light {
                color: #1e3a8a;
                font-weight: 700;
                padding: 12px 30px;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            }
            .btn-outline-light {
                border-width: 2px;
                font-weight: 600;
                padding: 12px 30px;
                border-radius: 12px;
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container text-center hero-section px-4">
            <div class="mb-4">
                <i class="bi bi-shield-check display-1 mb-3"></i>
                <h1 class="display-5 fw-bold mb-2">SIM-IMM</h1>
                <p class="lead mb-5 opacity-75">Sistem Informasi Manajemen Keanggotaan & Kearsipan</p>
            </div>
            
            <div class="d-grid gap-3 col-11 mx-auto mb-5">
                <a href="{{ route('pendaftaran') }}" class="btn btn-light btn-lg py-3">Daftar Sekarang</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg py-3">Masuk ke Sistem</a>
            </div>

            <div class="row g-3 text-start mt-5">
                <div class="col-12">
                    <div class="glass-card d-flex align-items-center">
                        <i class="bi bi-card-checklist fs-2 me-3 text-info"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">Pendaftaran Online</h6>
                            <p class="small mb-0 opacity-75">Proses cepat dan validasi mudah oleh admin.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="glass-card d-flex align-items-center">
                        <i class="bi bi-person-badge fs-2 me-3 text-warning"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">E-KTA & Sertifikat</h6>
                            <p class="small mb-0 opacity-75">Akses kartu anggota dan sertifikat digital kapan saja.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="glass-card d-flex align-items-center">
                        <i class="bi bi-archive fs-2 me-3 text-success"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">Arsip Terintegrasi</h6>
                            <p class="small mb-0 opacity-75">Penyimpanan dokumen organisasi yang aman.</p>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="mt-5 pt-5 pb-4">
                <p class="small opacity-50">&copy; {{ date('Y') }} Ikatan Mahasiswa Muhammadiyah</p>
            </footer>
        </div>
    </body>
</html>
