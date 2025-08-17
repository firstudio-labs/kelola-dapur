@if ($paginator->hasPages())
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="dataTables_info" role="status" aria-live="polite">
                Menampilkan {{ $paginator->firstItem() ?? 0 }} sampai {{ $paginator->lastItem() ?? 0 }} 
                dari {{ $paginator->total() }} entri
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination justify-content-end">
                    {{-- First Page Link --}}
                    @if($paginator->currentPage() > 3)
                        <li class="paginate_button page-item">
                            <a href="{{ $paginator->url(1) }}" class="page-link" aria-label="First">
                                <i class="bx bx-chevrons-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="paginate_button page-item previous disabled">
                            <span class="page-link" aria-label="Previous">
                                <i class="bx bx-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="paginate_button page-item previous">
                            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev" aria-label="Previous">
                                <i class="bx bx-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="paginate_button page-item disabled">
                                <span class="page-link">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="paginate_button page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="paginate_button page-item">
                                        <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="paginate_button page-item next">
                            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next" aria-label="Next">
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="paginate_button page-item next disabled">
                            <span class="page-link" aria-label="Next">
                                <i class="bx bx-chevron-right"></i>
                            </span>
                        </li>
                    @endif

                    {{-- Last Page Link --}}
                    @if($paginator->currentPage() < $paginator->lastPage() - 2)
                        <li class="paginate_button page-item">
                            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="page-link" aria-label="Last">
                                <i class="bx bx-chevrons-right"></i>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endif