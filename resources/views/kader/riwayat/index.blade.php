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

    <div class="card border-0 shadow-sm p-3 mb-4" style="border-radius: 15px;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div>
                <h6 class="fw-bold mb-1">Kehadiran terkonfirmasi</h6>
                <small class="text-muted">{{ $jumlahKegiatanHadir }} dari {{ $minimumKegiatanHadir }} kegiatan hadir</small>
            </div>
            <i class="bi bi-patch-check fs-3 text-success"></i>
        </div>
    </div>

    <h6 class="fw-bold mb-3">Daftar Kehadiran</h6>
    <x-sort-control :action="route('kader.riwayat.index')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />
    <div class="row g-3 index-card-grid">
    @forelse($presensis as $p)
        <div class="col-12 col-sm-6">
        <div class="card h-100 p-3 border-0 shadow-sm index-card d-flex flex-column" style="border-radius: 15px;">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center min-w-0">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; min-width: 40px;">
                        <i class="bi bi-calendar-check text-primary"></i>
                    </div>
                    <div class="min-w-0">
                        <h6 class="mb-0 fw-bold text-break" style="font-size: 0.85rem;">{{ $p->kegiatan->nama_kegiatan }}</h6>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $p->kegiatan->tanggal_waktu->translatedFormat('d M Y') }}</small>
                    </div>
                </div>
                <span class="badge {{ $p->status_kehadiran === 'hadir' ? 'bg-success' : ($p->status_kehadiran === 'izin' ? 'bg-warning' : 'bg-danger') }} rounded-pill px-3" style="font-size: 0.65rem;">
                    {{ ucfirst($p->status_kehadiran) }}
                </span>
            </div>

            @php
                $sertifikat = $sertifikats->get($p->kegiatan_id);
            @endphp
            <div class="mt-3 pt-2 border-top d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 index-card__actions">
                <div class="small text-muted" style="font-size: 0.75rem;">
                    Sertifikat:
                    @if($sertifikat)
                        <span class="badge bg-success"><i class="bi bi-patch-check"></i> Tersedia</span>
                    @else
                        <span class="badge bg-secondary">Belum tersedia</span>
                    @endif
                </div>
                <div>
                    @if($sertifikat && $canClaimSertifikat && $p->status_kehadiran === 'hadir')
                        <a href="{{ route('kader.sertifikat.download', $sertifikat) }}" class="btn btn-sm btn-outline-success btn-ui btn-ui-sm px-3">
                            <i class="bi bi-download"></i> Unduh
                        </a>
                    @elseif(! $sertifikat && $canClaimSertifikat && $p->status_kehadiran === 'hadir')
                        <form action="{{ route('kader.sertifikat.klaim', $p) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary btn-ui btn-ui-sm px-3">
                                <i class="bi bi-award"></i> Klaim Sertifikat
                            </button>
                        </form>
                    @elseif(! $sertifikat && ! $canClaimSertifikat)
                        <span class="small text-muted">Klaim tersedia setelah {{ $minimumKegiatanHadir }} kegiatan hadir</span>
                    @elseif(! $sertifikat)
                        <span class="small text-muted">Klaim tidak tersedia untuk status {{ ucfirst($p->status_kehadiran) }}</span>
                    @elseif($sertifikat)
                        <span class="small text-muted">Unduh terkunci</span>
                    @endif
                </div>
            </div>
        </div>
        </div>

    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-clock-history display-4 text-muted opacity-25"></i>
            <p class="text-muted mt-2 small">Belum ada riwayat kegiatan.</p>
        </div>
    @endforelse
    </div>

    {{ $presensis->links('components.pagination') }}

    <div class="pb-3"></div>
</x-app-layout>
