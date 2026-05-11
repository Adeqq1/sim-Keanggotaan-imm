<x-app-layout>
    <x-slot name="header">
        Dashboard Admin
    </x-slot>

    <div class="row g-3">
        <div class="col-6">
            <div class="card p-3 text-center h-100">
                <i class="bi bi-people text-primary fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['total_anggota'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Total Anggota</small>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center h-100">
                <i class="bi bi-calendar-event text-success fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['total_kegiatan'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Total Kegiatan</small>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center h-100">
                <i class="bi bi-person-plus text-warning fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['pendaftar_pending'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Pendaftar Baru</small>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center h-100">
                <i class="bi bi-file-earmark-pdf text-danger fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['total_arsip'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Total Arsip</small>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h6 class="fw-bold mb-3">Menu Cepat</h6>
        <div class="list-group shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <a href="{{ route('admin.pendaftaran.index') }}" class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person-check me-2 text-primary"></i> Validasi Pendaftaran</span>
                <i class="bi bi-chevron-right small text-muted"></i>
            </a>
            <a href="{{ route('admin.sertifikat.create') }}" class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-patch-plus me-2 text-success"></i> Buat Sertifikat</span>
                <i class="bi bi-chevron-right small text-muted"></i>
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-bar-graph me-2 text-info"></i> Laporan Bulanan</span>
                <i class="bi bi-chevron-right small text-muted"></i>
            </a>
        </div>
    </div>

    <div class="mt-4 mb-3">
        <h6 class="fw-bold mb-3">Kegiatan Terdekat</h6>
        @forelse($recent_kegiatans as $kegiatan)
            <div class="card mb-2 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded p-2 me-3 text-center" style="min-width: 50px;">
                        <span class="d-block fw-bold text-primary">{{ $kegiatan->tanggal_waktu->format('d') }}</span>
                        <span class="small text-muted text-uppercase" style="font-size: 0.6rem;">{{ $kegiatan->tanggal_waktu->format('M') }}</span>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $kegiatan->nama_kegiatan }}</h6>
                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $kegiatan->lokasi }}</small>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted small text-center py-3">Belum ada kegiatan mendatang.</p>
        @endforelse
    </div>
</x-app-layout>
