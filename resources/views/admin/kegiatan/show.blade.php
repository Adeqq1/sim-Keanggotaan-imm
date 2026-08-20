<x-app-layout>
    <x-slot name="header">Detail Kegiatan</x-slot>

    @if(auth()->user()->role === 'admin')
        <x-kegiatan-submenu />
    @endif

    <div class="card border-0 shadow-sm overflow-hidden mb-4">
        <img src="{{ $kegiatan->thumbnail_url }}" alt="Thumbnail {{ $kegiatan->nama_kegiatan }}" class="w-100" style="max-height: 360px; object-fit: cover;">
        <div class="card-body p-4">
            <h2 class="h4 fw-bold mb-3">{{ $kegiatan->nama_kegiatan }}</h2>
            <div class="d-flex flex-column flex-md-row gap-3 text-muted mb-4">
                <span><i class="bi bi-calendar-event me-2"></i>{{ $kegiatan->tanggal_waktu->translatedFormat('d F Y, H:i') }}</span>
                <span><i class="bi bi-geo-alt me-2"></i>{{ $kegiatan->lokasi }}</span>
            </div>
            <div class="lh-lg">{!! nl2br(e($kegiatan->deskripsi)) !!}</div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([['Jumlah Peserta', $kegiatan->presensi_count, 'primary'], ['Hadir', $kegiatan->hadir_count, 'success'], ['Izin', $kegiatan->izin_count, 'warning'], ['Alfa', $kegiatan->alfa_count, 'danger']] as [$label, $count, $color])
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 p-3">
                    <small class="text-muted">{{ $label }}</small>
                    <strong class="fs-3 text-{{ $color }}">{{ $count }}</strong>
                </div>
            </div>
        @endforeach
    </div>

    @if($kegiatan->presensi_count === 0)
        <div class="alert alert-secondary">Presensi belum dicatat untuk kegiatan ini.</div>
    @endif

    <div class="d-flex flex-wrap gap-2 mb-5">
        <a href="{{ route('admin.presensi.show', $kegiatan) }}" class="btn btn-outline-primary btn-ui">Lihat Presensi</a>
        @if(auth()->user()->role === 'instruktur')
            <a href="{{ route('admin.kegiatan.materi-kegiatan.index', $kegiatan) }}" class="btn btn-outline-primary btn-ui">Kelola Materi</a>
            @if($kegiatan->laporanKegiatan)
                <a href="{{ route('admin.laporan-kegiatan.download', $kegiatan->laporanKegiatan) }}" class="btn btn-outline-primary btn-ui" aria-label="Unduh laporan {{ $kegiatan->nama_kegiatan }}">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Unduh Laporan
                </a>
            @else
                <span class="badge bg-secondary align-self-center">Laporan belum tersedia</span>
            @endif
        @elseif($kegiatan->laporanKegiatan)
            <a href="{{ route('admin.laporan-kegiatan.show', $kegiatan->laporanKegiatan) }}" class="btn btn-primary btn-ui">Lihat Laporan</a>
            <a href="{{ route('admin.laporan-kegiatan.edit', $kegiatan->laporanKegiatan) }}" class="btn btn-outline-secondary btn-ui">Ubah Laporan</a>
        @else
            <a href="{{ route('admin.kegiatan.laporan-kegiatan.create', $kegiatan) }}" class="btn btn-primary btn-ui">Buat Laporan</a>
            <span class="badge bg-secondary align-self-center">Belum dibuat</span>
        @endif
    </div>
</x-app-layout>
