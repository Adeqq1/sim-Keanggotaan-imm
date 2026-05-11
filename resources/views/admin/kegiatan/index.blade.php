<x-app-layout>
    <x-slot name="header">
        Manajemen Kegiatan
    </x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0">Daftar Kegiatan</h6>
        <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah
        </a>
    </div>

    @forelse($kegiatans as $kegiatan)
        <div class="card mb-3 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-light rounded p-2 me-3 text-center" style="min-width: 55px;">
                    <span class="d-block fw-bold text-primary fs-5">{{ $kegiatan->tanggal_waktu->format('d') }}</span>
                    <span class="small text-muted text-uppercase" style="font-size: 0.65rem;">{{ $kegiatan->tanggal_waktu->format('M Y') }}</span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1">{{ $kegiatan->nama_kegiatan }}</h6>
                    <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i> {{ $kegiatan->lokasi }}</small>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('admin.presensi.show', $kegiatan) }}"><i class="bi bi-check2-square me-2 text-success"></i> Kelola Presensi</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.kegiatan.edit', $kegiatan) }}"><i class="bi bi-pencil me-2 text-info"></i> Edit Kegiatan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item py-2 text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $kegiatan->id }}">
                                <i class="bi bi-trash me-2"></i> Hapus
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <x-_modal-delete 
            id="deleteModal{{ $kegiatan->id }}" 
            :action="route('admin.kegiatan.destroy', $kegiatan)" 
            message="Menghapus kegiatan ini akan menghapus semua data presensi dan sertifikat yang terkait."
        />
    @empty
        <div class="text-center py-5">
            <i class="bi bi-calendar-x display-4 text-muted"></i>
            <p class="text-muted mt-2">Belum ada kegiatan.</p>
        </div>
    @endforelse

    {{ $kegiatans->links('components.pagination') }}
</x-app-layout>
