<x-app-layout>
    <x-slot name="header">
        E-Sertifikat Saya
    </x-slot>

    <div class="certificate-page-heading">
        <div class="certificate-page-heading__title">
            <span class="certificate-page-heading__icon"><i class="bi bi-award-fill"></i></span>
            <div>
                <h2 class="h5 fw-bold mb-1">Koleksi Sertifikat</h2>
                <p class="text-muted small mb-0">Daftar sertifikat kegiatan yang telah Anda ikuti.</p>
            </div>
        </div>
        <div class="certificate-page-heading__status">
            <strong>{{ $sertifikats->total() }}</strong>
            <span>Sertifikat</span>
        </div>
        <p class="certificate-page-heading__attendance text-muted small mb-0">
            <i class="bi bi-patch-check-fill text-success me-1"></i>
            Kehadiran terkonfirmasi: <strong>{{ $jumlahKegiatanHadir }} dari {{ $minimumKegiatanHadir }}</strong> kegiatan hadir.
        </p>
    </div>
    <div class="certificate-sort-toolbar {{ $sertifikats->isEmpty() ? 'certificate-sort-toolbar--empty' : '' }}">
        <span class="certificate-sort-toolbar__label"><i class="bi bi-filter me-1"></i> Urutkan koleksi</span>
        <x-sort-control :action="route('kader.sertifikat.index')" :options="$options" :selected-sort="$sort['key']" />
    </div>

    <div class="row g-3 index-card-grid certificate-card-grid">
    @forelse($sertifikats as $cert)
        <div class="col-12 col-sm-6">
        <div class="card h-100 p-3 border-0 shadow-sm index-card certificate-card certificate-card--kader d-flex flex-column">
            <div class="d-flex align-items-center gap-2 index-card__content">
                <div class="me-3">
                    <div class="certificate-card__icon" aria-hidden="true">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <h3 class="h6 fw-bold mb-1 text-break">{{ $cert->kegiatan->nama_kegiatan }}</h3>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Diterbitkan: {{ $cert->created_at->translatedFormat('d M Y') }}</small>
                    <small class="certificate-card__number d-block text-break">{{ $cert->nomor_sertifikat }}</small>
                </div>
            </div>
            <div class="pt-3 mt-auto index-card__actions certificate-card__actions">
                @if($canDownloadSertifikat && $eligibleKegiatanIds->contains($cert->kegiatan_id))
                    <a href="{{ route('kader.sertifikat.download', $cert) }}" class="btn btn-outline-primary btn-ui btn-ui-sm certificate-card__download" aria-label="Unduh sertifikat {{ $cert->kegiatan->nama_kegiatan }} dalam format PDF">
                        <i class="bi bi-download" aria-hidden="true"></i><span>Unduh PDF</span>
                    </a>
                @elseif(! $canDownloadSertifikat)
                    <span class="certificate-card__locked"><i class="bi bi-lock-fill" aria-hidden="true"></i><span>Unduh terkunci sampai {{ $minimumKegiatanHadir }} kegiatan hadir.</span></span>
                @else
                    <span class="certificate-card__locked"><i class="bi bi-lock-fill" aria-hidden="true"></i><span>Unduh terkunci sampai kehadiran kegiatan terkonfirmasi.</span></span>
                @endif
            </div>
        </div>
        </div>
    @empty
        <div class="col-12"><div class="card certificate-empty-state border-0 shadow-sm bg-light">
            <div class="certificate-empty-state__icon"><i class="bi bi-patch-minus"></i></div>
            <div>
                <h6 class="fw-bold text-muted mb-1">Belum Ada Sertifikat</h6>
                <p class="text-muted small mb-2">Sertifikat yang sudah diterbitkan oleh admin akan muncul di sini.</p>
                <a href="{{ route('kader.riwayat.index') }}" class="small text-decoration-none certificate-empty-state__link">
                    Lihat Riwayat Kehadiran <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div></div>
    @endforelse
    </div>

    {{ $sertifikats->links('components.pagination') }}

    <div class="mt-5 certificate-activity-tip">
        <i class="bi bi-lightbulb certificate-activity-tip__icon"></i>
        <div>
            <h6 class="fw-bold mb-1">Tips Keaktifan</h6>
            <p class="small mb-0">Ikuti lebih banyak kegiatan organisasi untuk meningkatkan portofolio keaktifan Anda!</p>
        </div>
        <a href="{{ route('kader.riwayat.index') }}" class="btn btn-sm btn-outline-success btn-ui">Lihat Riwayat</a>
    </div>
</x-app-layout>
