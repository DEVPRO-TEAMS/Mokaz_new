<!-- MODAL Bootstrap simplifiée -->
<div class="modal fade" id="showCommentModal{{ $item->uuid }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-light">
                {{-- <h5 class="modal-title text-white">
                    <i class="bi bi-building text-white"></i> {{ $apartement->title ?? '' }}
                    <span class="badge bg-secondary">{{ $apartement->code ?? '' }}</span>
                </h5> --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <!-- Description -->
                <div class="pt-4">
                    <h6 class="text-danger"><i class="bi bi-align-start"></i> Commentaires</h6>
                    <div class="bg-white border border-danger rounded-3 p-3">
                        <p class="text-muted">
                            {!! $item->comment ?? '' !!}
                        </p>
                    </div>
                </div>
                <!-- Notes -->
                <!-- Notes -->
                <div class="pt-4">
                    <h6 class="text-danger"><i class="bi bi-star"></i> Notes</h6>
                    <div class="py-4 bg-white border border-danger rounded-3 p-3">
                        <ul class="list-inline m-0">
                            @php
                                $rating = (int) $item->rating ?? 0;
                            @endphp

                            @for ($i = 0; $i < 5; $i++)
                                <li class="list-inline-item">
                                    <i
                                        class="bi bi-star-fill fs-3 {{ $i < $rating ? 'text-warning' : 'text-secondary' }}"></i>
                                </li>
                            @endfor
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer btn-container">
                {{-- @if (Auth::user()->user_type == 'partner') --}}
                    @if ($item->etat == 'pending')
                        <button class="btn btn-success me-2">
                            <a class="deleteConfirmation text-white" data-uuid="{{ $item->uuid }}"
                                data-type="confirmation_redirect" data-placement="top" data-token="{{ csrf_token() }}"
                                data-url="{{ route('partner.approveComment', $item->uuid) }}"
                                data-title="Vous êtes sur le point d'activer ce commentaire"
                                data-id="{{ $item->uuid }}" data-param="0"
                                data-route="{{ route('partner.approveComment', $item->uuid) }}" title="Approuver">
                                <i class="fas fa-check" style="cursor: pointer"></i> Activer</a>
                        </button>

                        <button class="btn btn-danger">
                            <a class="deleteConfirmation  text-white" data-uuid="{{ $item->uuid }}"
                                data-type="confirmation_redirect" data-placement="top"
                                data-token="{{ csrf_token() }}"
                                data-url="{{ route('partner.rejectComment', $item->uuid) }}"
                                data-title="Vous êtes sur le point de rejeter ce commentaire"
                                data-id="{{ $item->uuid }}" data-param="0"
                                data-route="{{ route('partner.rejectComment', $item->uuid) }}" title="Rejeter">
                                <i class="fas fa-times" style="cursor: pointer"></i> Rejeter</a>
                        </button>
                    @endif
                
                {{-- @endif --}}
            </div>

        </div>
    </div>
</div>
