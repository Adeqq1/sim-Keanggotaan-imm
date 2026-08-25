<x-app-layout>
    <x-slot name="header">
        Ubah Kegiatan
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.kegiatan.update', $kegiatan) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required>
                    @error('nama_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal & Waktu <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="tanggal_waktu" class="form-control @error('tanggal_waktu') is-invalid @enderror" value="{{ old('tanggal_waktu', $kegiatan->tanggal_waktu->format('Y-m-d\TH:i')) }}" required>
                    @error('tanggal_waktu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Angkatan yang Berhak Mengikuti <span class="text-danger">*</span></label>
                    @php($targetTahun = old('tahun_angkatan', $kegiatan->tahunAngkatans->pluck('tahun_daftar')->all()))
                    <div class="row g-2">
                        @for($tahun = 2016; $tahun <= now()->year; $tahun++)
                            <div class="col-6 col-sm-4 col-md-3"><label class="activity-year-option"><input class="activity-year-option__input" type="checkbox" name="tahun_angkatan[]" value="{{ $tahun }}" @checked(in_array($tahun, $targetTahun))><span>{{ $tahun }}</span></label></div>
                        @endfor
                    </div>
                    <small class="text-muted">Hanya anggota aktif dari tahun yang dipilih yang muncul pada presensi.</small>
                    @error('tahun_angkatan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('tahun_angkatan.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Lokasi <span class="text-danger">*</span></label>
                    <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi', $kegiatan->lokasi) }}" required>
                    @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jenis Pelaksanaan <span class="text-danger">*</span></label>
                        <select name="jenis_pelaksanaan" class="form-select @error('jenis_pelaksanaan') is-invalid @enderror" required>
                            <option value="satu_sesi" @selected(old('jenis_pelaksanaan', $kegiatan->jenis_pelaksanaan) === 'satu_sesi')>Satu sesi</option>
                            <option value="multi_sesi" @selected(old('jenis_pelaksanaan', $kegiatan->jenis_pelaksanaan) === 'multi_sesi')>Multi-sesi</option>
                        </select>
                        @error('jenis_pelaksanaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Minimum sesi terverifikasi <span class="text-danger">*</span></label>
                        <input type="number" name="minimum_sesi_terverifikasi" min="1" max="255" value="{{ old('minimum_sesi_terverifikasi', $kegiatan->minimum_sesi_terverifikasi) }}" class="form-control @error('minimum_sesi_terverifikasi') is-invalid @enderror" required>
                        @error('minimum_sesi_terverifikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Thumbnail Kegiatan</label>
                    @if(filled($kegiatan->thumbnail))
                        <div class="mb-2">
                            <img src="{{ $kegiatan->thumbnail_url }}" alt="Gambar mini kegiatan" class="img-thumbnail" style="max-height: 150px;" onerror="this.onerror=null; this.src='{{ asset('images/placeholder-kegiatan.png') }}';">
                        </div>
                    @endif
                    <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">Format: jpeg, png, jpg. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</small>
                    @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-ui">
                        <i class="bi bi-save"></i> Perbarui
                    </button>
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-ui">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
