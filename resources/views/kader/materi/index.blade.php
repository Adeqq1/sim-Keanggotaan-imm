<x-app-layout>
    <x-slot name="header">Materi Kegiatan</x-slot>

    <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h6 class="fw-bold mb-1">Materi Saya</h6>
            <small class="text-muted">Materi dari kegiatan yang telah Anda hadiri.</small>
        </div>
        <a href="{{ route('kader.materi.saved.index') }}" class="btn btn-outline-primary btn-ui btn-ui-sm flex-shrink-0"><i class="bi bi-bookmark me-1"></i> Tersimpan</a>
    </div>
    <x-sort-control :action="route('kader.materi.index')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />

    <div class="row g-3 index-card-grid">
        @forelse($materis as $materi)
            <div class="col-12 col-sm-6">
                <div class="card h-100 p-3 border-0 shadow-sm index-card d-flex flex-column" style="border-radius: 15px;">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 45px; height: 45px;"><i class="bi bi-journal-text fs-4"></i></div>
                        <div class="min-w-0">
                            <h6 class="fw-bold mb-1 text-break">{{ $materi->judul }}</h6>
                            <small class="text-primary d-block text-break"><i class="bi bi-calendar-event me-1"></i>{{ $materi->kegiatan->nama_kegiatan }}</small>
                        </div>
                    </div>
                    <p class="small text-muted flex-grow-1">{{ str($materi->deskripsi)->limit(130) }}</p>
                    <small class="text-muted mb-3">Diunggah {{ $materi->created_at->translatedFormat('d M Y') }}</small>
                    <div class="d-flex gap-2">
                        <a href="{{ route('kader.materi.download', $materi) }}" class="btn btn-primary btn-ui btn-ui-sm flex-grow-1"><i class="bi bi-download me-1"></i> Unduh</a>
                        @if($materi->tersimpan)
                            <button type="button" class="btn btn-outline-success btn-ui btn-ui-sm" disabled><i class="bi bi-bookmark-check me-1"></i> Tersimpan</button>
                        @else
                            <form action="{{ route('kader.materi.save', $materi) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-ui btn-ui-sm"><i class="bi bi-bookmark me-1"></i> Simpan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-x display-4 text-muted opacity-25"></i>
                <p class="text-muted mt-2 mb-0">Belum ada materi yang dapat diakses.</p>
                <small class="text-muted">Materi akan muncul setelah presensi kegiatan Anda dikonfirmasi hadir.</small>
            </div>
        @endforelse
    </div>

    {{ $materis->links('components.pagination') }}
    <div class="pb-3"></div>
</x-app-layout>
