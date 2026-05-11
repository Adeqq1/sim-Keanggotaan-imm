@if ($paginator->hasPages())
    <nav class="d-flex justify-content-center mt-4 mb-2">
        <ul class="pagination pagination-sm shadow-sm" style="border-radius: 10px; overflow: hidden; border: none;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link border-0 py-2 px-3 bg-white text-muted"><i class="bi bi-chevron-left"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 py-2 px-3 bg-white text-primary" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i></a>
                </li>
            @endif

            {{-- Page Indicator --}}
            <li class="page-item">
                <span class="page-link border-0 py-2 px-3 bg-white text-dark fw-bold">
                    {{ $paginator->currentPage() }} <span class="text-muted fw-normal">/</span> {{ $paginator->lastPage() }}
                </span>
            </li>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 py-2 px-3 bg-white text-primary" href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="bi bi-chevron-right"></i></a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link border-0 py-2 px-3 bg-white text-muted"><i class="bi bi-chevron-right"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif
