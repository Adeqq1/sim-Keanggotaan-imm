<x-app-layout>
    <x-slot name="header">Materi Tersimpan</x-slot>

    <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h6 class="fw-bold mb-1">Materi Tersimpan</h6>
            <small class="text-muted">Materi yang Anda tandai dan masih dapat diakses.</small>
        </div>
        <a href="{{ route('kader.materi.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm flex-shrink-0"><i class="bi bi-arrow-left me-1"></i> Semua Materi</a>
    </div>
    <x-sort-control :action="route('kader.materi.saved.index')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />

    <div class="row g-3 index-card-grid">
        @forelse($materis as $materi)
            <div class="col-12 col-sm-6">
                <div class="card h-100 p-3 border-0 shadow-sm index-card d-flex flex-column" style="border-radius: 15px;">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-success flex-shrink-0" style="width: 45px; height: 45px;"><i class="bi bi-bookmark-check fs-4"></i></div>
                        <div class="min-w-0">
                            <h6 class="fw-bold mb-1 text-break">{{ $materi->judul }}</h6>
                            <small class="text-primary d-block text-break"><i class="bi bi-calendar-event me-1"></i>{{ $materi->kegiatan->nama_kegiatan }}</small>
                        </div>
                    </div>
                    <p class="small text-muted flex-grow-1">{{ str($materi->deskripsi)->limit(130) }}</p>
                    <a href="{{ route('kader.materi.download', $materi) }}" class="btn btn-primary btn-ui btn-ui-sm"><i class="bi bi-download me-1"></i> Unduh</a>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-bookmark-x display-4 text-muted opacity-25"></i>
                <p class="text-muted mt-2 mb-0">Belum ada materi tersimpan yang dapat diakses.</p>
            </div>
        @endforelse
    </div>

    {{ $materis->links('components.pagination') }}
    <div class="pb-3"></div>
</x-app-layout>
