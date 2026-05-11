<x-app-layout>
    <x-slot name="header">
        Riwayat Keaktifan
    </x-slot>

    <!-- Stat Cards -->
    <div class="row g-2 mb-4">
        <div class="col-4">
            <div class="card p-2 text-center border-0 shadow-sm bg-success bg-opacity-10" style="border-radius: 12px;">
                <h4 class="fw-bold mb-0 text-success">{{ $stats['hadir'] }}</h4>
                <small class="text-success fw-bold" style="font-size: 0.6rem;">HADIR</small>
            </div>
        </div>
        <div class="col-4">
            <div class="card p-2 text-center border-0 shadow-sm bg-warning bg-opacity-10" style="border-radius: 12px;">
                <h4 class="fw-bold mb-0 text-warning">{{ $stats['izin'] }}</h4>
                <small class="text-warning fw-bold" style="font-size: 0.6rem;">IZIN</small>
            </div>
        </div>
        <div class="col-4">
            <div class="card p-2 text-center border-0 shadow-sm bg-danger bg-opacity-10" style="border-radius: 12px;">
                <h4 class="fw-bold mb-0 text-danger">{{ $stats['alfa'] }}</h4>
                <small class="text-danger fw-bold" style="font-size: 0.6rem;">ALFA</small>
            </div>
        </div>
    </div>

    <h6 class="fw-bold mb-3">Daftar Kehadiran</h6>
    @forelse($presensis as $p)
        <div class="card mb-2 p-3 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center overflow-hidden">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; min-width: 40px;">
                        <i class="bi bi-calendar-check text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <h6 class="mb-0 fw-bold text-truncate" style="font-size: 0.85rem;">{{ $p->kegiatan->nama_kegiatan }}</h6>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $p->kegiatan->tanggal_waktu->format('d M Y') }}</small>
                    </div>
                </div>
                <span class="badge {{ $p->status_kehadiran === 'hadir' ? 'bg-success' : ($p->status_kehadiran === 'izin' ? 'bg-warning' : 'bg-danger') }} rounded-pill px-3" style="font-size: 0.65rem;">
                    {{ ucfirst($p->status_kehadiran) }}
                </span>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-clock-history display-4 text-muted opacity-25"></i>
            <p class="text-muted mt-2 small">Belum ada riwayat kegiatan.</p>
        </div>
    @endforelse

    <div class="pb-3"></div>
</x-app-layout>
