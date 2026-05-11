<x-app-layout>
    <x-slot name="header">
        E-Sertifikat
    </x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0">Riwayat Sertifikat</h6>
        <a href="{{ route('admin.sertifikat.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Generate
        </a>
    </div>

    @forelse($sertifikats as $cert)
        <div class="card mb-3 p-3">
            <div class="d-flex align-items-center">
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
                <a href="{{ route('admin.sertifikat.download', $cert) }}" class="btn btn-link text-success p-0">
                    <i class="bi bi-download fs-4"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-patch-minus display-4 text-muted"></i>
            <p class="text-muted mt-2">Belum ada sertifikat yang di-generate.</p>
        </div>
    @endforelse

    {{ $sertifikats->links('components.pagination') }}
</x-app-layout>
