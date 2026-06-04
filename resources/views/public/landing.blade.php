<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>IMM | Ikatan Mahasiswa Muhammadiyah</title>

    {{-- SEO Meta --}}
    <meta name="description" content="Profil resmi Ikatan Mahasiswa Muhammadiyah (IMM). Bergabung, berkarya, dan berkembang bersama.">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url('/') }}">
    <meta property="og:title"       content="IMM | Ikatan Mahasiswa Muhammadiyah">
    <meta property="og:description" content="Profil resmi Ikatan Mahasiswa Muhammadiyah (IMM). Bergabung, berkarya, dan berkembang bersama.">
    <meta property="og:image"       content="{{ asset('images/landing/hero.jpg') }}">
    <meta property="og:locale"      content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="IMM | Ikatan Mahasiswa Muhammadiyah">
    <meta name="twitter:description" content="Profil resmi Ikatan Mahasiswa Muhammadiyah (IMM). Bergabung, berkarya, dan berkembang bersama.">
    <meta name="twitter:image"       content="{{ asset('images/landing/hero.jpg') }}">

    {{-- JSON-LD Organization Schema --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "Ikatan Mahasiswa Muhammadiyah",
        "alternateName": "IMM",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/landing/hero.jpg') }}",
        "foundingDate": "1964-03-14",
        "description": "Organisasi otonom Muhammadiyah yang bergerak di bidang kemahasiswaan.",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "{{ config('landing.contact.address') }}",
            "addressCountry": "ID"
        },
        "email": "{{ config('landing.contact.email') }}",
        "sameAs": [
            "{{ config('landing.contact.website') }}"
        ]
    }
    </script>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.js'])

    {{-- PWA --}}
    <meta name="theme-color" content="#800000">
    <link rel="manifest" href="/manifest.json">
    @verbatim
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
    @endverbatim
</head>
<body style="font-family:'Inter',sans-serif;">

    @include('public.partials._navbar')
    @include('public.partials._hero')
    @include('public.partials._stats')
    @include('public.partials._about')
    @include('public.partials._program')
    @include('public.partials._visi-misi')
    @include('public.partials._cta')
    @include('public.partials._footer')

</body>
</html>
