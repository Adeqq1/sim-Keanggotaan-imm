<x-app-layout>
    <x-slot name="header">
        Manajemen Anggota
    </x-slot>

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
        <h6 class="fw-bold mb-0">Daftar Anggota</h6>
        <div class="anggota-index-actions d-flex flex-nowrap gap-2">
            <form action="{{ route('admin.anggota.generate-nia-bulk') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-ui btn-ui-sm" title="Isi NIA untuk anggota yang belum memiliki NIA" onclick="return confirm('Isi NIA untuk semua anggota yang belum memiliki NIA?')">
                    <i class="bi bi-magic"></i> Isi NIA Kosong
                </button>
            </form>
            <a href="{{ route('admin.anggota.create') }}" class="btn btn-primary btn-ui btn-ui-sm anggota-index-add" aria-label="Tambah anggota" title="Tambah anggota">
                <i class="bi bi-plus-lg"></i><span class="d-none d-sm-inline"> Tambah</span>
            </a>
        </div>
    </div>

    <form action="{{ route('admin.anggota.index') }}" method="GET" class="mb-4">
        <input type="hidden" name="sort" value="{{ $sort['key'] }}">
        <input type="hidden" name="direction" value="{{ $sort['direction'] }}">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md">
                <label for="anggota-search" class="visually-hidden">Cari anggota berdasarkan nama atau NIA</label>
                <input id="anggota-search" type="text" name="search" class="form-control shadow-sm" placeholder="Cari nama atau NIA..." value="{{ $search }}" aria-label="Cari anggota berdasarkan nama atau NIA">
            </div>
            <div class="col-12 col-md-auto">
                <label for="anggota-role" class="visually-hidden">Filter role anggota</label>
                <select id="anggota-role" name="role" class="form-select shadow-sm" aria-label="Filter role anggota">
                    <option value="">Semua role</option>
                    @foreach (App\Enums\RoleEnum::cases() as $roleOption)
                        @continue($roleOption === App\Enums\RoleEnum::ADMIN)
                        <option value="{{ $roleOption->value }}" @selected($selectedRole === $roleOption->value)>
                            {{ $roleOption->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button class="btn btn-primary btn-ui flex-grow-1" type="submit">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
                @if($search !== '' || $selectedRole !== null)
                    <a href="{{ route('admin.anggota.index') }}" class="btn btn-outline-secondary btn-ui flex-grow-1 text-nowrap">Atur ulang filter</a>
                @endif
            </div>
        </div>
    </form>
    <x-sort-control :action="route('admin.anggota.index')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" :preserved-inputs="['search' => $search, 'role' => $selectedRole]" />

    <div class="row g-3 index-card-grid">
    @forelse($anggotas as $anggota)
        <div class="col-12 col-sm-6">
        <div class="card h-100 p-3 index-card d-flex flex-column">
            <div class="d-flex align-items-center gap-2">
                <div class="me-3">
                    @if($anggota->foto_profil)
                        <img src="{{ Storage::url($anggota->foto_profil) }}" class="rounded-circle shadow-sm" width="50" height="50" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 50px; height: 50px;">
                            {{ substr($anggota->nama_lengkap, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h6 class="fw-bold mb-0 text-break">{{ $anggota->nama_lengkap }}</h6>
                        @if($anggota->user)
                            <span class="badge {{ $anggota->user->role_color }}" style="font-size: 0.7rem;">
                                {{ ucfirst($anggota->user->role) }}
                            </span>
                        @endif
                    </div>
                    <small class="text-muted">NIA: {{ $anggota->nia ?? '-' }}</small>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-label="Aksi untuk {{ $anggota->nama_lengkap }}">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('admin.anggota.show', $anggota->id) }}"><i class="bi bi-eye me-2 text-primary"></i> Detail</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.anggota.edit', $anggota->id) }}"><i class="bi bi-pencil me-2 text-info"></i> Ubah</a></li>
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
        </div>
    @empty
        <div class="col-12 text-center py-5">
            @if($search !== '' || $selectedRole !== null)
                <i class="bi bi-search display-4 text-muted opacity-50"></i>
                <p class="text-muted mt-2">Tidak ada anggota yang sesuai dengan pencarian atau filter yang dipilih.</p>
            @else
                <i class="bi bi-people display-4 text-muted"></i>
                <p class="text-muted mt-2">Belum ada data anggota.</p>
            @endif
        </div>
    @endforelse
    </div>

    @foreach($anggotas as $anggota)
        <x-_modal-delete
            id="deleteModal{{ $anggota->id }}"
            :action="route('admin.anggota.destroy', $anggota->id)"
            message="Menghapus anggota ini akan menghapus semua riwayat presensi dan sertifikat terkait."
        />
    @endforeach

    {{ $anggotas->links('components.pagination') }}
</x-app-layout>
