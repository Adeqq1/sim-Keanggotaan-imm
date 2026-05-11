<x-app-layout>
    <x-slot name="header">
        Laporan Sistem
    </x-slot>

    <div class="card p-4">
        <h6 class="fw-bold mb-4 border-bottom pb-2">Filter Laporan</h6>
        
        <form action="{{ route('admin.laporan.exportPdf') }}" method="POST" id="laporanForm">
            @csrf
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Jenis Laporan</label>
                <select name="jenis_laporan" class="form-select @error('jenis_laporan') is-invalid @enderror" required>
                    <option value="kegiatan">Laporan Kegiatan</option>
                    <option value="anggota">Laporan Anggota Baru</option>
                    <option value="pendaftaran">Laporan Pendaftaran</option>
                    <option value="arsip">Laporan Arsip</option>
                </select>
                @error('jenis_laporan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ date('Y-m-01') }}" required>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="d-grid gap-3 mt-4">
                <button type="submit" class="btn btn-danger py-3 fw-bold">
                    <i class="bi bi-file-earmark-pdf me-2"></i> Export ke PDF
                </button>
                <button type="button" onclick="exportExcel()" class="btn btn-success py-3 fw-bold">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export ke Excel
                </button>
            </div>
        </form>
    </div>

    <script>
        function exportExcel() {
            var form = document.getElementById('laporanForm');
            var originalAction = form.action;
            form.action = "{{ route('admin.laporan.exportExcel') }}";
            form.submit();
            form.action = originalAction;
        }
    </script>
</x-app-layout>
