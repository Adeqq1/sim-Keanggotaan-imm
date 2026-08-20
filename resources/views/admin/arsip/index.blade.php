<x-app-layout>
    <x-slot name="header">
        Arsip Dokumen
    </x-slot>

    <h6 class="fw-bold mb-3">Daftar Arsip</h6>

    <form method="GET" action="{{ route('admin.arsip.index') }}" class="row g-2 mb-4">
        <input type="hidden" name="sort" value="{{ $sort['key'] }}">
        <input type="hidden" name="direction" value="{{ $sort['direction'] }}">
        <div class="col-12 col-md-6">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul atau nomor dokumen...">
        </div>
        <div class="col-8 col-md-4">
            <select name="kategori" class="form-select">
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
                <a href="{{ route('admin.arsip.index') }}" class="small text-decoration-none">Atur ulang filter</a>
            </div>
        @endif
    </form>
    <x-sort-control :action="route('admin.arsip.index')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" :preserved-inputs="['q' => request('q'), 'kategori' => request('kategori')]" />

    <div class="row g-3 index-card-grid">
    @forelse($arsips as $arsip)
        <div class="col-12 col-sm-6">
        <div class="card h-100 p-3 index-card d-flex flex-column">
            <div class="d-flex align-items-center">
                <div class="bg-light rounded p-2 me-3 text-center" style="min-width: 45px;">
                    <i class="bi bi-file-earmark-text fs-3 text-primary"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold mb-1 text-truncate">{{ $arsip->judul_dokumen }}</h6>
                    <small class="text-muted d-block text-truncate">No: {{ $arsip->nomor_dokumen ?? '-' }}</small>
                    <small class="text-muted d-block text-truncate">Anggota: {{ $arsip->anggota?->nama_lengkap ?? '-' }}</small>
                    <span class="badge bg-light text-dark fw-normal" style="font-size: 0.7rem;">{{ $arsip->kategori_label }}</span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-label="Aksi untuk {{ $arsip->judul_dokumen }}">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('admin.arsip.download', $arsip) }}"><i class="bi bi-download me-2 text-success"></i> Unduh</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item py-2 text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $arsip->id }}">
                                <i class="bi bi-trash me-2"></i> Hapus
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-folder-x display-4 text-muted"></i>
            <p class="text-muted mt-2">Belum ada arsip yang diunggah.</p>
        </div>
    @endforelse
    </div>

    @foreach($arsips as $arsip)
        <x-_modal-delete
            id="deleteModal{{ $arsip->id }}"
            :action="route('admin.arsip.destroy', $arsip)"
            message="Data arsip yang dihapus tidak dapat dikembalikan."
        />
    @endforeach

    {{ $arsips->links('components.pagination') }}
</x-app-layout>
