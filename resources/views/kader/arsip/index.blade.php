<x-app-layout>
    <x-slot name="header">
        Arsip Dokumen
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-exclamation-octagon me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card p-4 mb-4 border-0 shadow-sm" style="border-radius: 15px;">
        <h6 class="fw-bold mb-3">Kirim Dokumen Baru</h6>
        <p class="text-muted small">Unggah dokumen pendukung (SK, Surat, Laporan, dll) ke dalam arsip sistem.</p>
        <form action="{{ route('kader.arsip.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Judul Dokumen</label>
                <input type="text" name="judul_dokumen" class="form-control bg-light border-0 @error('judul_dokumen') is-invalid @enderror" placeholder="Contoh: Laporan Kegiatan Perkaderan" value="{{ old('judul_dokumen') }}" required>
                @error('judul_dokumen')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold">Kategori</label>
                    <select name="kategori_arsip" class="form-select bg-light border-0 @error('kategori_arsip') is-invalid @enderror" required>
                        <option value="laporan" {{ old('kategori_arsip') === 'laporan' ? 'selected' : '' }}>Laporan</option>
                        <option value="surat_masuk" {{ old('kategori_arsip') === 'surat_masuk' ? 'selected' : '' }}>Surat Masuk</option>
                        <option value="surat_keluar" {{ old('kategori_arsip') === 'surat_keluar' ? 'selected' : '' }}>Surat Keluar</option>
                        <option value="lainnya" {{ old('kategori_arsip') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('kategori_arsip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold">No. Dokumen</label>
                    <input type="text" name="nomor_dokumen" class="form-control bg-light border-0 @error('nomor_dokumen') is-invalid @enderror" placeholder="Opsional" value="{{ old('nomor_dokumen') }}">
                    @error('nomor_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Pilih File (PDF, Excel, Word, atau Gambar)</label>
                <input type="file" name="file_arsip" class="form-control bg-light border-0 @error('file_arsip') is-invalid @enderror" required>
                @error('file_arsip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format yang didukung: PDF, XLS, XLSX, DOC, DOCX, JPG, PNG. Maksimal 10MB.</small>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                <i class="bi bi-cloud-upload me-2"></i> Unggah Sekarang
            </button>
        </form>
    </div>

    <h6 class="fw-bold mb-3">Daftar Arsip</h6>
    @forelse($arsips as $arsip)
        <div class="card mb-3 p-3 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary" style="width: 45px; height: 45px;">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold mb-1 text-truncate" style="font-size: 0.9rem;">{{ $arsip->judul_dokumen }}</h6>
                    <small class="text-muted d-block text-truncate" style="font-size: 0.75rem;">No: {{ $arsip->nomor_dokumen ?? '-' }}</small>
                    <span class="badge bg-light text-dark fw-normal mt-1" style="font-size: 0.65rem;">{{ ucfirst(str_replace('_', ' ', $arsip->kategori_arsip)) }}</span>
                </div>
                <a href="{{ route('kader.arsip.download', $arsip) }}" class="btn btn-link text-primary p-0">
                    <i class="bi bi-download fs-4"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-folder-x display-4 text-muted opacity-25"></i>
            <p class="text-muted mt-2 small">Belum ada arsip dokumen.</p>
        </div>
    @endforelse

    {{ $arsips->links('components.pagination') }}
    <div class="pb-3"></div>
</x-app-layout>
