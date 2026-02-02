<!-- Modal détails réservation -->
<div class="modal fade" id="showReservationModal{{ $reservation->id }}" tabindex="-1" aria-labelledby="showReservationModalLabel{{ $reservation->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <div class="d-flex align-items-center">
                    <div class="reservation-avatar me-3">
                        <div class="avatar-initials bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center rounded-2" 
                             style="width: 40px; height: 40px;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="showReservationModalLabel{{ $reservation->id }}">
                            {{ $reservation->nom ?? 'Client' }} {{ $reservation->prenoms ?? '' }}
                        </h5>
                        <small class="text-muted">Réservation #{{ $reservation->id }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Informations client -->
                    <div class="col-md-6 mb-4">
                        <h6 class="fw-semibold mb-3">
                            <i class="fas fa-user me-2 text-primary"></i>Informations Client
                        </h6>
                        <div class="bg-light rounded-3 p-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="info-item">
                                        <label class="form-label fw-semibold text-muted mb-1">
                                            <i class="fas fa-user me-2"></i>Nom complet
                                        </label>
                                        <p class="mb-0">{{ $reservation->nom ?? 'Non renseigné' }} {{ $reservation->prenoms ?? '' }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="info-item">
                                        <label class="form-label fw-semibold text-muted mb-1">
                                            <i class="fas fa-envelope me-2"></i>Email
                                        </label>
                                        <p class="mb-0">
                                            @if($reservation->email)
                                                <a href="mailto:{{ $reservation->email }}" class="text-primary text-decoration-none">
                                                    {{ $reservation->email }}
                                                </a>
                                            @else
                                                Non renseigné
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="info-item">
                                        <label class="form-label fw-semibold text-muted mb-1">
                                            <i class="fas fa-phone me-2"></i>Téléphone
                                        </label>
                                        <p class="mb-0">
                                            @if($reservation->phone)
                                                <a href="tel:{{ $reservation->phone }}" class="text-primary text-decoration-none">
                                                    {{ $reservation->phone }}
                                                </a>
                                            @else
                                                Non renseigné
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations réservation -->
                    <div class="col-md-6 mb-4">
                        <h6 class="fw-semibold mb-3">
                            <i class="fas fa-calendar-alt me-2 text-success"></i>Détails Réservation
                        </h6>
                        <div class="bg-light rounded-3 p-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="info-item">
                                        <label class="form-label fw-semibold text-muted mb-1">
                                            <i class="fas fa-home me-2"></i>Bien réservé
                                        </label>
                                        <p class="mb-0">
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ $reservation->room_id ?? 'Non assigné' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="info-item">
                                        <label class="form-label fw-semibold text-muted mb-1">
                                            <i class="fas fa-toggle-on me-2"></i>Statut
                                        </label>
                                        <p class="mb-0">
                                            @if ($reservation->status == 'confirmé')
                                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                                    <i class="fas fa-check-circle me-1"></i> Confirmée
                                                </span>
                                            @elseif ($reservation->status == 'annulé')
                                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Annulée
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning">
                                                    <i class="fas fa-clock me-1"></i> En attente
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="info-item">
                                        <label class="form-label fw-semibold text-muted mb-1">
                                            <i class="fas fa-credit-card me-2"></i>Statut paiement
                                        </label>
                                        <p class="mb-0">
                                            @if ($reservation->statut_paiement == 'payé')
                                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                                    <i class="fas fa-check-circle me-1"></i> Payé
                                                </span>
                                            @elseif ($reservation->statut_paiement == 'non payé')
                                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Non payé
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning">
                                                    <i class="fas fa-clock me-1"></i> En attente
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Dates et durée -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3">
                            <i class="fas fa-calendar-week me-2 text-warning"></i>Période de réservation
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold text-muted mb-1">Date de début</label>
                            <p class="mb-0">
                                @if($reservation->start_time)
                                    {{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y à H:i') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($reservation->start_time)->diffForHumans() }}</small>
                                @else
                                    Non renseigné
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold text-muted mb-1">Date de fin</label>
                            <p class="mb-0">
                                @if($reservation->end_time)
                                    {{ \Carbon\Carbon::parse($reservation->end_time)->format('d/m/Y à H:i') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($reservation->end_time)->diffForHumans() }}</small>
                                @else
                                    Non renseigné
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Informations financières -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3">
                            <i class="fas fa-money-bill-wave me-2 text-success"></i>Informations financières
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold text-muted mb-1">Prix unitaire</label>
                            <p class="mb-0">
                                @if($reservation->unit_price)
                                    <span class="badge bg-success bg-opacity-10 text-success fs-6">
                                        <i class="fas fa-euro-sign me-1"></i>
                                        {{ number_format($reservation->unit_price, 2, ',', ' ') }} €
                                    </span>
                                @else
                                    Non renseigné
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold text-muted mb-1">Prix total</label>
                            <p class="mb-0">
                                @if($reservation->total_price)
                                    <span class="badge bg-primary bg-opacity-10 text-primary fs-6">
                                        <i class="fas fa-euro-sign me-1"></i>
                                        {{ number_format($reservation->total_price, 2, ',', ' ') }} €
                                    </span>
                                @else
                                    Non renseigné
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Notes -->
                @if($reservation->notes)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3">
                            <i class="fas fa-sticky-note me-2 text-info"></i>Notes
                        </h6>
                        <div class="bg-light rounded-3 p-3">
                            <p class="mb-0">{!! $reservation->notes !!}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Informations de traitement -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3">
                            <i class="fas fa-info-circle me-2 text-info"></i>Informations de suivi
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold text-muted mb-1">Créé le</label>
                            <p class="mb-0">
                                {{ $reservation->created_at->format('d/m/Y à H:i') ?? 'Non renseigné' }}
                                <br>
                                <small class="text-muted">{{ $reservation->created_at->diffForHumans() ?? '' }}</small>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold text-muted mb-1">Traité par</label>
                            <p class="mb-0">{{ $reservation->traited_by ?? 'Non renseigné' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold text-muted mb-1">Traité le</label>
                            <p class="mb-0">
                                @if($reservation->traited_at)
                                    {{ \Carbon\Carbon::parse($reservation->traited_at)->format('d/m/Y à H:i') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($reservation->traited_at)->diffForHumans() }}</small>
                                @else
                                    Non traité
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0 bg-light">
                <div class="d-flex justify-content-between w-100">
                    <div>
                        @if($reservation->status == 'en attente')
                            <button type="button" class="btn btn-success me-2" onclick="confirmReservation({{ $reservation->id }})">
                                <i class="fas fa-check me-1"></i> Confirmer
                            </button>
                            <button type="button" class="btn btn-danger" onclick="cancelReservation({{ $reservation->id }})">
                                <i class="fas fa-times me-1"></i> Annuler
                            </button>
                        @endif
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary me-2" onclick="editReservation({{ $reservation->id }})">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>