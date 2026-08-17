@if(session('success') || session('error') || session('warning') || session('info'))
    <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x mb-5 p-3" style="z-index: 1060;">
        @if(session('success'))
            <div class="toast show align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-auto-dismiss-toast>
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast show align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-auto-dismiss-toast>
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="toast show align-items-center text-dark bg-warning border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-auto-dismiss-toast>
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="toast show align-items-center text-dark bg-info border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-auto-dismiss-toast>
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-info-circle me-2"></i> {{ session('info') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endif
    </div>
@endif
