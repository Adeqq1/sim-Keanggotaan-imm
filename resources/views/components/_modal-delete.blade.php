@props(['id', 'action', 'message' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.'])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Title" aria-describedby="{{ $id }}Description" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable delete-confirmation-dialog">
        <div class="modal-content delete-confirmation-modal">
            <div class="modal-header delete-confirmation-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="delete-confirmation-icon" aria-hidden="true"><i class="bi bi-exclamation-triangle"></i></span>
                    <h5 class="modal-title fw-bold mb-0" id="{{ $id }}Title">Konfirmasi Hapus</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body delete-confirmation-body">
                <p class="mb-0" id="{{ $id }}Description">{{ $message }}</p>
            </div>
            <div class="modal-footer delete-confirmation-footer">
                <button type="button" class="btn btn-outline-secondary btn-ui btn-ui-sm" data-bs-dismiss="modal">Batal</button>
                <form action="{{ $action }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-ui btn-ui-sm">Hapus Data</button>
                </form>
            </div>
        </div>
    </div>
</div>
