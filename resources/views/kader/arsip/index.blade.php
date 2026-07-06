<x-app-layout>
    <x-slot name="header">
        Arsip Dokumen
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup notifikasi"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-exclamation-octagon me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup notifikasi"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Daftar Arsip</h6>
        <a href="{{ route('kader.arsip.create') }}" class="btn btn-primary btn-sm fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Unggah Dokumen
        </a>
    </div>
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
    </form>

    @forelse($arsips as $arsip)
        <div class="card mb-3 p-3 border-0 shadow-sm" style="border-radius: 15px;">
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
    @empty
        <div class="text-center py-5">
            <i class="bi bi-folder-x display-4 text-muted opacity-25"></i>
            <p class="text-muted mt-2 small">Belum ada arsip dokumen.</p>
        </div>
    @endforelse

    {{ $arsips->links('components.pagination') }}
    <div class="pb-3"></div>
</x-app-layout>
