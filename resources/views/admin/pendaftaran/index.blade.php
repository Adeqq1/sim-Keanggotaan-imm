<x-app-layout>
    <x-slot name="header">
        Validasi Pendaftaran
    </x-slot>

    <div class="mb-3">
        <h6 class="fw-bold">Daftar Calon Anggota</h6>
        <p class="text-muted small">Klik pada nama untuk melihat detail dan melakukan validasi.</p>
    </div>

    @forelse($pendaftarans as $item)
        <div class="card mb-3 p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="fw-bold mb-1">{{ $item->nama_lengkap }}</h6>
                    <small class="text-muted d-block"><i class="bi bi-envelope me-1"></i> {{ $item->email }}</small>
                    <small class="text-muted d-block"><i class="bi bi-calendar-check me-1"></i> {{ $item->tanggal_daftar->format('d M Y') }}</small>
                </div>
                <span class="badge {{ $item->status_validasi === 'pending' ? 'bg-warning' : ($item->status_validasi === 'disetujui' ? 'bg-success' : 'bg-danger') }}">
                    {{ ucfirst($item->status_validasi) }}
                </span>
            </div>
            
            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('admin.pendaftaran.show', $item) }}" class="btn btn-primary btn-sm flex-grow-1">Detail & Validasi</a>
                @if($item->file_persyaratan)
                    <a href="{{ Storage::url($item->file_persyaratan) }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-text"></i></a>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-person-x display-4 text-muted"></i>
            <p class="text-muted mt-2">Tidak ada pendaftaran pending.</p>
        </div>
    @endforelse

    {{ $pendaftarans->links('components.pagination') }}
</x-app-layout>
