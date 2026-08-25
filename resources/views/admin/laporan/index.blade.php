<x-app-layout>
    <x-slot name="header">
        Laporan Sistem
    </x-slot>

    <div class="laporan-page-layout">
        <div class="card p-4 laporan-panel laporan-filter-panel">
            <div class="laporan-panel-heading">
                <span class="laporan-panel-icon"><i class="bi bi-sliders2"></i></span>
                <div>
                    <h6 class="fw-bold mb-1">Filter Laporan</h6>
                    <p class="text-muted small mb-0">Tentukan data dan periode yang ingin Anda unduh.</p>
                </div>
            </div>

            <form id="laporan-filter-form" action="{{ route('admin.laporan.exportPdf') }}" method="POST" data-date-range-form>
                @csrf

                <div class="laporan-form-section">
                    <span class="laporan-form-section-label">Data laporan</span>
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
                </div>

                <div class="laporan-form-section">
                    <span class="laporan-form-section-label">Periode laporan</span>
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
                </div>

                <div class="d-grid gap-3 mt-4 laporan-mobile-export-actions">
                    <button type="submit" class="btn btn-outline-primary btn-ui py-3">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Export ke PDF
                    </button>
                    <button type="submit" formaction="{{ route('admin.laporan.exportExcel') }}" class="btn btn-outline-success btn-ui py-3">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export ke Excel
                    </button>
                </div>
            </form>
        </div>

        <aside class="card p-4 laporan-panel laporan-export-panel">
            <div class="laporan-panel-heading">
                <span class="laporan-panel-icon"><i class="bi bi-download"></i></span>
                <div>
                    <h6 class="fw-bold mb-1">Format Export</h6>
                    <p class="text-muted small mb-0">Gunakan filter di sebelah kiri.</p>
                </div>
            </div>

            <div class="laporan-export-list">
                <button type="submit" form="laporan-filter-form" class="laporan-export-option laporan-export-option--pdf">
                    <span class="laporan-export-option-icon"><i class="bi bi-file-earmark-pdf"></i></span>
                    <span class="laporan-export-option-copy">
                        <strong>Export PDF</strong>
                        <small>Dokumen siap dibagikan atau dicetak</small>
                    </span>
                    <i class="bi bi-arrow-right laporan-export-option-arrow"></i>
                </button>
                <button type="submit" form="laporan-filter-form" formaction="{{ route('admin.laporan.exportExcel') }}" class="laporan-export-option laporan-export-option--excel">
                    <span class="laporan-export-option-icon"><i class="bi bi-file-earmark-spreadsheet"></i></span>
                    <span class="laporan-export-option-copy">
                        <strong>Export Excel</strong>
                        <small>Data mudah diolah kembali</small>
                    </span>
                    <i class="bi bi-arrow-right laporan-export-option-arrow"></i>
                </button>
            </div>
        </aside>
    </div>
</x-app-layout>
