@if ($apparts->hasPages())
    <nav class="d-flex justify-items-center justify-content-between">
        <div class="d-non flex-fill d-flex align-items-center justify-content-between">
            <div class="m-auto">
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($apparts->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" class="page-link" data-page="{{ $apparts->currentPage() - 1 }}" rel="prev" aria-label="Précédent">&lsaquo;</button>
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
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $i }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <button type="button" class="page-link" data-page="{{ $i }}">{{ $i }}</button>
                                </li>
                            @endif
                        {{-- Points de suspension après la première page --}}
                        @elseif ($i === 2 && $currentPage > 3)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        {{-- Points de suspension avant la dernière page --}}
                        @elseif ($i === $lastPage - 1 && $currentPage < $lastPage - 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($apparts->hasMorePages())
                        <li class="page-item">
                            <button type="button" class="page-link" data-page="{{ $apparts->currentPage() + 1 }}" rel="next" aria-label="Suivant">&rsaquo;</button>
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