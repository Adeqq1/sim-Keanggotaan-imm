<x-app-layout>
    <x-slot name="header">
        Dashboard Admin
    </x-slot>

    {{-- Statistik: mobile 2 kolom, desktop 4 kolom --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card p-3 text-center h-100 stat-card-hover">
                <i class="bi bi-people text-primary fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['total_anggota'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Total Anggota</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card p-3 text-center h-100 stat-card-hover">
                <i class="bi bi-calendar-event text-success fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['total_kegiatan'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Total Kegiatan</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card p-3 text-center h-100 stat-card-hover">
                <i class="bi bi-person-plus text-warning fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['pendaftar_pending'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Pendaftar Baru</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card p-3 text-center h-100 stat-card-hover">
                <i class="bi bi-file-earmark-pdf text-danger fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['total_arsip'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Total Arsip</small>
            </div>
        </div>
    </div>

    {{-- Desktop: 2 kolom (Menu Cepat | Kegiatan Terdekat), Mobile: 1 kolom --}}
    <div class="row g-4">

        {{-- Menu Cepat --}}
        <div class="col-12 col-lg-5">
            <h6 class="fw-bold mb-3">Menu Cepat</h6>
            <div class="list-group shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <a href="{{ route('admin.pendaftaran.index') }}"
                   class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person-check me-2 text-primary"></i> Validasi Pendaftaran</span>
                    <i class="bi bi-chevron-right small text-muted"></i>
                </a>
                <a href="{{ route('admin.sertifikat.create') }}"
                   class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-patch-plus me-2 text-success"></i> Buat Sertifikat</span>
                    <i class="bi bi-chevron-right small text-muted"></i>
                </a>
                <a href="{{ route('admin.sertifikat.verifikasi.index') }}"
                   class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-patch-check me-2 text-warning"></i> Verifikasi Sertifikat
                        @if($stats['sertifikat_pending'] > 0)
                            <span class="badge bg-danger ms-1" style="font-size: 0.65rem; padding: 0.25em 0.5em;">{{ $stats['sertifikat_pending'] }}</span>
                        @endif
                    </span>
                    <i class="bi bi-chevron-right small text-muted"></i>
                </a>
                <a href="{{ route('admin.laporan.index') }}"
                   class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-earmark-bar-graph me-2 text-info"></i> Laporan Bulanan</span>
                    <i class="bi bi-chevron-right small text-muted"></i>
                </a>
            </div>
        </div>

        {{-- Kegiatan Terdekat --}}
        <div class="col-12 col-lg-7">
            <h6 class="fw-bold mb-3">Kegiatan Terdekat</h6>
            @forelse($recent_kegiatans as $kegiatan)
                <div class="card mb-2 p-3 kegiatan-card">
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded p-2 me-3 text-center" style="min-width: 50px;">
                            <span class="d-block fw-bold text-primary">{{ $kegiatan->tanggal_waktu->format('d') }}</span>
                            <span class="small text-muted text-uppercase" style="font-size: 0.6rem;">{{ $kegiatan->tanggal_waktu->format('M') }}</span>
                        </div>
                        <div class="overflow-hidden">
                            <h6 class="mb-0 fw-bold text-truncate">{{ $kegiatan->nama_kegiatan }}</h6>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $kegiatan->lokasi }}</small>
                        </div>
                        <div class="ms-auto d-none d-lg-block">
                            <small class="text-muted">{{ $kegiatan->tanggal_waktu->format('H:i') }} WIB</small>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted small text-center py-3">Belum ada kegiatan mendatang.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>
