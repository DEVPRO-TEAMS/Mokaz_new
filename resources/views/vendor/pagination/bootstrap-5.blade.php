@if ($paginator->hasPages())
    <nav class="d-flex justify-items-center justify-content-between">
        {{-- Version Mobile (XS) : uniquement Précédent / Suivant --}}
        {{-- <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination">
                 Previous Page Link 
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.previous')</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
                    </li>
                @endif

                Next Page Link
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">@lang('pagination.next')</span>
                    </li>
                @endif
            </ul>
        </div> --}}

        {{-- Version Desktop (SM et +) --}}
        <div class="d-non flex-fill d-flex align-items-center justify-content-between">
            <div class="m-auto">
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Pages numérotées avec logique "..." --}}
                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                    @endphp

                    @for ($i = 1; $i <= $lastPage; $i++)
                        {{-- Toujours afficher la 1ère et la dernière page --}}
                        @if ($i === 1 || $i === $lastPage || ($i >= $currentPage - 1 && $i <= $currentPage + 1))
                            @if ($i == $currentPage)
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $i }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
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
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif

