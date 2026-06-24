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
    <body class="antialiased">

        {{-- ============================================================
             SIDEBAR DESKTOP (hanya muncul di layar ≥992px via CSS)
             ============================================================ --}}
        <aside class="sidebar-desktop d-none d-lg-flex">
            {{-- Brand --}}
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <p class="sidebar-brand-title">{{ config('app.name', 'SIM-IMM') }}</p>
                <p class="sidebar-brand-subtitle">Sistem Informasi Keanggotaan</p>
            </div>

            {{-- Navigasi sesuai role --}}
            <nav class="sidebar-nav">
                @if(auth()->user()->role === 'admin')
                    <p class="sidebar-section-label">Menu Admin</p>
                    <a href="{{ route('admin.dashboard') }}"
                       class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.pendaftaran.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}">
                        <i class="bi bi-person-plus"></i>
                        Pendaftaran
                    </a>
                    <a href="{{ route('admin.anggota.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        Anggota
                    </a>
                    <a href="{{ route('admin.kegiatan.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.kegiatan.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i>
                        Kegiatan
                    </a>

                    <p class="sidebar-section-label">Manajemen</p>
                    <a href="{{ route('admin.sertifikat.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.sertifikat.index') || request()->routeIs('admin.sertifikat.create') ? 'active' : '' }}">
                        <i class="bi bi-patch-plus"></i>
                        Sertifikat
                    </a>
                    @php
                        $sertifikatPending = \App\Models\Presensi::where('status_klaim', 'pending')->count();
                    @endphp
                    <a href="{{ route('admin.sertifikat.verifikasi.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.sertifikat.verifikasi.*') ? 'active' : '' }}">
                        <i class="bi bi-patch-check"></i>
                        Verifikasi
                        @if($sertifikatPending > 0)
                            <span class="sidebar-badge">{{ $sertifikatPending }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.laporan.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        Laporan
                    </a>

                @elseif(auth()->user()->role === 'instruktur')
                    <p class="sidebar-section-label">Menu Instruktur</p>
                    <a href="{{ route('admin.kegiatan.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.kegiatan.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i>
                        Kegiatan
                    </a>
                    @php
                        $pendingCount = \App\Models\Presensi::where('status_klaim', 'pending')->count();
                    @endphp
                    <a href="{{ route('admin.sertifikat.verifikasi.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.sertifikat.verifikasi.*') ? 'active' : '' }}">
                        <i class="bi bi-patch-check"></i>
                        Verifikasi
                        @if($pendingCount > 0)
                            <span class="sidebar-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>

                @else
                    <p class="sidebar-section-label">Menu Kader</p>
                    <a href="{{ route('kader.dashboard') }}"
                       class="sidebar-link {{ request()->routeIs('kader.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('kader.ekta') }}"
                       class="sidebar-link {{ request()->routeIs('kader.ekta') ? 'active' : '' }}">
                        <i class="bi bi-person-vcard"></i>
                        E-KTA Digital
                    </a>
                    <a href="{{ route('kader.sertifikat.index') }}"
                       class="sidebar-link {{ request()->routeIs('kader.sertifikat.*') ? 'active' : '' }}">
                        <i class="bi bi-award"></i>
                        E-Sertifikat
                    </a>
                    <a href="{{ route('kader.riwayat.index') }}"
                       class="sidebar-link {{ request()->routeIs('kader.riwayat.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        Riwayat
                    </a>
                @endif

                <p class="sidebar-section-label">Akun</p>
                <a href="{{ route('profile.edit') }}"
                   class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    Profil Saya
                </a>
            </nav>

            {{-- Footer user info --}}
            <div class="sidebar-footer">
                <div class="d-flex align-items-center gap-2">
                    <div class="sidebar-user-avatar">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden flex-1">
                        <p class="sidebar-user-name">{{ auth()->user()->name }}</p>
                        <p class="sidebar-user-role">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 text-white opacity-50"
                                title="Keluar" style="font-size: 1.1rem;">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ============================================================
             APP WRAPPER — bergeser ke kanan saat sidebar aktif (desktop)
             ============================================================ --}}
        <div class="app-wrapper">

            {{-- Header Mobile (tersembunyi di desktop) --}}
            <header class="navbar-header py-3 px-3 shadow-sm sticky-top d-lg-none">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h5 mb-0 fw-bold">{{ $header ?? config('app.name') }}</h1>
                    <div class="dropdown">
                        <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 text-primary"></i> Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            {{-- Desktop Topbar (tersembunyi di mobile) --}}
            <div class="desktop-topbar d-none d-lg-flex">
                <h1 class="desktop-topbar-title">{{ $header ?? config('app.name') }}</h1>
                <div class="dropdown">
                    <button class="user-menu-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar-sm">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <span>{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; min-width: 180px;">
                        <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 text-primary"></i> Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Content --}}
            <main class="app-main-content">
                {{ $slot }}
            </main>

            {{-- Bottom Navigation (komponen sudah punya d-lg-none) --}}
            <x-bottom-nav />

            {{-- Alerts --}}
            <x-_alert />
        </div>
    </body>
</html>
