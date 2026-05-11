<nav class="navbar fixed-bottom bg-white shadow-lg border-top p-0">
    <div class="container-fluid d-flex justify-content-around">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-center py-2 {{ request()->routeIs('admin.dashboard') ? 'text-primary font-weight-bold' : 'text-muted' }}">
                <i class="bi bi-speedometer2 fs-4 d-block"></i>
                <small style="font-size: 0.7rem;">Dashboard</small>
            </a>
            <a href="{{ route('admin.pendaftaran.index') }}" class="nav-link text-center py-2 {{ request()->routeIs('admin.pendaftaran.*') ? 'text-primary font-weight-bold' : 'text-muted' }}">
                <i class="bi bi-person-plus fs-4 d-block"></i>
                <small style="font-size: 0.7rem;">Pendaftar</small>
            </a>
            <a href="{{ route('admin.anggota.index') }}" class="nav-link text-center py-2 {{ request()->routeIs('admin.anggota.*') ? 'text-primary font-weight-bold' : 'text-muted' }}">
                <i class="bi bi-people fs-4 d-block"></i>
                <small style="font-size: 0.7rem;">Anggota</small>
            </a>
            <a href="{{ route('admin.kegiatan.index') }}" class="nav-link text-center py-2 {{ request()->routeIs('admin.kegiatan.*') ? 'text-primary font-weight-bold' : 'text-muted' }}">
                <i class="bi bi-calendar-event fs-4 d-block"></i>
                <small style="font-size: 0.7rem;">Kegiatan</small>
            </a>
        @else
            <a href="{{ route('kader.dashboard') }}" class="nav-link text-center py-2 {{ request()->routeIs('kader.dashboard') ? 'text-primary font-weight-bold' : 'text-muted' }}">
                <i class="bi bi-house fs-4 d-block"></i>
                <small style="font-size: 0.7rem;">Home</small>
            </a>
            <a href="{{ route('kader.ekta') }}" class="nav-link text-center py-2 {{ request()->routeIs('kader.ekta') ? 'text-primary font-weight-bold' : 'text-muted' }}">
                <i class="bi bi-card-text fs-4 d-block"></i>
                <small style="font-size: 0.7rem;">E-KTA</small>
            </a>
            <a href="{{ route('kader.sertifikat.index') }}" class="nav-link text-center py-2 {{ request()->routeIs('kader.sertifikat.*') ? 'text-primary font-weight-bold' : 'text-muted' }}">
                <i class="bi bi-patch-check fs-4 d-block"></i>
                <small style="font-size: 0.7rem;">Sertifikat</small>
            </a>
            <a href="{{ route('kader.riwayat.index') }}" class="nav-link text-center py-2 {{ request()->routeIs('kader.riwayat.*') ? 'text-primary font-weight-bold' : 'text-muted' }}">
                <i class="bi bi-clock-history fs-4 d-block"></i>
                <small style="font-size: 0.7rem;">Riwayat</small>
            </a>
        @endif
        <a href="{{ route('profile.edit') }}" class="nav-link text-center py-2 {{ request()->routeIs('profile.edit') ? 'text-primary font-weight-bold' : 'text-muted' }}">
            <i class="bi bi-person-circle fs-4 d-block"></i>
            <small style="font-size: 0.7rem;">Profil</small>
        </a>
    </div>
</nav>
