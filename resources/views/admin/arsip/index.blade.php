<x-app-layout>
    <x-slot name="header">
        Arsip Dokumen
    </x-slot>

    <div class="card p-4 mb-4">
        <h6 class="fw-bold mb-3">Unggah Arsip Baru</h6>
        <form action="{{ route('admin.arsip.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Judul Dokumen</label>
                <input type="text" name="judul_dokumen" class="form-control" placeholder="Contoh: SK Pelantikan 2024" required>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold">Nomor Dokumen</label>
                    <input type="text" name="nomor_dokumen" class="form-control" placeholder="001/SK/..." required>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold">Kategori</label>
                    <select name="kategori_arsip" class="form-select" required>
                        <option value="surat_keputusan">SK</option>
                        <option value="surat_masuk">Surat Masuk</option>
                        <option value="surat_keluar">Surat Keluar</option>
                        <option value="laporan">Laporan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">File (PDF/JPG/PNG)</label>
                <input type="file" name="file_arsip" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Unggah Dokumen</button>
        </form>
    </div>

    <h6 class="fw-bold mb-3">Daftar Arsip</h6>
    @forelse($arsips as $arsip)
        <div class="card mb-3 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-light rounded p-2 me-3 text-center" style="min-width: 45px;">
                    <i class="bi bi-file-earmark-text fs-3 text-primary"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold mb-1 text-truncate">{{ $arsip->judul_dokumen }}</h6>
                    <small class="text-muted d-block text-truncate">No: {{ $arsip->nomor_dokumen }}</small>
                    <span class="badge bg-light text-dark fw-normal" style="font-size: 0.7rem;">{{ ucfirst(str_replace('_', ' ', $arsip->kategori_arsip)) }}</span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('admin.arsip.download', $arsip) }}"><i class="bi bi-download me-2 text-success"></i> Download</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item py-2 text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $arsip->id }}">
                                <i class="bi bi-trash me-2"></i> Hapus
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <x-_modal-delete 
            id="deleteModal{{ $arsip->id }}" 
            :action="route('admin.arsip.destroy', $arsip)" 
            message="Data arsip yang dihapus tidak dapat dikembalikan."
        />
    @empty
        <div class="text-center py-5">
            <i class="bi bi-folder-x display-4 text-muted"></i>
            <p class="text-muted mt-2">Belum ada arsip yang diunggah.</p>
        </div>
    @endforelse

    {{ $arsips->links('components.pagination') }}
</x-app-layout>
