<x-app-layout>
    <x-slot name="header">
        Dashboard Admin
    </x-slot>

    <x-sort-control :action="route('admin.dashboard')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />
    {{-- Area Statistik Grafik: hanya di desktop --}}
    <div class="d-none d-lg-block mb-4">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary btn-ui btn-ui-sm shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#chartCollapse" aria-expanded="false" aria-controls="chartCollapse">
                <i class="bi bi-graph-up me-1"></i> Tampilkan Grafik Statistik
            </button>
        </div>
        
        <div class="collapse" id="chartCollapse">
            <div class="row g-3 mb-4">
                <div class="col-lg-4">
                    <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-people-fill me-1 text-primary"></i> Anggota per Bulan</h6>
                        <div style="position: relative; height: 220px;">
                            <canvas id="anggotaChart" role="img" aria-label="Grafik pendaftaran anggota baru per bulan selama 12 bulan terakhir"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-calendar-event-fill me-1 text-success"></i> Kegiatan per Bulan</h6>
                        <div style="position: relative; height: 220px;">
                            <canvas id="kegiatanChart" role="img" aria-label="Grafik jumlah kegiatan per bulan selama 12 bulan terakhir"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card p-3 shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person-check-fill me-1 text-danger"></i> Kehadiran Anggota</h6>
                        <div style="position: relative; height: 220px;">
                            <canvas id="kehadiranChart" role="img" aria-label="Grafik jumlah kehadiran anggota per bulan selama 12 bulan terakhir"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik mobile: 2 kolom, hanya <992px --}}
    <div class="row g-3 mb-4 d-lg-none">
        <div class="col-6">
            <div class="card p-3 text-center h-100 stat-card-hover">
                <i class="bi bi-people text-primary fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['total_anggota'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Total Anggota</small>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center h-100 stat-card-hover">
                <i class="bi bi-calendar-event text-success fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['total_kegiatan'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Total Kegiatan</small>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center h-100 stat-card-hover">
                <i class="bi bi-person-plus text-warning fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['pendaftar_pending'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Pendaftar Baru</small>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center h-100 stat-card-hover">
                <i class="bi bi-file-earmark-pdf text-danger fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">{{ $stats['total_arsip'] }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Total Arsip</small>
            </div>
        </div>
    </div>

    {{-- Ringkasan statistik desktop: hanya ≥992px, tanpa mengubah grid mobile --}}
    <div class="row g-3 mb-4 d-none d-lg-flex desktop-stat-grid">
        <div class="col-lg-3">
            <div class="desktop-stat-card">
                <span class="desktop-stat-icon text-bg-primary-subtle"><i class="bi bi-people-fill text-primary"></i></span>
                <div class="desktop-stat-body">
                    <div class="desktop-stat-value">{{ $stats['total_anggota'] }}</div>
                    <div class="desktop-stat-label">Total Anggota</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="desktop-stat-card">
                <span class="desktop-stat-icon text-bg-success-subtle"><i class="bi bi-calendar-event-fill text-success"></i></span>
                <div class="desktop-stat-body">
                    <div class="desktop-stat-value">{{ $stats['total_kegiatan'] }}</div>
                    <div class="desktop-stat-label">Total Kegiatan</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="desktop-stat-card">
                <span class="desktop-stat-icon text-bg-warning-subtle"><i class="bi bi-person-plus-fill text-warning-emphasis"></i></span>
                <div class="desktop-stat-body">
                    <div class="desktop-stat-value">{{ $stats['pendaftar_pending'] }}</div>
                    <div class="desktop-stat-label">Pendaftar Baru</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="desktop-stat-card">
                <span class="desktop-stat-icon text-bg-danger-subtle"><i class="bi bi-file-earmark-pdf-fill text-danger"></i></span>
                <div class="desktop-stat-body">
                    <div class="desktop-stat-value">{{ $stats['total_arsip'] }}</div>
                    <div class="desktop-stat-label">Total Arsip</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Desktop: 2 kolom (Menu Cepat | Kegiatan Terdekat), Mobile: 1 kolom --}}
    <div class="row g-4">

        {{-- Menu Cepat --}}
        <div class="col-12 col-lg-5">
            <div class="desktop-section-heading"><i class="bi bi-lightning-charge"></i> Menu Cepat</div>
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
                <a href="{{ route('admin.laporan.index') }}"
                   class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-earmark-bar-graph me-2 text-info"></i> Laporan Bulanan</span>
                    <i class="bi bi-chevron-right small text-muted"></i>
                </a>
            </div>
        </div>

        {{-- Kegiatan Terdekat --}}
        <div class="col-12 col-lg-7">
            <div class="desktop-section-heading"><i class="bi bi-calendar3"></i> Kegiatan Terdekat</div>
            <div class="d-none d-lg-flex flex-column gap-2">
                @forelse($recent_kegiatans as $kegiatan)
                    <div class="desktop-kegiatan-item">
                        <div class="desktop-kegiatan-date">
                            <span class="desktop-kegiatan-date__day">{{ $kegiatan->tanggal_waktu->format('d') }}</span>
                            <span class="desktop-kegiatan-date__month">{{ $kegiatan->tanggal_waktu->translatedFormat('M') }}</span>
                        </div>
                        <div class="desktop-kegiatan-body">
                            <h6 class="desktop-kegiatan-title">{{ $kegiatan->nama_kegiatan }}</h6>
                            <div class="desktop-kegiatan-meta"><i class="bi bi-geo-alt me-1"></i> {{ $kegiatan->lokasi }}</div>
                        </div>
                        <span class="desktop-kegiatan-time">{{ $kegiatan->tanggal_waktu->format('H:i') }} WIB</span>
                    </div>
                @empty
                    <div class="desktop-panel p-4 text-center">
                        <p class="text-muted small mb-0">Belum ada kegiatan mendatang.</p>
                    </div>
                @endforelse
            </div>
            <div class="d-lg-none">
                @forelse($recent_kegiatans as $kegiatan)
                    <div class="card mb-2 p-3 kegiatan-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-3 text-center" style="min-width: 50px;">
                                <span class="d-block fw-bold text-primary">{{ $kegiatan->tanggal_waktu->format('d') }}</span>
                                <span class="small text-muted text-uppercase" style="font-size: 0.6rem;">{{ $kegiatan->tanggal_waktu->translatedFormat('M') }}</span>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="mb-0 fw-bold text-truncate">{{ $kegiatan->nama_kegiatan }}</h6>
                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $kegiatan->lokasi }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small text-center py-3">Belum ada kegiatan mendatang.</p>
                @endforelse
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const collapseEl = document.getElementById('chartCollapse');
            if (!collapseEl) return;

            let chartsInitialized = false;

            function createChart(ctxId, type, labels, data, datasetLabel, colors, options = {}) {
                const ctx = document.getElementById(ctxId);
                if (!ctx) return null;

                return new Chart(ctx.getContext('2d'), {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: datasetLabel,
                            data: data,
                            borderWidth: 1,
                            borderRadius: 4,
                            ...colors
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
                        },
                        ...options
                    }
                });
            }

            collapseEl.addEventListener('shown.bs.collapse', function () {
                if (chartsInitialized) return;

                const chartData = @json($chartData);

                // 1. Anggota per Bulan Chart
                createChart('anggotaChart', 'bar', chartData.anggota_per_bulan.labels, chartData.anggota_per_bulan.data, 'Anggota Baru', {
                    backgroundColor: '#800000', // Maroon
                    borderColor: '#800000'
                });

                // 2. Kegiatan per Bulan Chart
                createChart('kegiatanChart', 'bar', chartData.kegiatan_per_bulan.labels, chartData.kegiatan_per_bulan.data, 'Jumlah Kegiatan', {
                    backgroundColor: '#198754', // Success green
                    borderColor: '#198754'
                });

                // 3. Kehadiran Anggota Chart
                createChart('kehadiranChart', 'line', chartData.kehadiran_per_bulan.labels, chartData.kehadiran_per_bulan.data, 'Kehadiran', {
                    backgroundColor: 'rgba(220, 53, 69, 0.2)', // Danger red soft
                    borderColor: '#dc3545', // Danger red
                    borderWidth: 2
                }, {
                    tension: 0.3,
                    fill: true
                });

                chartsInitialized = true;
            });
        });
    </script>
</x-app-layout>
