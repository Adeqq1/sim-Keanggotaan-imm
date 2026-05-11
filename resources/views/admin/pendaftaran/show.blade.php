<x-app-layout>
    <x-slot name="header">
        Detail Pendaftar
    </x-slot>

    <div class="card p-4 mb-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Pribadi</h6>
        <div class="row g-3">
            <div class="col-12">
                <small class="text-muted d-block">Nama Lengkap</small>
                <span class="fw-bold">{{ $pendaftaran->nama_lengkap }}</span>
            </div>
            <div class="col-12">
                <small class="text-muted d-block">Email</small>
                <span class="fw-bold">{{ $pendaftaran->email }}</span>
            </div>
            <div class="col-6">
                <small class="text-muted d-block">Tempat Lahir</small>
                <span class="fw-bold">{{ $pendaftaran->tempat_lahir }}</span>
            </div>
            <div class="col-6">
                <small class="text-muted d-block">Tanggal Lahir</small>
                <span class="fw-bold">{{ $pendaftaran->tanggal_lahir->format('d/m/Y') }}</span>
            </div>
            <div class="col-12">
                <small class="text-muted d-block">No. Telepon</small>
                <span class="fw-bold">{{ $pendaftaran->no_telp }}</span>
            </div>
            <div class="col-12">
                <small class="text-muted d-block">Alamat</small>
                <span class="fw-bold">{{ $pendaftaran->alamat }}</span>
            </div>
        </div>

        @if($pendaftaran->file_persyaratan)
            <div class="mt-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Lampiran</h6>
                <a href="{{ Storage::url($pendaftaran->file_persyaratan) }}" target="_blank" class="btn btn-outline-primary w-100 py-3">
                    <i class="bi bi-file-earmark-pdf me-2"></i> Lihat File Persyaratan
                </a>
            </div>
        @endif
    </div>

    @if($pendaftaran->status_validasi === 'pending')
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Tindakan Validasi</h6>
            <form action="{{ route('admin.pendaftaran.validate', $pendaftaran) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">Catatan Admin</label>
                    <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Alasan penolakan atau catatan tambahan..."></textarea>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" name="status" value="disetujui" class="btn btn-success py-3 fw-bold">
                        <i class="bi bi-check-circle me-2"></i> Setujui & Buat Akun
                    </button>
                    <button type="submit" name="status" value="ditolak" class="btn btn-danger py-3 fw-bold">
                        <i class="bi bi-x-circle me-2"></i> Tolak Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="alert {{ $pendaftaran->status_validasi === 'disetujui' ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm">
            Status: <strong>{{ ucfirst($pendaftaran->status_validasi) }}</strong>
            @if($pendaftaran->catatan_admin)
                <p class="mt-2 mb-0 small">{{ $pendaftaran->catatan_admin }}</p>
            @endif
        </div>
    @endif

    <div class="text-center mt-4 mb-3">
        <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-link text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
</x-app-layout>
