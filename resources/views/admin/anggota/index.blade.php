<x-app-layout>
    <x-slot name="header">
        Manajemen Anggota
    </x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0">Daftar Anggota</h6>
        <a href="{{ route('admin.anggota.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah
        </a>
    </div>

    <form action="{{ route('admin.anggota.index') }}" method="GET" class="mb-4">
        <div class="input-group shadow-sm">
            <input type="text" name="search" class="form-control border-0" placeholder="Cari nama atau NIA..." value="{{ request('search') }}">
            <button class="btn btn-white border-0" type="submit"><i class="bi bi-search text-primary"></i></button>
        </div>
    </form>

    @forelse($anggotas as $anggota)
        <div class="card mb-3 p-3">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    @if($anggota->foto_profil)
                        <img src="{{ Storage::url($anggota->foto_profil) }}" class="rounded-circle shadow-sm" width="50" height="50" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 50px; height: 50px;">
                            {{ substr($anggota->nama_lengkap, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0">{{ $anggota->nama_lengkap }}</h6>
                    <small class="text-muted">NIA: {{ $anggota->nia ?? '-' }}</small>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('admin.anggota.show', $anggota) }}"><i class="bi bi-eye me-2 text-primary"></i> Detail</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.anggota.edit', $anggota) }}"><i class="bi bi-pencil me-2 text-info"></i> Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item py-2 text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $anggota->id }}">
                                <i class="bi bi-trash me-2"></i> Hapus
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <x-_modal-delete 
            id="deleteModal{{ $anggota->id }}" 
            :action="route('admin.anggota.destroy', $anggota)" 
            message="Menghapus anggota ini akan menghapus semua riwayat presensi dan sertifikat terkait."
        />
    @empty
        <div class="text-center py-5">
            <i class="bi bi-people display-4 text-muted"></i>
            <p class="text-muted mt-2">Belum ada data anggota.</p>
        </div>
    @endforelse

    {{ $anggotas->links('components.pagination') }}
</x-app-layout>
