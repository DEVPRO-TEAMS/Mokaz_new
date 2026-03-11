@if ($apparts->hasPages())

<nav>
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($apparts->onFirstPage())
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">&lsaquo;</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $apparts->previousPageUrl() }}" rel="prev">&lsaquo;</a>
            </li>
        @endif

        {{-- Pages numérotées avec logique "..." --}}
        @php
            $currentPage = $apparts->currentPage();
            $lastPage = $apparts->lastPage();
        @endphp

        @for ($i = 1; $i <= $lastPage; $i++)
            {{-- Toujours afficher la 1ère et la dernière page --}}
            @if ($i === 1 || $i === $lastPage || ($i >= $currentPage - 1 && $i <= $currentPage + 1))
                @if ($i == $currentPage)
                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $i }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $apparts->url($i) }}">{{ $i }}</a></li>
                @endif
            {{-- Points de suspension après la première page --}}
            @elseif ($i === 2 && $currentPage > 3)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            {{-- Points de suspension avant la dernière page --}}
            @elseif ($i === $lastPage - 1 && $currentPage < $lastPage - 2)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif
        @endfor

        {{-- Next Page Link --}}
        @if ($apparts->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $apparts->nextPageUrl() }}" rel="next">&rsaquo;</a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">&rsaquo;</span>
            </li>
        @endif
    </ul>
        
</nav>
@endif