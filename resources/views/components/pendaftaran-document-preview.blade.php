<div class="modal fade" id="previewDocumentModal" tabindex="-1" aria-labelledby="previewDocumentTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl preview-document-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewDocumentTitle">Preview Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewDocumentImage" class="img-fluid d-block mx-auto" style="max-height: 80vh;" alt="Preview dokumen identitas" src="">
                <div id="previewDocumentPlaceholder" class="d-none py-5">
                    <i class="bi bi-image display-4 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Gambar tidak dapat ditampilkan.</p>
                    <a id="previewDocumentDownloadFallback" href="#" class="btn btn-outline-primary btn-sm mt-3">
                        <i class="bi bi-download me-1"></i> Unduh Dokumen
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <a id="previewDocumentDownload" href="#" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i> Unduh Dokumen
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
