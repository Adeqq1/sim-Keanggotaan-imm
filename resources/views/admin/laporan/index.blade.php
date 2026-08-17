<x-app-layout>
    <x-slot name="header">
        Laporan Sistem
    </x-slot>

    <div class="card p-4">
        <h6 class="fw-bold mb-4 border-bottom pb-2">Filter Laporan</h6>

        <form action="{{ route('admin.laporan.exportPdf') }}" method="POST" data-date-range-form>
            @csrf

            <div class="mb-3">
                <label for="jenis_laporan" class="form-label small fw-bold">Jenis Laporan</label>
                <select id="jenis_laporan" name="jenis_laporan" class="form-select @error('jenis_laporan') is-invalid @enderror" required>
                    <option value="kegiatan">Laporan Kegiatan</option>
                    <option value="anggota">Laporan Anggota Baru</option>
                    <option value="pendaftaran">Laporan Pendaftaran</option>
                    <option value="arsip">Laporan Arsip</option>
                </select>
                @error('jenis_laporan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-12 col-sm-6 mb-3">
                    <label for="tanggal_mulai" class="form-label small fw-bold">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" data-date-start class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', date('Y-m-01')) }}" required>
                    @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-sm-6 mb-3">
                    <label for="tanggal_selesai" class="form-label small fw-bold">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" data-date-end class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai', date('Y-m-d')) }}" required>
                    @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-grid gap-3 mt-4">
                <button type="submit" class="btn btn-outline-primary btn-ui py-3">
                    <i class="bi bi-file-earmark-pdf me-2"></i> Export ke PDF
                </button>
                <button type="submit" formaction="{{ route('admin.laporan.exportExcel') }}" class="btn btn-outline-success btn-ui py-3">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export ke Excel
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
