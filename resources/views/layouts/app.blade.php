<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIM-IMM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
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
                font-family: 'Inter', sans-serif;
                background-color: #f8f9fa;
                padding-bottom: 80px; /* Space for Bottom Nav */
            }
            .navbar-header {
                background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
                color: white;
            }
            .card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            .btn-primary {
                background-color: #3b82f6;
                border-color: #3b82f6;
                border-radius: 8px;
                padding: 10px 20px;
                font-weight: 600;
            }
            .btn-primary:hover {
                background-color: #2563eb;
                border-color: #2563eb;
            }
        </style>
    </head>
    <body class="antialiased">
        <!-- Header -->
        <header class="navbar-header py-3 px-3 shadow-sm sticky-top">
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

        <!-- Content -->
        <main class="container py-4">
            {{ $slot }}
        </main>

        <!-- Bottom Navigation -->
        <x-bottom-nav />

        <!-- Alerts -->
        <x-_alert />
    </body>
</html>
