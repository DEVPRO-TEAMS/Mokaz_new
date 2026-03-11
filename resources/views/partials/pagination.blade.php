@if ($apparts->hasPages())
    <nav class="d-flex justify-items-center justify-content-between">
        <div class="d-flex flex-fill align-items-center justify-content-between">
            <div class="m-auto">
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($apparts->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" class="page-link pagination-btn" data-page="{{ $apparts->currentPage() - 1 }}" rel="prev" aria-label="Précédent">&lsaquo;</button>
                        </li>
                    @endif

                    {{-- Pages numérotées avec logique "..." --}}
                    @php
                        $currentPage = $apparts->currentPage();
                        $lastPage = $apparts->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                        
                        if ($start <= 3) {
                            $start = 1;
                            $end = min($lastPage, 5);
                        }
                        
                        if ($end >= $lastPage - 2) {
                            $start = max(1, $lastPage - 4);
                            $end = $lastPage;
                        }
                    @endphp

                    {{-- Première page --}}
                    @if ($start > 1)
                        <li class="page-item">
                            <button type="button" class="page-link pagination-btn" data-page="1">1</button>
                        </li>
                        @if ($start > 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif

                    {{-- Pages autour de la page courante --}}
                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $currentPage)
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $i }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <button type="button" class="page-link pagination-btn" data-page="{{ $i }}">{{ $i }}</button>
                            </li>
                        @endif
                    @endfor

                    {{-- Dernière page --}}
                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <button type="button" class="page-link pagination-btn" data-page="{{ $lastPage }}">{{ $lastPage }}</button>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($apparts->hasMorePages())
                        <li class="page-item">
                            <button type="button" class="page-link pagination-btn" data-page="{{ $apparts->currentPage() + 1 }}" rel="next" aria-label="Suivant">&rsaquo;</button>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif