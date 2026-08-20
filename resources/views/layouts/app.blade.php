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
        @php
            $user = auth()->user();
            $profilePhoto = $user->profile_photo_url;
        @endphp

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
                       class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.pendaftaran.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.pendaftaran.*')) aria-current="page" @endif>
                        <i class="bi bi-person-plus"></i>
                        Pendaftaran
                    </a>
                    <a href="{{ route('admin.anggota.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.anggota.*')) aria-current="page" @endif>
                        <i class="bi bi-people"></i>
                        Anggota
                    </a>
                    <p class="sidebar-section-label">Kegiatan</p>
                    <a href="{{ route('admin.kegiatan.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.kegiatan.index', 'admin.kegiatan.create', 'admin.kegiatan.store', 'admin.kegiatan.show', 'admin.kegiatan.edit', 'admin.kegiatan.update', 'admin.kegiatan.destroy') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.kegiatan.index', 'admin.kegiatan.create', 'admin.kegiatan.store', 'admin.kegiatan.show', 'admin.kegiatan.edit', 'admin.kegiatan.update', 'admin.kegiatan.destroy')) aria-current="page" @endif>
                        <i class="bi bi-calendar-event"></i>
                        Daftar Kegiatan
                    </a>
                    <a href="{{ route('admin.presensi.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.presensi.*') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.presensi.*')) aria-current="page" @endif>
                        <i class="bi bi-check2-square"></i>
                        Rekap Presensi
                    </a>
                    <a href="{{ route('admin.laporan-kegiatan.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.laporan-kegiatan.*', 'admin.kegiatan.laporan-kegiatan.*') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.laporan-kegiatan.*', 'admin.kegiatan.laporan-kegiatan.*')) aria-current="page" @endif>
                        <i class="bi bi-file-earmark-text"></i>
                        Laporan Kegiatan
                    </a>
                    <a href="{{ route('admin.materi-kegiatan.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.materi-kegiatan.*') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.materi-kegiatan.*')) aria-current="page" @endif>
                        <i class="bi bi-journal-text"></i>
                        Materi Kegiatan
                    </a>

                    <p class="sidebar-section-label">Manajemen</p>
                    <a href="{{ route('admin.sertifikat.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.sertifikat.index') || request()->routeIs('admin.sertifikat.create') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.sertifikat.index') || request()->routeIs('admin.sertifikat.create')) aria-current="page" @endif>
                        <i class="bi bi-patch-plus"></i>
                        Sertifikat
                    </a>
                    <a href="{{ route('admin.arsip.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.arsip.*') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.arsip.*')) aria-current="page" @endif>
                        <i class="bi bi-folder2-open"></i>
                        E-Arsip
                    </a>
                    <a href="{{ route('admin.laporan.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.laporan.*')) aria-current="page" @endif>
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        Laporan Sistem
                    </a>

                @elseif(auth()->user()->role === 'instruktur')
                    <p class="sidebar-section-label">Menu Instruktur</p>
                    <a href="{{ route('admin.kegiatan.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.kegiatan.*', 'admin.presensi.*') ? 'active' : '' }}"
                       @if(request()->routeIs('admin.kegiatan.*', 'admin.presensi.*')) aria-current="page" @endif>
                        <i class="bi bi-calendar-event"></i>
                        Kegiatan
                    </a>
                    <nav class="sidebar-submenu" aria-label="Navigasi kegiatan">
                        <a href="{{ route('admin.kegiatan.index') }}"
                           class="sidebar-sublink {{ request()->routeIs('admin.kegiatan.index', 'admin.kegiatan.show', 'admin.kegiatan.edit', 'admin.kegiatan.update', 'admin.kegiatan.destroy') ? 'active' : '' }}"
                           @if(request()->routeIs('admin.kegiatan.index', 'admin.kegiatan.show', 'admin.kegiatan.edit', 'admin.kegiatan.update', 'admin.kegiatan.destroy')) aria-current="page" @endif>
                            <i class="bi bi-list-ul"></i>
                            Daftar Kegiatan
                        </a>
                        <a href="{{ route('admin.kegiatan.create') }}"
                           class="sidebar-sublink {{ request()->routeIs('admin.kegiatan.create', 'admin.kegiatan.store') ? 'active' : '' }}"
                           @if(request()->routeIs('admin.kegiatan.create', 'admin.kegiatan.store')) aria-current="page" @endif>
                            <i class="bi bi-plus-circle"></i>
                            Buat Kegiatan Baru
                        </a>
                        <a href="{{ route('admin.presensi.index') }}"
                           class="sidebar-sublink {{ request()->routeIs('admin.presensi.*') ? 'active' : '' }}"
                           @if(request()->routeIs('admin.presensi.*')) aria-current="page" @endif>
                            <i class="bi bi-check2-square"></i>
                            Rekap Presensi
                        </a>
                    </nav>

                @else
                    <p class="sidebar-section-label">Menu Kader</p>
                    <a href="{{ route('kader.dashboard') }}"
                       class="sidebar-link {{ request()->routeIs('kader.dashboard') ? 'active' : '' }}"
                       @if(request()->routeIs('kader.dashboard')) aria-current="page" @endif>
                        <i class="bi bi-house"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('kader.ekta') }}"
                       class="sidebar-link {{ request()->routeIs('kader.ekta') ? 'active' : '' }}"
                       @if(request()->routeIs('kader.ekta')) aria-current="page" @endif>
                        <i class="bi bi-person-vcard"></i>
                        E-KTA Digital
                    </a>
                    <a href="{{ route('kader.sertifikat.index') }}"
                       class="sidebar-link {{ request()->routeIs('kader.sertifikat.*') ? 'active' : '' }}"
                       @if(request()->routeIs('kader.sertifikat.*')) aria-current="page" @endif>
                        <i class="bi bi-award"></i>
                        E-Sertifikat
                    </a>
                    <a href="{{ route('kader.arsip.index') }}"
                       class="sidebar-link {{ request()->routeIs('kader.arsip.*') ? 'active' : '' }}"
                       @if(request()->routeIs('kader.arsip.*')) aria-current="page" @endif>
                        <i class="bi bi-folder2-open"></i>
                        E-Arsip
                    </a>
                    <a href="{{ route('kader.materi.index') }}"
                       class="sidebar-link {{ request()->routeIs('kader.materi.*') ? 'active' : '' }}"
                       @if(request()->routeIs('kader.materi.*')) aria-current="page" @endif>
                        <i class="bi bi-journal-text"></i>
                        Materi
                    </a>
                    <a href="{{ route('kader.riwayat.index') }}"
                       class="sidebar-link {{ request()->routeIs('kader.riwayat.*') ? 'active' : '' }}"
                       @if(request()->routeIs('kader.riwayat.*')) aria-current="page" @endif>
                        <i class="bi bi-clock-history"></i>
                        Riwayat
                    </a>
                @endif

                <p class="sidebar-section-label">Akun</p>
                <a href="{{ route('profile.edit') }}"
                   class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                   @if(request()->routeIs('profile.edit')) aria-current="page" @endif>
                    <i class="bi bi-person-circle"></i>
                    Profil Saya
                </a>
            </nav>

            {{-- Footer user info --}}
            <div class="sidebar-footer">
                <div class="d-flex align-items-center gap-2">
                    <div class="sidebar-user-avatar d-flex align-items-center justify-content-center overflow-hidden">
                        @if($profilePhoto)
                            <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" width="36" height="36" style="object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                            <span style="display: none;">{{ $user->initials }}</span>
                        @else
                            {{ $user->initials }}
                        @endif
                    </div>
                    <div class="overflow-hidden flex-1">
                        <p class="sidebar-user-name">{{ $user->name }}</p>
                        <p class="sidebar-user-role">{{ $user->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 text-white opacity-50"
                                title="Keluar" aria-label="Keluar" style="font-size: 1.1rem;">
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
                        <button class="btn btn-link text-white p-0 d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Buka menu pengguna">
                            @if($profilePhoto)
                                <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" width="28" height="28" class="rounded-circle me-1" style="object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                <span class="rounded-circle bg-white bg-opacity-25 text-white fw-bold align-items-center justify-content-center me-1" style="display: none; width: 28px; height: 28px; font-size: 0.7rem;">{{ $user->initials }}</span>
                            @else
                                <span class="rounded-circle bg-white bg-opacity-25 text-white fw-bold d-inline-flex align-items-center justify-content-center me-1" style="width: 28px; height: 28px; font-size: 0.7rem;">{{ $user->initials }}</span>
                            @endif
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
                        <div class="user-avatar-sm">
                            @if($profilePhoto)
                                <img src="{{ $profilePhoto }}" alt="{{ $user->name }}" width="28" height="28" class="rounded-circle" style="object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                <span style="display: none;">{{ $user->initials }}</span>
                            @else
                                {{ $user->initials }}
                            @endif
                        </div>
                        <span>{{ $user->name }}</span>
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
