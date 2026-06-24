<x-app-layout>
    <x-slot name="header">
        Dashboard Admin
    </x-slot>

    {{-- Area Statistik Grafik: hanya di desktop --}}
    <div class="d-none d-lg-block mb-4">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary btn-sm shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#chartCollapse" aria-expanded="false" aria-controls="chartCollapse">
                <i class="bi bi-graph-up me-1"></i> Tampilkan Grafik Statistik
            </button>
        </div>
        
        <div class="collapse" id="chartCollapse">
            <div class="row g-3 mb-4">
                <div class="col-lg-4">
                    <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-people-fill me-1 text-primary"></i> Anggota per Bulan</h6>
                        <div style="position: relative; height: 220px;">
                            <canvas id="anggotaChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-calendar-event-fill me-1 text-success"></i> Kegiatan per Bulan</h6>
                        <div style="position: relative; height: 220px;">
                            <canvas id="kegiatanChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person-check-fill me-1 text-danger"></i> Kehadiran Anggota</h6>
                        <div style="position: relative; height: 220px;">
                            <canvas id="kehadiranChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const collapseEl = document.getElementById('chartCollapse');
            let chartsInitialized = false;

            collapseEl.addEventListener('shown.bs.collapse', function () {
                if (chartsInitialized) return;

                const chartData = @json($chartData);

                // 1. Anggota per Bulan Chart
                const ctxAnggota = document.getElementById('anggotaChart').getContext('2d');
                new Chart(ctxAnggota, {
                    type: 'bar',
                    data: {
                        labels: chartData.anggota_per_bulan.labels,
                        datasets: [{
                            label: 'Anggota Baru',
                            data: chartData.anggota_per_bulan.data,
                            backgroundColor: '#800000', // Maroon
                            borderColor: '#800000',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });

                // 2. Kegiatan per Bulan Chart
                const ctxKegiatan = document.getElementById('kegiatanChart').getContext('2d');
                new Chart(ctxKegiatan, {
                    type: 'bar',
                    data: {
                        labels: chartData.kegiatan_per_bulan.labels,
                        datasets: [{
                            label: 'Jumlah Kegiatan',
                            data: chartData.kegiatan_per_bulan.data,
                            backgroundColor: '#198754', // Success green
                            borderColor: '#198754',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });

                // 3. Kehadiran Anggota Chart
                const ctxKehadiran = document.getElementById('kehadiranChart').getContext('2d');
                new Chart(ctxKehadiran, {
                    type: 'line',
                    data: {
                        labels: chartData.kehadiran_per_bulan.labels,
                        datasets: [{
                            label: 'Kehadiran',
                            data: chartData.kehadiran_per_bulan.data,
                            backgroundColor: 'rgba(220, 53, 69, 0.2)', // Danger red soft
                            borderColor: '#dc3545', // Danger red
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });

                chartsInitialized = true;
            });
        });
    </script>
</x-app-layout>
