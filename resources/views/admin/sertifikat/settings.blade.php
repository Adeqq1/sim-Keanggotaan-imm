<x-app-layout>
    <x-slot name="header">
        Pengaturan Background Sertifikat
    </x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0">Atur Background E-Sertifikat</h6>
        <a href="{{ route('admin.sertifikat.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card p-4">
                <h6 class="fw-bold mb-4 border-bottom pb-2">Konfigurasi Sertifikat</h6>
                <form action="{{ route('admin.sertifikat.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input @error('use_background') is-invalid @enderror" type="checkbox" name="use_background" id="use_background" value="1" {{ $useBackground ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold small" for="use_background">Gunakan Gambar Background pada Sertifikat</label>
                        <div class="form-text text-muted small">Jika dinonaktifkan, sertifikat akan di-generate dengan latar belakang putih polos.</div>
                        @error('use_background') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Unggah/Ganti File Gambar Background</label>
                        <input type="file" name="background_image" class="form-control @error('background_image') is-invalid @enderror" accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text text-muted small mt-1">
                            * Opsional. Kosongkan jika hanya ingin mengaktifkan/menonaktifkan background.<br>
                            * Format yang diterima: JPG, JPEG, PNG. Max: 4MB.<br>
                            * Sistem akan secara otomatis memotong (crop) dan menyesuaikan rasio gambar ke standar <strong>A4 Landscape (1122 x 794 piksel)</strong> agar posisi teks tetap rapi.
                        </div>
                        @error('background_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-3 fw-bold">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-4">
                <h6 class="fw-bold mb-4 border-bottom pb-2">Background Saat Ini (Preview)</h6>
                @if($bgExists)
                    <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm border mb-2" style="background-color: #f8f9fa;">
                        <img src="{{ asset($bgPath) }}?t={{ time() }}" alt="Current Background" style="object-fit: contain; width: 100%; height: 100%;">
                    </div>
                    <small class="text-muted text-center d-block">Preview background aktif saat ini</small>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center border border-dashed rounded py-5 bg-light">
                        <i class="bi bi-image display-4 text-muted mb-2"></i>
                        <span class="text-muted small">Belum ada background sertifikat yang diatur</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
