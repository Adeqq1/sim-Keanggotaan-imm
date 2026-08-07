<x-app-layout>
    <x-slot name="header">
        Validasi Pendaftaran
    </x-slot>

    <div class="mb-3">
        <h6 class="fw-bold">Daftar Calon Anggota</h6>
        <p class="text-muted small">Klik pada nama untuk melihat detail dan melakukan validasi.</p>
    </div>

    <div class="row g-3 index-card-grid">
        @forelse($pendaftarans as $item)
            <div class="col-12 col-sm-6">
                <div class="card h-100 p-3 index-card d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start index-card__content">
                        <div class="min-w-0">
                    <h6 class="fw-bold mb-1 text-break">{{ $item->nama_lengkap }}</h6>
                    <small class="text-muted d-block text-break"><i class="bi bi-envelope me-1"></i> {{ $item->email }}</small>
                    <small class="text-muted d-block"><i class="bi bi-person-badge me-1"></i> Daftar sebagai: {{ \App\Enums\RoleEnum::labelFor($item->role) }}</small>
                    <small class="text-muted d-block"><i class="bi bi-calendar-check me-1"></i> {{ $item->tanggal_daftar->translatedFormat('d M Y') }}</small>
                </div>
                <span class="badge {{ $item->status_validasi === 'pending' ? 'bg-warning' : ($item->status_validasi === 'disetujui' ? 'bg-success' : 'bg-danger') }}">
                    {{ ucfirst($item->status_validasi) }}
                </span>
                    </div>
                    <div class="mt-3 d-flex gap-2 index-card__actions">
                        <a href="{{ route('admin.pendaftaran.show', $item) }}" class="btn btn-primary btn-ui btn-ui-sm flex-grow-1">Detail & Validasi</a>
                        @if($item->file_persyaratan)
                            <a href="{{ Storage::url($item->file_persyaratan) }}" target="_blank" class="btn btn-outline-secondary btn-ui btn-ui-sm btn-icon" aria-label="Lihat lampiran {{ $item->nama_lengkap }}" title="Lihat lampiran"><i class="bi bi-file-earmark-text"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-person-x display-4 text-muted"></i>
                <p class="text-muted mt-2">Tidak ada pendaftaran pending.</p>
            </div>
        @endforelse
    </div>

    {{ $pendaftarans->links('components.pagination') }}
</x-app-layout>
