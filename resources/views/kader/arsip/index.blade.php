<x-app-layout>
    <x-slot name="header">
        Arsip Dokumen
    </x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Daftar Arsip</h6>
        <a href="{{ route('kader.arsip.create') }}" class="btn btn-primary btn-sm fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Unggah Dokumen
        </a>
    </div>

    <form method="GET" action="{{ route('kader.arsip.index') }}" class="row g-2 mb-4">
        <div class="col-12 col-md-6">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0" placeholder="Cari judul atau nomor dokumen...">
        </div>
        <div class="col-8 col-md-4">
            <select name="kategori" class="form-select bg-light border-0">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $value => $label)
                    <option value="{{ $value }}" @selected(request('kategori') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-4 col-md-2">
            <button type="submit" class="btn btn-primary w-100">Cari</button>
        </div>
        @if(request()->filled('q') || request()->filled('kategori'))
            <div class="col-12">
                <a href="{{ route('kader.arsip.index') }}" class="small text-decoration-none">Atur ulang filter</a>
            </div>
        @endif
    </form>

    <div class="row g-3 index-card-grid">
    @forelse($arsips as $arsip)
        <div class="col-12 col-sm-6">
        <div class="card h-100 p-3 border-0 shadow-sm index-card d-flex flex-column" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary" style="width: 45px; height: 45px;">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold mb-1 text-truncate" style="font-size: 0.9rem;">{{ $arsip->judul_dokumen }}</h6>
                    <small class="text-muted d-block text-truncate" style="font-size: 0.75rem;">No: {{ $arsip->nomor_dokumen ?? '-' }}</small>
                    <span class="badge bg-light text-dark fw-normal mt-1" style="font-size: 0.65rem;">{{ $arsip->kategori_label }}</span>
                </div>
                <a href="{{ route('kader.arsip.download', $arsip) }}" class="btn btn-outline-primary btn-ui btn-ui-sm btn-icon" aria-label="Unduh arsip {{ $arsip->judul_dokumen }}" title="Unduh arsip">
                    <i class="bi bi-download fs-4"></i>
                </a>
            </div>
        </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-folder-x display-4 text-muted opacity-25"></i>
            <p class="text-muted mt-2 small">Belum ada arsip dokumen.</p>
        </div>
    @endforelse
    </div>

    {{ $arsips->links('components.pagination') }}
    <div class="pb-3"></div>
</x-app-layout>
