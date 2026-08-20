<x-app-layout>
    <x-slot name="header">Materi Kegiatan</x-slot>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">{{ $kegiatan->nama_kegiatan }}</h5>
            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $kegiatan->tanggal_waktu->translatedFormat('d F Y, H:i') }} WIB</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
            <a href="{{ route('admin.kegiatan.materi-kegiatan.create', $kegiatan) }}" class="btn btn-primary btn-ui btn-ui-sm"><i class="bi bi-plus-lg"></i> Tambah Materi</a>
        </div>
    </div>
    <x-sort-control :action="route('admin.kegiatan.materi-kegiatan.index', $kegiatan)" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />

    <div class="row g-3 index-card-grid">
        @forelse($materis as $materi)
            <div class="col-12 col-sm-6">
                <div class="card h-100 p-3 border-0 shadow-sm index-card">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 48px; height: 48px;">
                            <i class="bi bi-file-earmark-text fs-4"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="fw-bold mb-1 text-break">{{ $materi->judul }}</h6>
                            <p class="small text-muted mb-1">{{ str($materi->deskripsi)->limit(110) }}</p>
                            <small class="text-muted">Diunggah {{ $materi->created_at->translatedFormat('d M Y') }}</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-label="Aksi untuk {{ $materi->judul }}">
                                <i class="bi bi-three-dots-vertical fs-5"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item py-2" href="{{ route('admin.kegiatan.materi-kegiatan.edit', [$kegiatan, $materi]) }}"><i class="bi bi-pencil me-2 text-info"></i> Ubah Materi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button type="button" class="dropdown-item py-2 text-danger" data-bs-toggle="modal" data-bs-target="#deleteMateri{{ $materi->id }}"><i class="bi bi-trash me-2"></i> Hapus</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-x display-4 text-muted opacity-25"></i>
                <p class="text-muted mt-2">Belum ada materi untuk kegiatan ini.</p>
            </div>
        @endforelse
    </div>

    @foreach($materis as $materi)
        <x-_modal-delete id="deleteMateri{{ $materi->id }}" :action="route('admin.kegiatan.materi-kegiatan.destroy', [$kegiatan, $materi])" message="File materi ini juga akan dihapus secara permanen." />
    @endforeach

    {{ $materis->links('components.pagination') }}
</x-app-layout>
