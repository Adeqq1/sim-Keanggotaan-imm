<x-app-layout>
    <x-slot name="header">
        Unggah Arsip
    </x-slot>

    <div class="card p-4 mb-4">
        <h6 class="fw-bold mb-3">Unggah Arsip Baru</h6>
        <form action="{{ route('admin.arsip.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-bold">Anggota</label>
                <select name="anggota_id" class="form-select @error('anggota_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($anggotas as $anggota)
                        <option value="{{ $anggota->id }}" @selected(old('anggota_id') == $anggota->id)>{{ $anggota->nama_lengkap }}</option>
                    @endforeach
                </select>
                @error('anggota_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Judul Dokumen</label>
                <input type="text" name="judul_dokumen" class="form-control @error('judul_dokumen') is-invalid @enderror" placeholder="Contoh: SK Pelantikan 2024" value="{{ old('judul_dokumen') }}" required>
                @error('judul_dokumen')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label small fw-bold">Nomor Dokumen</label>
                    <input type="text" name="nomor_dokumen" class="form-control @error('nomor_dokumen') is-invalid @enderror" placeholder="Opsional" value="{{ old('nomor_dokumen') }}">
                    @error('nomor_dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label small fw-bold">Kategori</label>
                    <select name="kategori_arsip" class="form-select @error('kategori_arsip') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori as $value => $label)
                            <option value="{{ $value }}" @selected(old('kategori_arsip') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('kategori_arsip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">File Dokumen</label>
                <input type="file" name="file_arsip" class="form-control @error('file_arsip') is-invalid @enderror" required>
                @error('file_arsip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted d-block mt-1">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Maksimal 10MB.</small>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.arsip.index') }}" class="btn btn-light w-50 py-2 fw-bold">Kembali</a>
                <button type="submit" class="btn btn-primary w-50 py-2 fw-bold">Unggah</button>
            </div>
        </form>
    </div>
</x-app-layout>
