<?php

/**
 * Konfigurasi konten Landing Page IMM.
 * Edit file ini untuk mengubah teks, angka, dan link tanpa menyentuh template Blade.
 *
 * TODO: Verifikasi semua data di bawah dengan PM / pengurus DPP IMM sebelum go-live.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Statistik Organisasi
    |--------------------------------------------------------------------------
    | TODO: Ganti dengan angka real dari data keanggotaan IMM.
    */
    'stats' => [
        ['value' => '1000+', 'label' => 'Anggota Aktif'],
        ['value' => '50+',   'label' => 'Komisariat'],
        ['value' => '100+',  'label' => 'Kegiatan / Tahun'],
        ['value' => '60+',   'label' => 'Tahun Berkarya'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Program Kerja Unggulan
    |--------------------------------------------------------------------------
    */
    'programs' => [
        [
            'image' => '/images/landing/program-1.jpg',
            'alt' => 'Kajian rutin IMM',
            'icon' => 'bi-journal-text',
            'icon_bg' => '#fef7f7',
            'icon_color' => 'text-primary',
            'title' => 'Kajian Rutin',
            'description' => 'Forum diskusi dan kajian keislaman yang diselenggarakan secara rutin untuk memperdalam pemahaman agama dan isu-isu kontemporer.',
        ],
        [
            'image' => '/images/landing/program-2.jpg',
            'alt' => 'Pengabdian masyarakat IMM',
            'icon' => 'bi-heart',
            'icon_bg' => '#f0fdf4',
            'icon_color' => 'text-success',
            'title' => 'Pengabdian Masyarakat',
            'description' => 'Kegiatan sosial dan bakti masyarakat sebagai wujud nyata kepedulian IMM terhadap lingkungan sekitar dan masyarakat yang membutuhkan.',
        ],
        [
            'image' => '/images/landing/program-3.jpg',
            'alt' => 'Pelatihan kader IMM',
            'icon' => 'bi-mortarboard',
            'icon_bg' => '#fef3c7',
            'icon_color' => 'text-warning',
            'title' => 'Pelatihan Kader',
            'description' => 'Program kaderisasi berjenjang mulai dari Darul Arqam Dasar hingga Darul Arqam Madya untuk membentuk pemimpin yang tangguh dan berintegritas.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pilar Organisasi (Section Tentang)
    |--------------------------------------------------------------------------
    */
    'pillars' => [
        ['icon' => 'bi-book-half',  'color' => 'text-primary', 'bg' => '#fef7f7', 'title' => 'Religiusitas',    'desc' => 'Iman & akhlak mulia'],
        ['icon' => 'bi-lightbulb',  'color' => 'text-success', 'bg' => '#f0fdf4', 'title' => 'Intelektualitas', 'desc' => 'Ilmu & wawasan luas'],
        ['icon' => 'bi-people',     'color' => 'text-warning', 'bg' => '#fef3c7', 'title' => 'Humanitas',       'desc' => 'Peduli sesama'],
        ['icon' => 'bi-award',      'color' => 'text-danger',  'bg' => '#fdf2f8', 'title' => 'Kaderisasi',      'desc' => 'Pemimpin masa depan'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Misi Organisasi
    |--------------------------------------------------------------------------
    | TODO: Sesuaikan dengan redaksi resmi AD/ART IMM.
    */
    'misi' => [
        ['icon' => 'bi-book',          'text' => 'Membina dan mengembangkan kemampuan akademik mahasiswa yang berlandaskan nilai-nilai Islam.'],
        ['icon' => 'bi-people',        'text' => 'Membentuk kader yang memiliki kepribadian muslim, cakap, dan percaya diri.'],
        ['icon' => 'bi-globe',         'text' => 'Mengembangkan potensi kreatif, keilmuan, sosial, dan budaya mahasiswa.'],
        ['icon' => 'bi-shield-check',  'text' => 'Memperkuat ukhuwah islamiyah dan solidaritas antar mahasiswa Muslim.'],
        ['icon' => 'bi-arrow-up-circle', 'text' => 'Berperan aktif dalam pembangunan masyarakat yang adil, makmur, dan beradab.'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Kontak & Media Sosial
    |--------------------------------------------------------------------------
    | TODO: Verifikasi alamat, email, dan URL sosmed resmi DPP IMM.
    | Kosongkan 'url' => '' untuk menyembunyikan ikon sosmed yang belum ada.
    */
    'contact' => [
        'address' => 'Kompleks Islamic Center, Jl. Rang Kayo Hitam, Cadika, Rimbo Tengah, Kab.Bungo, Prov.Jambi', // TODO: konfirmasi alamat resmi
        'email' => 'pcimmbungo64@gmail.com',                                // TODO: konfirmasi email resmi
        'website' => 'https://imm.or.id',
    ],

    'social_links' => [
        ['icon' => 'bi-instagram',  'label' => 'Instagram IMM', 'url' => ''], // TODO: isi URL akun resmi
        ['icon' => 'bi-twitter-x',  'label' => 'Twitter IMM',   'url' => ''], // TODO: isi URL akun resmi
        ['icon' => 'bi-youtube',    'label' => 'YouTube IMM',   'url' => ''], // TODO: isi URL akun resmi
        ['icon' => 'bi-facebook',   'label' => 'Facebook IMM',  'url' => ''], // TODO: isi URL akun resmi
    ],

];
