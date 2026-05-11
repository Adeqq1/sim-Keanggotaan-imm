@if(session('success'))
    <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x mb-5 p-3" style="z-index: 1060;">
        <div class="toast show align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x mb-5 p-3" style="z-index: 1060;">
        <div class="toast show align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<script>
    // Auto hide toasts after 3 seconds
    document.querySelectorAll('.toast').forEach(function(toastNode) {
        setTimeout(function() {
            var toast = bootstrap.Toast.getOrCreateInstance(toastNode);
            toast.hide();
        }, 3000);
    });
</script>
