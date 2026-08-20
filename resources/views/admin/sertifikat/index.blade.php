<x-app-layout>
    <x-slot name="header">
        E-Sertifikat
    </x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0">Riwayat Sertifikat</h6>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.sertifikat.settings') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm btn-icon" aria-label="Pengaturan latar sertifikat" title="Pengaturan latar sertifikat">
                <i class="bi bi-image"></i> 
            </a>
            <a href="{{ route('admin.sertifikat.create') }}" class="btn btn-primary btn-ui btn-ui-sm">
                <i class="bi bi-plus-lg"></i> Tambah
            </a>
        </div>
    </div>
    <x-sort-control :action="route('admin.sertifikat.index')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />

    <div class="row g-3 index-card-grid">
    @forelse($sertifikats as $cert)
        <div class="col-12 col-sm-6">
        <div class="card h-100 p-3 index-card d-flex flex-column">
            <div class="d-flex align-items-center index-card__content">
                <div class="me-3">
                    <div class="rounded bg-light d-flex align-items-center justify-content-center text-success" style="width: 45px; height: 45px;">
                        <i class="bi bi-patch-check fs-3"></i>
                    </div>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold mb-1 text-truncate">{{ $cert->anggota->nama_lengkap }}</h6>
                    <small class="text-muted d-block text-truncate">{{ $cert->kegiatan->nama_kegiatan }}</small>
                    <small class="text-primary d-block" style="font-size: 0.7rem;">No: {{ $cert->nomor_sertifikat }}</small>
                </div>
            </div>
            <div class="pt-3 mt-auto border-top index-card__actions">
                <a href="{{ route('admin.sertifikat.download', $cert) }}" class="btn btn-outline-success btn-ui btn-ui-sm" aria-label="Unduh sertifikat {{ $cert->anggota->nama_lengkap }}" title="Unduh sertifikat">
                    <i class="bi bi-download fs-4"></i>
                </a>
            </div>
        </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-patch-minus display-4 text-muted"></i>
            <p class="text-muted mt-2">Belum ada sertifikat yang di-generate.</p>
        </div>
    @endforelse
    </div>

    {{ $sertifikats->links('components.pagination') }}
</x-app-layout>
