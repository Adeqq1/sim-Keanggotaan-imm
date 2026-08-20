<x-app-layout>
    <x-slot name="header">Materi Kegiatan</x-slot>
    <x-kegiatan-submenu />
    <x-sort-control :action="route('admin.materi-kegiatan.index')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />

    <div class="row g-3 index-card-grid">
        @forelse($materis as $materi)
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 p-3">
                    <h6 class="fw-bold">{{ $materi->judul }}</h6>
                    <p class="small text-muted mb-2">{{ $materi->kegiatan->nama_kegiatan }} · {{ $materi->kegiatan->tanggal_waktu->translatedFormat('d F Y') }}</p>
                    <p class="text-muted">{{ Str::limit($materi->deskripsi ?: '-', 150) }}</p>
                    <small class="text-muted mb-3">Diunggah {{ $materi->created_at->translatedFormat('d F Y') }}</small>
                    <a href="{{ route('admin.kegiatan.show', $materi->kegiatan) }}" class="btn btn-outline-primary btn-ui btn-ui-sm mt-auto">Lihat Kegiatan</a>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">Belum ada materi kegiatan.</div>
        @endforelse
    </div>

    {{ $materis->links('components.pagination') }}
</x-app-layout>
