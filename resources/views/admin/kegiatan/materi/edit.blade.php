<x-app-layout>
    <x-slot name="header">Ubah Materi</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.kegiatan.materi-kegiatan.index', $kegiatan) }}" class="btn btn-outline-secondary btn-ui btn-ui-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.kegiatan.materi-kegiatan.update', [$kegiatan, $materiKegiatan]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="judul" class="form-label fw-bold">Judul <span class="text-danger">*</span></label>
                    <input id="judul" type="text" name="judul" value="{{ old('judul', $materiKegiatan->judul) }}" class="form-control @error('judul') is-invalid @enderror" maxlength="255" required>
                    @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-bold">Deskripsi <span class="text-danger">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="5" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $materiKegiatan->deskripsi) }}</textarea>
                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label for="file_materi" class="form-label fw-bold">Ganti File Materi</label>
                    <input id="file_materi" type="file" name="file_materi" class="form-control @error('file_materi') is-invalid @enderror" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                    <small class="text-muted d-block">File sekarang: {{ basename($materiKegiatan->file_materi) }}. Biarkan kosong jika tidak ingin mengganti file.</small>
                    <small class="text-muted">PDF, DOC, DOCX, PPT, PPTX, XLS, atau XLSX. Maksimal 2MB.</small>
                    @error('file_materi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-ui"><i class="bi bi-save"></i> Simpan Perubahan</button>
                    <a href="{{ route('admin.kegiatan.materi-kegiatan.index', $kegiatan) }}" class="btn btn-outline-secondary btn-ui">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
