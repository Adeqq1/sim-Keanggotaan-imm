<x-app-layout>
    <x-slot name="header">
        Tambah Kegiatan
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan') }}" placeholder="Contoh: Rapat Bulanan" required>
                    @error('nama_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal & Waktu <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="tanggal_waktu" class="form-control @error('tanggal_waktu') is-invalid @enderror" value="{{ old('tanggal_waktu') }}" required>
                    @error('tanggal_waktu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Lokasi <span class="text-danger">*</span></label>
                    <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi') }}" placeholder="Contoh: Aula Gedung A" required>
                    @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jenis Pelaksanaan <span class="text-danger">*</span></label>
                        <select name="jenis_pelaksanaan" class="form-select @error('jenis_pelaksanaan') is-invalid @enderror" required>
                            <option value="">Pilih kebijakan</option>
                            <option value="satu_sesi" @selected(old('jenis_pelaksanaan') === 'satu_sesi')>Satu sesi</option>
                            <option value="multi_sesi" @selected(old('jenis_pelaksanaan') === 'multi_sesi')>Multi-sesi</option>
                        </select>
                        @error('jenis_pelaksanaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Minimum sesi terverifikasi <span class="text-danger">*</span></label>
                        <input type="number" name="minimum_sesi_terverifikasi" min="1" max="255" value="{{ old('minimum_sesi_terverifikasi', 1) }}" class="form-control @error('minimum_sesi_terverifikasi') is-invalid @enderror" required>
                        @error('minimum_sesi_terverifikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Deskripsi kegiatan (opsional)">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Thumbnail Kegiatan</label>
                    <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">Format: jpeg, png, jpg. Maksimal 2MB. (Opsional)</small>
                    @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-ui">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-ui">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
