<x-app-layout>
    <x-slot name="header">Rekap Presensi</x-slot>

    @if(auth()->user()->role === 'admin')
        <x-kegiatan-submenu />
    @endif
    <x-sort-control :action="route('admin.presensi.index')" :options="$options" :selected-sort="$sort['key']" />

    <div class="row g-3 index-card-grid">
        @forelse($kegiatans as $kegiatan)
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 p-3" data-kegiatan-id="{{ $kegiatan->id }}" data-peserta="{{ $kegiatan->presensi_count }}" data-hadir="{{ $kegiatan->hadir_count }}" data-izin="{{ $kegiatan->izin_count }}" data-alfa="{{ $kegiatan->alfa_count }}">
                    <h6 class="fw-bold">{{ $kegiatan->nama_kegiatan }}</h6>
                    <p class="small text-muted mb-3"><i class="bi bi-calendar-event me-1"></i>{{ $kegiatan->tanggal_waktu->translatedFormat('d F Y, H:i') }}<br><i class="bi bi-geo-alt me-1"></i>{{ $kegiatan->lokasi }}</p>
                    <div class="row text-center g-2 mb-3">
                        @foreach([['Peserta', $kegiatan->presensi_count], ['Hadir', $kegiatan->hadir_count], ['Izin', $kegiatan->izin_count], ['Alfa', $kegiatan->alfa_count]] as [$label, $count])
                            <div class="col-3"><strong class="d-block fs-5">{{ $count }}</strong><small class="text-muted">{{ $label }}</small></div>
                        @endforeach
                    </div>
                    @if($kegiatan->presensi_count === 0)<p class="small text-muted">Presensi belum dicatat.</p>@endif
                    <a href="{{ route('admin.presensi.show', $kegiatan) }}" class="btn btn-outline-primary btn-ui btn-ui-sm mt-auto">Lihat Presensi</a>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">Belum ada kegiatan.</div>
        @endforelse
    </div>

    {{ $kegiatans->links('components.pagination') }}
</x-app-layout>
