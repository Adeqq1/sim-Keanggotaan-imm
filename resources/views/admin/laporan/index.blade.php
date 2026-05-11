<x-app-layout>
    <x-slot name="header">
        Laporan Sistem
    </x-slot>

    <div class="card p-4">
        <h6 class="fw-bold mb-4 border-bottom pb-2">Filter Laporan</h6>

        <div class="mb-3">
            <label class="form-label small fw-bold">Jenis Laporan</label>
            <select id="jenis_laporan" class="form-select" required>
                <option value="kegiatan">Laporan Kegiatan</option>
                <option value="anggota">Laporan Anggota Baru</option>
                <option value="pendaftaran">Laporan Pendaftaran</option>
                <option value="arsip">Laporan Arsip</option>
            </select>
        </div>

        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label small fw-bold">Tanggal Mulai</label>
                <input type="date" id="tanggal_mulai" class="form-control" value="{{ date('Y-m-01') }}" required>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label small fw-bold">Tanggal Selesai</label>
                <input type="date" id="tanggal_selesai" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>

        {{-- Hidden form for PDF --}}
        <form id="formPdf" action="{{ route('admin.laporan.exportPdf') }}" method="POST" style="display:none">
            @csrf
            <input type="hidden" name="jenis_laporan" id="pdf_jenis">
            <input type="hidden" name="tanggal_mulai" id="pdf_mulai">
            <input type="hidden" name="tanggal_selesai" id="pdf_selesai">
        </form>

        {{-- Hidden form for Excel --}}
        <form id="formExcel" action="{{ route('admin.laporan.exportExcel') }}" method="POST" style="display:none">
            @csrf
            <input type="hidden" name="jenis_laporan" id="excel_jenis">
            <input type="hidden" name="tanggal_mulai" id="excel_mulai">
            <input type="hidden" name="tanggal_selesai" id="excel_selesai">
        </form>

        <div class="d-grid gap-3 mt-4">
            <button type="button" onclick="submitForm('pdf')" class="btn btn-danger py-3 fw-bold">
                <i class="bi bi-file-earmark-pdf me-2"></i> Export ke PDF
            </button>
            <button type="button" onclick="submitForm('excel')" class="btn btn-success py-3 fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export ke Excel
            </button>
        </div>
    </div>

    <script>
        function submitForm(type) {
            var jenis = document.getElementById('jenis_laporan').value;
            var mulai = document.getElementById('tanggal_mulai').value;
            var selesai = document.getElementById('tanggal_selesai').value;

            if (!jenis || !mulai || !selesai) {
                alert('Harap lengkapi semua filter terlebih dahulu.');
                return;
            }

            if (mulai > selesai) {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai.');
                return;
            }

            if (type === 'pdf') {
                document.getElementById('pdf_jenis').value = jenis;
                document.getElementById('pdf_mulai').value = mulai;
                document.getElementById('pdf_selesai').value = selesai;
                document.getElementById('formPdf').submit();
            } else {
                document.getElementById('excel_jenis').value = jenis;
                document.getElementById('excel_mulai').value = mulai;
                document.getElementById('excel_selesai').value = selesai;
                document.getElementById('formExcel').submit();
            }
        }
    </script>
</x-app-layout>
