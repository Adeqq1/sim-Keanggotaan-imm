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
            <div class="col-12">
                <small class="text-muted d-block">Daftar Sebagai</small>
                <span class="badge {{ \App\Enums\RoleEnum::badgeClassFor($pendaftaran->role) }}">
                    {{ \App\Enums\RoleEnum::labelFor($pendaftaran->role) }}
                </span>
            </div>
            <div class="col-12 col-sm-6">
                <small class="text-muted d-block">Komisariat</small>
                <span class="fw-bold">
                    {{ $pendaftaran->komisariat_id ? (\App\Models\Pendaftaran::KOMISARIAT[$pendaftaran->komisariat_id] ?? $pendaftaran->komisariat_id) : 'Tidak tercatat (data lama)' }}
                </span>
            </div>
            <div class="col-12 col-sm-6">
                <small class="text-muted d-block">Tahun Daftar</small>
                <span class="fw-bold">{{ $pendaftaran->tahun_daftar ?? 'Tidak tercatat (data lama)' }}</span>
            </div>
            <div class="col-12 col-sm-6">
                <small class="text-muted d-block">Tempat Lahir</small>
                <span class="fw-bold">{{ $pendaftaran->tempat_lahir }}</span>
            </div>
            <div class="col-12 col-sm-6">
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

        <div class="mt-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3">Dokumen Identitas</h6>
            <div class="mb-3">
                <small class="text-muted d-block">Jenis Dokumen Identitas</small>
                <span class="fw-bold">
                    {{ $pendaftaran->jenis_dokumen_identitas ? (\App\Models\Pendaftaran::JENIS_DOKUMEN_IDENTITAS[$pendaftaran->jenis_dokumen_identitas] ?? 'Tidak dikenal') : 'Tidak tercatat (data lama)' }}
                </span>
            </div>

            @if($pendaftaran->file_persyaratan)
                @php
                    $ext = strtolower(pathinfo($pendaftaran->file_persyaratan, PATHINFO_EXTENSION));
                @endphp
                <div class="d-flex gap-2 flex-wrap">
                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                        <button type="button" class="btn btn-outline-primary btn-ui btn-ui-sm pendaftaran-detail-control py-2 preview-image-btn"
                            data-preview-url="{{ route('admin.pendaftaran.document.preview', $pendaftaran) }}"
                            data-download-url="{{ route('admin.pendaftaran.document.download', $pendaftaran) }}"
                            data-nama="{{ $pendaftaran->nama_lengkap }}">
                            <i class="bi bi-eye me-2"></i> Pratinjau Dokumen
                        </button>
                    @elseif($ext === 'pdf')
                        <a href="{{ route('admin.pendaftaran.document.preview', $pendaftaran) }}" target="_blank" rel="noopener"
                           class="btn btn-outline-primary btn-ui btn-ui-sm pendaftaran-detail-control py-2">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Buka PDF di Tab Baru
                        </a>
                    @endif
                    <a href="{{ route('admin.pendaftaran.document.download', $pendaftaran) }}" class="btn btn-outline-primary btn-ui btn-ui-sm pendaftaran-detail-control py-2">
                        <i class="bi bi-download me-2"></i> Unduh Dokumen Identitas
                    </a>
                </div>
            @else
                <div class="text-muted small">Dokumen tidak tersedia pada data lama.</div>
            @endif
        </div>
    </div>

    @if($pendaftaran->status_validasi === 'pending')
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Tindakan Validasi</h6>

            @error('email')
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
            @enderror
            @error('status')
                <div class="alert alert-warning">
                    {{ $message }}
                </div>
            @enderror

            <form action="{{ route('admin.pendaftaran.validate', $pendaftaran) }}" method="POST" class="mb-3">
                @csrf
                <input type="hidden" name="status" value="disetujui">

                <label class="form-label small fw-bold">Role Akun</label>
                <select name="role" class="form-select @error('role') is-invalid @enderror mb-3" required>
                    <option value="kader" @selected(old('role', $pendaftaran->role ?? 'kader') === 'kader')>Kader</option>
                    <option value="instruktur" @selected(old('role', $pendaftaran->role ?? 'kader') === 'instruktur')>Instruktur</option>
                </select>
                <div class="form-text small mb-3">Konfirmasi role final sebelum akun dibuat.</div>
                @error('role')
                    <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn btn-success btn-ui btn-ui-sm pendaftaran-detail-control w-100 w-sm-auto py-2">
                    <i class="bi bi-check-circle me-2"></i> Setujui & Buat Akun
                </button>
            </form>

            <form action="{{ route('admin.pendaftaran.validate', $pendaftaran) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="ditolak">

                <label class="form-label small fw-bold">Catatan Admin</label>
                <textarea name="catatan_admin" class="form-control @error('catatan_admin') is-invalid @enderror" rows="3" placeholder="Alasan penolakan atau catatan tambahan...">{{ old('catatan_admin') }}</textarea>
                @error('catatan_admin')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn btn-danger btn-ui btn-ui-sm pendaftaran-detail-control w-100 w-sm-auto py-2 mt-3">
                    <i class="bi bi-x-circle me-2"></i> Tolak Pendaftaran
                </button>
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
        <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <x-pendaftaran-document-preview />
</x-app-layout>
