<x-app-layout>
    <x-slot name="header">
        E-Sertifikat Saya
    </x-slot>

    <div class="mb-4">
        <h2 class="h6 fw-bold mb-1">Koleksi Sertifikat</h2>
        <p class="text-muted small">Daftar sertifikat kegiatan yang telah Anda ikuti.</p>
        <p class="text-muted small mb-0">Kehadiran terkonfirmasi: {{ $jumlahKegiatanHadir }} dari {{ $minimumKegiatanHadir }} kegiatan hadir.</p>
    </div>
    <x-sort-control :action="route('kader.sertifikat.index')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />

    <div class="row g-3 index-card-grid">
    @forelse($sertifikats as $cert)
        <div class="col-12 col-sm-6">
        <div class="card h-100 p-3 border-0 shadow-sm index-card certificate-card d-flex flex-column">
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
                    <span class="small text-muted"><i class="bi bi-lock-fill me-1" aria-hidden="true"></i>Unduh terkunci sampai {{ $minimumKegiatanHadir }} kegiatan hadir.</span>
                @else
                    <span class="small text-muted"><i class="bi bi-lock-fill me-1" aria-hidden="true"></i>Unduh terkunci sampai kehadiran kegiatan terkonfirmasi.</span>
                @endif
            </div>
        </div>
        </div>
    @empty
        <div class="col-12"><div class="card p-5 text-center border-0 shadow-sm bg-light" style="border-radius: 20px;">
            <i class="bi bi-patch-minus display-1 text-muted opacity-25 mb-3"></i>
            <h6 class="fw-bold text-muted">Belum Ada Sertifikat</h6>
            <p class="text-muted small mb-0 px-4">Sertifikat yang sudah diterbitkan akan muncul di sini.</p>
        </div></div>
    @endforelse
    </div>

    {{ $sertifikats->links('components.pagination') }}

    <div class="mt-5 p-3 glass-card text-center text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 15px;">
        <i class="bi bi-lightbulb fs-3 mb-2 d-block"></i>
        <h6 class="fw-bold mb-1">Tips Keaktifan</h6>
        <p class="small mb-0 opacity-75">Ikuti lebih banyak kegiatan organisasi untuk meningkatkan portofolio keaktifan Anda!</p>
    </div>
</x-app-layout>
