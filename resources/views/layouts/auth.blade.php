<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIM-IMM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#800000">
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="https://ui-avatars.com/api/?name=IMM&background=800000&color=fff&size=192">
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js');
                });
            }
        </script>
    </head>
    <body>
        {{-- Split layout: kiri branding (desktop only) + kanan form --}}
        <div class="auth-split-wrapper">

            {{-- Sisi Kiri: Branding (hanya tampil di desktop ≥992px via CSS) --}}
            <div class="auth-split-left">
                <div class="auth-brand-logo">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h2 class="auth-brand-heading">{{ config('app.name', 'SIM-IMM') }}</h2>
                <p class="auth-brand-tagline">
                    Sistem Informasi Manajemen Keanggotaan &amp; Kearsipan Ikatan Mahasiswa Muhammadiyah
                </p>
                <div class="auth-features">
                    <div class="auth-feature-item">
                        <i class="bi bi-person-check"></i>
                        Kelola data keanggotaan dengan mudah
                    </div>
                    <div class="auth-feature-item">
                        <i class="bi bi-award"></i>
                        Sertifikat digital terverifikasi
                    </div>
                    <div class="auth-feature-item">
                        <i class="bi bi-calendar-event"></i>
                        Pantau kegiatan organisasi
                    </div>
                    <div class="auth-feature-item">
                        <i class="bi bi-phone"></i>
                        Tersedia sebagai aplikasi PWA
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan: Form auth --}}
            <div class="auth-split-right">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <div class="auth-brand-icon">
                            <i class="bi bi-shield-check fs-3"></i>
                        </div>
                        <h2 class="fw-bold text-primary">{{ config('app.name') }}</h2>
                        <p class="text-muted small">Sistem Informasi Manajemen Keanggotaan &amp; Kearsipan</p>
                    </div>

                    @yield('content')
                </div>
            </div>

        </div>
    </body>
</html>
