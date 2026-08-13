@php
    $daftarAktif = request()->routeIs(
        'admin.kegiatan.index',
        'admin.kegiatan.create',
        'admin.kegiatan.store',
        'admin.kegiatan.show',
        'admin.kegiatan.edit',
        'admin.kegiatan.update',
        'admin.kegiatan.destroy'
    );
@endphp

<nav class="nav nav-pills flex-nowrap overflow-auto gap-2 mb-4 pb-1" aria-label="Submenu kegiatan">
    <a class="nav-link text-nowrap {{ $daftarAktif ? 'active' : '' }}" href="{{ route('admin.kegiatan.index') }}">Daftar Kegiatan</a>
    <a class="nav-link text-nowrap {{ request()->routeIs('admin.presensi.*') ? 'active' : '' }}" href="{{ route('admin.presensi.index') }}">Rekap Presensi</a>
    <a class="nav-link text-nowrap {{ request()->routeIs('admin.laporan-kegiatan.*', 'admin.kegiatan.laporan-kegiatan.*') ? 'active' : '' }}" href="{{ route('admin.laporan-kegiatan.index') }}">Laporan Kegiatan</a>
    <a class="nav-link text-nowrap {{ request()->routeIs('admin.materi-kegiatan.*') ? 'active' : '' }}" href="{{ route('admin.materi-kegiatan.index') }}">Materi Kegiatan</a>
</nav>
