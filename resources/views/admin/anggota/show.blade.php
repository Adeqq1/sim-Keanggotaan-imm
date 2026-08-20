<x-app-layout>
    <x-slot name="header">
        Detail Anggota
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.anggota.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                @if($anggota->foto_profil_url)
                    <img src="{{ $anggota->foto_profil_url }}" alt="{{ $anggota->nama_lengkap }}" class="rounded-circle shadow" width="120" height="120" style="object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                    <div class="rounded-circle bg-primary bg-opacity-10 align-items-center justify-content-center text-primary fw-bold shadow" style="display: none; width: 120px; height: 120px; font-size: 3rem;">
                        {{ $anggota->initials }}
                    </div>
                @else
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center text-primary fw-bold shadow" style="width: 120px; height: 120px; font-size: 3rem;">
                        {{ $anggota->initials }}
                    </div>
                @endif
                <h4 class="fw-bold mt-3 mb-1">{{ $anggota->nama_lengkap }}</h4>
                <p class="text-muted mb-0">NIA: {{ $anggota->nia ?? '-' }}</p>
                <span class="badge {{ $anggota->status_aktif ? 'bg-success' : 'bg-secondary' }} mt-2">
                    {{ $anggota->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>

            <hr class="my-4">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Tempat Lahir</label>
                    <p class="mb-0">{{ $anggota->tempat_lahir ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Tanggal Lahir</label>
                    <p class="mb-0">{{ $anggota->tanggal_lahir?->translatedFormat('d F Y') ?? '-' }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label small text-muted fw-bold">Alamat</label>
                    <p class="mb-0">{{ $anggota->alamat ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">No. Telepon</label>
                    <p class="mb-0">{{ $anggota->no_telp ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Email</label>
                    <p class="mb-0">{{ $anggota->user->email ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Terdaftar Sejak</label>
                    <p class="mb-0">{{ $anggota->created_at?->translatedFormat('d F Y') ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Terakhir Diupdate</label>
                    <p class="mb-0">{{ $anggota->updated_at?->translatedFormat('d F Y H:i') ?? '-' }}</p>
                </div>
            </div>

            <hr class="my-4">

                <div class="d-flex gap-2">
                <a href="{{ route('admin.anggota.edit', $anggota->id) }}" class="btn btn-primary btn-ui">
                    <i class="bi bi-pencil"></i> Ubah
                </a>
                <button type="button" class="btn btn-danger btn-ui" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    <x-_modal-delete 
        id="deleteModal" 
        :action="route('admin.anggota.destroy', $anggota->id)" 
        message="Menghapus anggota ini akan menghapus semua riwayat presensi dan sertifikat terkait."
    />
</x-app-layout>
