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
                background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .auth-card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                width: 100%;
                max-width: 400px;
                padding: 30px;
            }
            .btn-primary {
                background-color: #3b82f6;
                border-color: #3b82f6;
                border-radius: 8px;
                padding: 12px;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="auth-card mx-3">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">{{ config('app.name') }}</h2>
                <p class="text-muted small">Sistem Informasi Manajemen Keanggotaan & Kearsipan</p>
            </div>

            @yield('content')
        </div>
    </body>
</html>
