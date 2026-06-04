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

        <style>
            body {
                font-family: 'Inter', sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(245, 158, 11, 0.18), transparent 32%),
                    linear-gradient(135deg, #800000 0%, #a00000 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            .auth-card {
                background: white;
                border: 1px solid rgba(128, 0, 0, 0.08);
                border-radius: 18px;
                box-shadow: 0 18px 45px rgba(96, 0, 0, 0.25);
                width: 100%;
                max-width: 400px;
                padding: 30px;
            }
            .auth-brand-icon {
                align-items: center;
                background: linear-gradient(135deg, #800000 0%, #a00000 100%);
                border-radius: 16px;
                box-shadow: 0 10px 24px rgba(128, 0, 0, 0.28);
                color: white;
                display: inline-flex;
                height: 56px;
                justify-content: center;
                margin-bottom: 0.75rem;
                width: 56px;
            }
            .auth-card a {
                color: #800000;
            }
            .auth-card a:hover {
                color: #a00000;
            }
            .form-control:focus,
            .form-check-input:focus {
                border-color: rgba(128, 0, 0, 0.35);
                box-shadow: 0 0 0 0.25rem rgba(128, 0, 0, 0.12);
            }
            .form-check-input:checked {
                background-color: #800000;
                border-color: #800000;
            }
            .btn-primary {
                background-color: #800000;
                border-color: #800000;
                border-radius: 8px;
                padding: 12px;
                font-weight: 600;
            }
            .btn-primary:hover,
            .btn-primary:focus {
                background-color: #600000;
                border-color: #600000;
            }
        </style>
    </head>
    <body>
        <div class="auth-card mx-3">
            <div class="text-center mb-4">
                <div class="auth-brand-icon">
                    <i class="bi bi-shield-check fs-3"></i>
                </div>
                <h2 class="fw-bold text-primary">{{ config('app.name') }}</h2>
                <p class="text-muted small">Sistem Informasi Manajemen Keanggotaan & Kearsipan</p>
            </div>

            @yield('content')
        </div>
    </body>
</html>
