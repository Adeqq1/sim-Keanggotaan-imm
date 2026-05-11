<x-app-layout>
    <x-slot name="header">
        E-Sertifikat Saya
    </x-slot>

    <div class="mb-4">
        <h6 class="fw-bold">Koleksi Sertifikat</h6>
        <p class="text-muted small">Daftar sertifikat kegiatan yang telah Anda ikuti.</p>
    </div>

    @forelse($sertifikats as $cert)
        <div class="card mb-3 p-3 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="rounded-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-center text-success shadow-sm" style="width: 55px; height: 55px;">
                        <i class="bi bi-award-fill fs-2"></i>
                    </div>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold mb-1 text-truncate">{{ $cert->kegiatan->nama_kegiatan }}</h6>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Diterbitkan: {{ $cert->created_at->format('d M Y') }}</small>
                    <small class="text-primary fw-bold d-block" style="font-size: 0.65rem;">No: {{ $cert->nomor_sertifikat }}</small>
                </div>
                <a href="{{ route('kader.sertifikat.download', $cert) }}" class="btn btn-outline-success border-2 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-download"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="card p-5 text-center border-0 shadow-sm bg-light" style="border-radius: 20px;">
            <i class="bi bi-patch-minus display-1 text-muted opacity-25 mb-3"></i>
            <h6 class="fw-bold text-muted">Belum Ada Sertifikat</h6>
            <p class="text-muted small mb-0 px-4">Sertifikat akan muncul di sini setelah Anda mengikuti kegiatan dan diverifikasi oleh Admin.</p>
        </div>
    @endforelse

    <div class="mt-5 p-3 glass-card text-center text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 15px;">
        <i class="bi bi-lightbulb fs-3 mb-2 d-block"></i>
        <h6 class="fw-bold mb-1">Tips Keaktifan</h6>
        <p class="small mb-0 opacity-75">Ikuti lebih banyak kegiatan organisasi untuk meningkatkan portofolio keaktifan Anda!</p>
    </div>
</x-app-layout>
