<x-app-layout>
    <x-slot name="header">
        E-Sertifikat
    </x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h6 fw-bold mb-1">Riwayat Sertifikat</h2>
            <p class="text-muted small mb-0">Kelola dan unduh sertifikat kegiatan anggota.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.sertifikat.settings') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">
                <i class="bi bi-image" aria-hidden="true"></i><span class="d-none d-sm-inline"> Pengaturan</span>
            </a>
            <a href="{{ route('admin.sertifikat.create') }}" class="btn btn-primary btn-ui btn-ui-sm">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Buat Sertifikat
            </a>
        </div>
    </div>
    <x-sort-control :action="route('admin.sertifikat.index')" :options="$options" :selected-sort="$sort['key']" />

    @if(request()->filled('generation'))
        <div class="certificate-generation-status" data-certificate-generation data-status-url="{{ route('admin.sertifikat.generation.status', request('generation')) }}">
            <div class="certificate-generation-status__icon"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span></div>
            <div class="certificate-generation-status__content">
                <strong data-generation-title>Membuat sertifikat</strong>
                <span data-generation-message>Menunggu proses queue dimulai...</span>
                <div class="progress" role="progressbar" aria-label="Progress pembuatan sertifikat">
                    <div class="progress-bar" data-generation-progress style="width: 0%"></div>
                </div>
            </div>
            <span class="certificate-generation-status__count" data-generation-count>0%</span>
        </div>
    @endif

    <div class="row g-3 index-card-grid" data-certificate-list>
    @forelse($sertifikats as $cert)
        <div class="col-12 col-sm-6">
        <div class="card h-100 p-3 index-card certificate-card d-flex flex-column">
            <div class="d-flex align-items-center index-card__content">
                <div class="me-3">
                    <div class="certificate-card__icon" aria-hidden="true">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <h3 class="h6 fw-bold mb-1 text-break">{{ $cert->anggota->nama_lengkap }}</h3>
                    <small class="text-muted d-block text-break">{{ $cert->kegiatan->nama_kegiatan }}</small>
                    <small class="certificate-card__number d-block text-break">{{ $cert->nomor_sertifikat }}</small>
                </div>
            </div>
            <div class="pt-3 mt-auto index-card__actions certificate-card__actions">
                <a href="{{ route('admin.sertifikat.download', $cert) }}" class="btn btn-outline-primary btn-ui btn-ui-sm certificate-card__download" aria-label="Unduh sertifikat {{ $cert->anggota->nama_lengkap }} dalam format PDF">
                    <i class="bi bi-download" aria-hidden="true"></i><span>Unduh PDF</span>
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
