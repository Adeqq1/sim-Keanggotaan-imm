<x-app-layout>
    <x-slot name="header">
        Arsip Dokumen
    </x-slot>

    <div class="card p-4 mb-4 border-0 shadow-sm" style="border-radius: 15px;">
        <h6 class="fw-bold mb-3">Kirim Dokumen Baru</h6>
        <p class="text-muted small">Unggah dokumen pendukung (SK, Surat, Laporan, dll) ke dalam arsip sistem.</p>
        <form action="{{ route('kader.arsip.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Judul Dokumen</label>
                <input type="text" name="judul_dokumen" class="form-control bg-light border-0" placeholder="Contoh: Laporan Kegiatan Perkaderan" required>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold">Kategori</label>
                    <select name="kategori_arsip" class="form-select bg-light border-0" required>
                        <option value="laporan">Laporan</option>
                        <option value="surat_masuk">Surat Masuk</option>
                        <option value="surat_keluar">Surat Keluar</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold">No. Dokumen</label>
                    <input type="text" name="nomor_dokumen" class="form-control bg-light border-0" placeholder="Opsional">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Pilih File (PDF/Gambar)</label>
                <input type="file" name="file_arsip" class="form-control bg-light border-0" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                <i class="bi bi-cloud-upload me-2"></i> Unggah Sekarang
            </button>
        </form>
    </div>

    <h6 class="fw-bold mb-3">Daftar Arsip</h6>
    @forelse($arsips as $arsip)
        <div class="card mb-3 p-3 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary" style="width: 45px; height: 45px;">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold mb-1 text-truncate" style="font-size: 0.9rem;">{{ $arsip->judul_dokumen }}</h6>
                    <small class="text-muted d-block text-truncate" style="font-size: 0.75rem;">No: {{ $arsip->nomor_dokumen ?? '-' }}</small>
                    <span class="badge bg-light text-dark fw-normal mt-1" style="font-size: 0.65rem;">{{ ucfirst(str_replace('_', ' ', $arsip->kategori_arsip)) }}</span>
                </div>
                <a href="{{ route('kader.arsip.download', $arsip) }}" class="btn btn-link text-primary p-0">
                    <i class="bi bi-download fs-4"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="bi bi-folder-x display-4 text-muted opacity-25"></i>
            <p class="text-muted mt-2 small">Belum ada arsip dokumen.</p>
        </div>
    @endforelse

    {{ $arsips->links('components.pagination') }}
    <div class="pb-3"></div>
</x-app-layout>
