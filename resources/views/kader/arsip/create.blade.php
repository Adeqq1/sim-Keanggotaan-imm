<x-app-layout>
    <x-slot name="header">
        Unggah Dokumen
    </x-slot>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-exclamation-octagon me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card p-4 mb-4 border-0 shadow-sm" style="border-radius: 15px;">
        <h6 class="fw-bold mb-3">Kirim Dokumen Baru</h6>
        <p class="text-muted small">Unggah dokumen pendukung ke dalam arsip sistem.</p>
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
                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label small fw-bold">Kategori</label>
                    <select name="kategori_arsip" class="form-select bg-light border-0 @error('kategori_arsip') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori as $value => $label)
                            <option value="{{ $value }}" @selected(old('kategori_arsip') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('kategori_arsip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label small fw-bold">No. Dokumen</label>
                    <input type="text" name="nomor_dokumen" class="form-control bg-light border-0 @error('nomor_dokumen') is-invalid @enderror" placeholder="Opsional" value="{{ old('nomor_dokumen') }}">
                    @error('nomor_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Pilih File</label>
                <input type="file" name="file_arsip" class="form-control bg-light border-0 @error('file_arsip') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                @error('file_arsip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format yang didukung: PDF, DOC, DOCX, XLS, XLSX. Maksimal 5MB.</small>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('kader.arsip.index') }}" class="btn btn-light w-50 py-3 fw-bold">Kembali</a>
                <button type="submit" class="btn btn-primary w-50 py-3 fw-bold shadow-sm">
                    <i class="bi bi-cloud-upload me-2"></i> Unggah
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
