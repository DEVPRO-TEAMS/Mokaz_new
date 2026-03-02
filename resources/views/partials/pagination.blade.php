@if ($apparts->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($apparts->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <button type="button" class="page-link" data-page="{{ $apparts->currentPage() - 1 }}">&laquo;</button>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($apparts->getUrlRange(1, $apparts->lastPage()) as $page => $url)
                @if ($page == $apparts->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" class="page-link" data-page="{{ $page }}">{{ $page }}</button>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($apparts->hasMorePages())
                <li class="page-item">
                    <button type="button" class="page-link" data-page="{{ $apparts->currentPage() + 1 }}">&raquo;</button>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
