@props(['id', 'action', 'message' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.'])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-3">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-danger display-4"></i>
                </div>
                <h5 class="fw-bold mb-2">Konfirmasi Hapus</h5>
                <p class="text-muted small mb-4">{{ $message }}</p>
                
                <div class="d-grid gap-2">
                    <form action="{{ $action }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold" style="border-radius: 10px;">Ya, Hapus Data</button>
                    </form>
                    <button type="button" class="btn btn-light w-100 py-2 fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>
