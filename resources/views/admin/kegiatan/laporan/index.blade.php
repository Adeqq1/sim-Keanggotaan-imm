<x-app-layout>
    <x-slot name="header">Laporan Kegiatan</x-slot>
    <x-kegiatan-submenu />

    <div class="row g-3 index-card-grid">
        @forelse($kegiatans as $kegiatan)
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 p-3">
                    <div class="d-flex justify-content-between gap-2 mb-2">
                        <h6 class="fw-bold mb-0">{{ $kegiatan->nama_kegiatan }}</h6>
                        <span class="badge {{ $kegiatan->laporanKegiatan ? 'bg-success' : 'bg-secondary' }}">{{ $kegiatan->laporanKegiatan ? 'Sudah dibuat' : 'Belum dibuat' }}</span>
                    </div>
                    <p class="small text-muted">{{ $kegiatan->tanggal_waktu->translatedFormat('d F Y, H:i') }} · {{ $kegiatan->lokasi }}</p>
                    <div class="d-flex gap-3 small mb-3">
                        <span>Peserta <strong>{{ $kegiatan->presensi_count }}</strong></span>
                        <span>Hadir <strong>{{ $kegiatan->hadir_count }}</strong></span>
                        <span>Izin <strong>{{ $kegiatan->izin_count }}</strong></span>
                        <span>Alfa <strong>{{ $kegiatan->alfa_count }}</strong></span>
                    </div>
                    <div class="d-flex gap-2 mt-auto">
                        @if($kegiatan->laporanKegiatan)
                            <a href="{{ route('admin.laporan-kegiatan.show', $kegiatan->laporanKegiatan) }}" class="btn btn-primary btn-ui btn-ui-sm">Lihat</a>
                            <a href="{{ route('admin.laporan-kegiatan.edit', $kegiatan->laporanKegiatan) }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">Ubah</a>
                        @else
                            <a href="{{ route('admin.kegiatan.laporan-kegiatan.create', $kegiatan) }}" class="btn btn-primary btn-ui btn-ui-sm">Buat Laporan</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">Belum ada kegiatan.</div>
        @endforelse
    </div>

    {{ $kegiatans->links('components.pagination') }}
</x-app-layout>
