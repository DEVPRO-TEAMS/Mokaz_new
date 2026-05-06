@extends('layouts.main')
@section('content')
    <section class="flat-section pt-4 flat-property-detail">
        <div class="container">
            <!-- Header avec bouton retour amélioré -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="javascript:history.back()" class="btn-back">
                    <i class="fa fa-chevron-left"></i> 
                    <span class="ms-2">Retour</span>
                </a>
                <div class="reservation-code-badge">
                    <span class="badge-code">#{{ $reservation->code }}</span>
                </div>
            </div>

            @php
                $start = \Carbon\Carbon::parse($reservation->start_time);
                $end = \Carbon\Carbon::parse($reservation->end_time);
                $totalMinutes = $start->diffInMinutes($end);
                $limit = $start->copy()->addMinutes($totalMinutes * 0.06);
                $date_limit = $limit->format('d/m/Y à H\hi');
                $dateLimitTotal = $limit->format('d/m/Y à H:i');
                $now = now();
                $date_reservation = \Carbon\Carbon::parse($reservation->created_at);
                $isActive = $start <= $now && $end >= $now;
                $isUpcoming = $start > $now;

                // Calculer la différence en jours entre la date de réservation et la date de début
                $daysUntilStay = $date_reservation->diffInDays($start, false);
                
                // Calculer la différence en heures depuis la création de la réservation
                $hoursSinceReservation = $date_reservation->diffInHours(now(), false);
                
                // Le client peut annuler si :
                // 1. Il a réservé 7 jours ou plus avant la date d'arrivée
                // 2. OU s'il est encore dans les 24 heures suivant la réservation
                $canCancel = ($daysUntilStay >= 7) && ($hoursSinceReservation <= 24);

            @endphp

            <!-- Carte d'état de réservation -->
            <div class="reservation-status-card mb-4">
                <div class="status-header">
                    <div class="d-flex align-items-center">
                        <div class="status-icon me-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Réservation {{ $reservation->code }}</h5>
                            <p class="text-muted mb-0">{{ $reservation->property->title }}</p>
                        </div>
                    </div>
                    <div class="status-badge-container">
                        @if ($reservation->paiement->payment_status === 'paid')
                            @switch($reservation->status)
                                @case('confirmed')
                                    <span class="badge-status confirmed">
                                        <i class="fas fa-check-circle me-1"></i> Réservation Confirmée
                                    </span>
                                    @break
                                @case('pending')
                                    <span class="badge-status pending">
                                        <i class="fas fa-clock me-1"></i> En attente de confirmation
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="badge-status cancelled">
                                        <i class="fas fa-times-circle me-1"></i> Réservation Annulée
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="badge-status completed">
                                        <i class="fas fa-flag-checkered me-1"></i> Séjour Terminée
                                    </span>
                                    @break
                                @case('reconducted')
                                    <span class="badge-status reconducted">
                                        <i class="fas fa-redo me-1"></i> Reconduite
                                    </span>
                                    @break
                            @endswitch
                            
                            @if($isActive)
                                <span class="badge-status active">
                                    <i class="fas fa-play-circle me-1"></i> Séjour en cours {{ $limit->diffForHumans() }} 
                                </span>
                            @elseif($isUpcoming)
                                <span class="badge-status upcoming">
                                    <i class="fas fa-clock me-1"></i> Séjour à venir dans {{ $daysUntilStay }} jours
                                </span>
                            @endif
                        @else
                            <span class="badge-status pending">
                                <i class="fas fa-clock me-1"></i> En attente de paiement
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Grille principale -->
            <div class="row g-4">
                <!-- Colonne gauche - Détails et reçu -->
                <div class="col-lg-8">
                    <!-- Carte des détails -->
                    <div class="modern-card mb-4">
                        <div class="card-header-custom">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Détails de la réservation
                            </h6>
                        </div>
                        <div class="card-body-custom">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-user me-2"></i>Client
                                        </span>
                                        <span class="detail-value">{{ $reservation->prenoms }} {{ $reservation->nom }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-envelope me-2"></i>Email
                                        </span>
                                        <span class="detail-value">{{ $reservation->email }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-phone me-2"></i>Téléphone
                                        </span>
                                        <span class="detail-value">{{ $reservation->phone }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-home me-2"></i>Type de séjour
                                        </span>
                                        <span class="detail-value badge-type">{{ $reservation->sejour }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-calendar-alt me-2"></i>Date d'arrivée
                                        </span>
                                        <span class="detail-value">{{ $start->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-calendar-times me-2"></i>Date de départ
                                        </span>
                                        <span class="detail-value">{{ $end->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="time-info-alert mt-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-clock text-warning mt-1 me-3"></i>
                                    <div>
                                        <strong>Important :</strong> Pour garantir votre réservation, merci de vous 
                                        présenter au plus tard le <span class="text-danger fw-bold">{{ $date_limit }}</span>. 
                                        En cas de retard, votre réservation sera automatiquement annulée.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte du reçu -->
                    <div class="modern-card {{ $reservation->paiement->payment_status === 'paid' ? '' : 'd-none' }}" id="receipt-card">
                        <div class="card-header-custom">
                            <h6 class="mb-0">
                                <i class="fas fa-receipt me-2"></i>Reçu de paiement
                            </h6>
                            <button class="btn-download-receipt" onclick="downloadReceipt()">
                                <i class="fas fa-download me-1"></i>Télécharger
                            </button>
                        </div>
                        <div class="card-body-custom">
                            <div id="final-receipt">
                                <!-- Contenu généré dynamiquement -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite - Actions et informations -->
                <div class="col-lg-4">
                    <!-- Carte d'actions -->
                    <div class="modern-card mb-4">
                        <div class="card-header-custom">
                            <h6 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </h6>
                        </div>
                        <div class="card-body-custom">
                            @if(($reservation->status === 'confirmed' || $reservation->status === 'pending') && $canCancel)
                                <button class="btn-action btn-cancel" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times-circle me-2"></i>Annuler la réservation
                                </button>
                            @endif
                            
                            @if($reservation->paiement->payment_status !== 'paid')
                                <a href="javascript:void(0);" type="button" onclick="processPayment()" class="btn-action btn-seccess-payment mt-2">
                                    <i class="fas fa-credit-card me-2"></i>Payer maintenant
                                </a>
                            @endif

                            @if(($reservation->status === 'confirmed' || $reservation->status === 'pending') && $reservation->paiement->payment_status === 'paid')
                                <a href="{{ route('reservation.reconduction', $reservation->uuid) }}" class="btn-action btn-support mt-2">
                                    <i class="fas fa-redo me-1"></i>Reconduire la reservation
                                </a>
                            @endif

                            <a href="tel:+2250787245197" class="btn-action btn-secondary mt-2">
                                <i class="fas fa-headset me-2"></i>Contacter le support
                            </a>
                        </div>
                    </div>

                    <!-- Carte informations de paiement -->
                    <div class="modern-card {{ $reservation->paiement->payment_status === 'paid' ? '' : 'd-none' }}">
                        <div class="card-header-custom">
                            <h6 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Informations de paiement
                            </h6>
                        </div>
                        <div class="card-body-custom">
                            <div class="payment-summary">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Montant payé :</span>
                                    <span class="payment-amount">{{ number_format($reservation->payment_amount, 0, ',', ' ') }} XOF</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Date de paiement :</span>
                                    <span>{{ $reservation->paiement->updated_at->format('d/m/Y à H:i') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Mode de paiement :</span>
                                    @switch($reservation->paiement->payment_mode)
                                        @case('PAIEMENTMARCHANDOMPAYCIDIRECT')
                                            <span class="shadow" style="color: #FFA500; font-weight: bold; padding: 5px; border-radius: 5px; background-color: white">Orange
                                                Money</span>
                                        @break

                                        @case('PAIEMENTMARCHAND_MTN_CI')
                                            <span class="shadow"
                                                style="color: #ffee00; font-weight: bold; padding: 5px; border-radius: 5px; background-color: white">MTN
                                                Money</span>
                                        @break

                                        @case('PAIEMENTMARCHAND_MOOV_CI')
                                            <span class="shadow"
                                                style="color: #005eff; font-weight: bold; padding: 5px; border-radius: 5px; background-color: white">Moov
                                                Money</span>
                                        @break

                                        @case('CI_PAIEMENTWAVE_TP')
                                            <span class="shadow"
                                                style="color: #00b3ff; font-weight: bold; padding: 5px; border-radius: 5px; background-color: white">Wave</span>
                                        @break

                                        @default
                                            <span class="shadow"
                                                style="color: #444; font-weight: bold; padding: 5px; border-radius: 5px; background-color: white; ">{{ $reservation->paiement->payment_mode }}</span>
                                    @endswitch
                                    {{-- <span class="badge-payment">Carte bancaire</span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section carte interactive -->
            <div class="modern-card mt-4 {{ $reservation->paiement->payment_status === 'paid' ? '' : 'd-none' }}">
                <div class="card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-map-marked-alt me-2"></i>Itinéraire vers le logement
                    </h6>
                    <div class="transport-mode-selector">
                        <button class="btn-transport active" data-mode="driving">
                            <i class="fas fa-car"></i> Voiture
                        </button>
                        <button class="btn-transport" data-mode="walking">
                            <i class="fas fa-walking"></i> À pied
                        </button>
                        <button class="btn-transport" data-mode="bicycling">
                            <i class="fas fa-bicycle"></i> Vélo
                        </button>
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="row">
                        <div class="col-lg-8">
                            <div id="map-location-property-intinerary" class="map-container" 
                                style="height: 400px; border-radius: 10px; overflow: hidden;"></div>
                        </div>
                        <div class="col-lg-4">
                            <div class="itinerary-info">
                                <h6 class="mb-3">Informations d'itinéraire</h6>
                                
                                <div class="info-item mb-3">
                                    <div class="info-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Adresse</small>
                                        <p class="mb-0 fw-bold">{{ $reservation->property->address }}</p>
                                    </div>
                                </div>
                                
                                <div class="info-item mb-3">
                                    <div class="info-icon">
                                        <i class="fas fa-route"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Distance</small>
                                        <p class="mb-0 fw-bold" id="distance-info">Calcul en cours...</p>
                                    </div>
                                </div>
                                
                                <div class="info-item mb-3">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Temps estimé</small>
                                        <p class="mb-0 fw-bold" id="duration-info">Calcul en cours...</p>
                                    </div>
                                </div>
                                
                                
                                
                                <a id="googleMapsBtn" target="_blank" class="btn-navigate">
                                    <i class="fab fa-google me-2"></i>Ouvrir dans Google Maps
                                </a>
                                
                                <button id="recenterBtn" class="btn-navigate mt-2" style="background: #6c757d;">
                                    <i class="fas fa-crosshairs me-2"></i>Recentrer sur ma position
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de confirmation d'annulation -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Annuler la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir annuler cette réservation ?</p>
                    <p class="text-muted small">Cette action est irréversible. Des frais d'annulation pourraient s'appliquer selon nos conditions générales.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Retour</button>
                    <button type="button" class="btn btn-danger" onclick="cancelReservation()">Confirmer l'annulation</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styles modernisés */
        :root {
            --primary-color: #4361ee;
            --primary-light: #eef2ff;
            --secondary-color: #3a0ca3;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: var(--border-radius);
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .btn-back:hover {
            background: var(--primary-light);
            border-color: var(--primary-color);
            transform: translateX(-4px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
        }

        .reservation-code-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 8px 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .badge-code {
            color: white;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .reservation-status-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            border-left: 4px solid var(--primary-color);
        }

        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .status-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .status-badge-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
        }

        .badge-status.confirmed {
            background: linear-gradient(135deg, #06d6a0, #04b486);
            color: white;
        }

        .badge-status.pending {
            background: linear-gradient(135deg, #ffd166, #ffb703);
            color: #333;
        }

        .badge-status.cancelled {
            background: linear-gradient(135deg, #ef476f, #e63946);
            color: white;
        }

        .badge-status.active {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
        }

        .badge-status.upcoming {
            background: linear-gradient(135deg, #7209b7, #560bad);
            color: white;
        }

        .modern-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card-header-custom {
            padding: 20px 25px;
            background: var(--light-color);
            border-bottom: 1px solid #eaeaea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-header-custom h6 {
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }

        .card-body-custom {
            padding: 25px;
        }

        .detail-item {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-label {
            display: block;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 16px;
        }

        .badge-type {
            background: var(--primary-light);
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 500;
        }

        .time-info-alert {
            background: linear-gradient(135deg, #fff8e1, #ffecb3);
            border-left: 4px solid #ffb300;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .btn-download-receipt {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-download-receipt:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-action {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            cursor: pointer;
            margin-bottom: 10px;
        }

        .btn-cancel {
            background: linear-gradient(135deg, #ef476f, #e63946);
            color: white;
        }

        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(239, 71, 111, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-seccess-payment {
            background: #28a745;
            color: white;
        }

        .btn-support {
            background: #17a2b8;
            color: white;
            text-decoration: none;
            text-align: center;
        }

        .payment-summary {
            background: var(--light-color);
            padding: 20px;
            border-radius: 10px;
        }

        .payment-amount {
            font-size: 22px;
            font-weight: 700;
            color: var(--success-color);
        }

        .badge-payment {
            background: var(--primary-light);
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 500;
        }

        .map-container {
            height: 400px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .transport-mode-selector {
            display: flex;
            gap: 10px;
        }

        .btn-transport {
            padding: 8px 15px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-transport.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .btn-transport:hover:not(.active) {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .itinerary-info {
            background: var(--light-color);
            padding: 25px;
            border-radius: 12px;
            height: 100%;
        }

        .info-item {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .btn-navigate {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            margin-top: 20px;
        }

        .btn-navigate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
            color: white;
        }

        /* Animation pour les chargements */
        @keyframes shimmer {
            0% { background-position: -200px 0; }
            100% { background-position: 200px 0; }
        }

        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200px 100%;
            animation: shimmer 1.5s infinite;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .status-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card-header-custom {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .transport-mode-selector {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reservationData = @json($reservation) || null;
            const dateLimit = @json($date_limit) || null;
            const dateLimitTotalStr = @json($dateLimitTotal) || null;
            const paymentStatus = @json($reservation->paiement->payment_status);
            // alert(paymentStatus);
            const reservationUuid = reservationData.uuid || null;
            let urlWaiting = "{{ route('reservation.paiement.waiting', ['reservation_uuid' => ':reservation_uuid']) }}";
            let urlFailed = "{{ route('reservation.paiement.failed', ['reservation_uuid' => ':reservation_uuid']) }}";
            let receiptDownloaded = false;
            // let currentMapMode = 'driving';
            function formatDateFR(dateString) {
                if (!dateString) return '';

                const d = new Date(dateString);

                return `${d.toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                })} à ${d.toLocaleTimeString('fr-FR', {
                    hour: '2-digit',
                    minute: '2-digit'
                })}`;
            }
            // Générer le reçu
            function generateReceipt() {
                if (!reservationData) return;

                const r = reservationData;
                const start = new Date(r.start_time.replace(" ", "T"));
                const end = new Date(r.end_time.replace(" ", "T"));

                const receiptHTML = `
                    <div class="receipt-container">
                        <div class="receipt-header">
                            <h6>Reçu de réservation</h6>
                            <small class="text-muted">${formatDateFR(r.created_at)}</small>
                        </div>
                        
                        <div class="receipt-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="receipt-item">
                                        <span>Référence:</span>
                                        <strong>${r.code}</strong>
                                    </div>
                                    <div class="receipt-item">
                                        <span>Client:</span>
                                        <strong>${r.prenoms} ${r.nom}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="receipt-item">
                                        <span>Montant:</span>
                                        <strong class="text-success">${Number(r.payment_amount).toLocaleString('fr-FR')} XOF</strong>
                                    </div>
                                    <div class="receipt-item">
                                        <span>Statut:</span>
                                        <span class="badge bg-success">Payé</span>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="receipt-details">
                                <h6 class="mb-3">Détails du séjour</h6>
                                ${r.sejour === 'Heure' ? `
                                    <div class="detail-grid">
                                        <div class="detail-item">
                                            <small>Type</small>
                                            <p class="mb-0">Réservation horaire</p>
                                        </div>
                                        <div class="detail-item">
                                            <small>Date</small>
                                            <p class="mb-0">${start.toLocaleDateString('fr-FR')}</p>
                                        </div>
                                        <div class="detail-item">
                                            <small>Heure début</small>
                                            <p class="mb-0">${start.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})}</p>
                                        </div>
                                        <div class="detail-item">
                                            <small>Heure fin</small>
                                            <p class="mb-0">${end.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})}</p>
                                        </div>
                                        <div class="detail-item">
                                            <small>Durée</small>
                                            <p class="mb-0">${r.nbr_of_sejour} heure(s)</p>
                                        </div>
                                    </div>
                                ` : `
                                    <div class="detail-grid">
                                        <div class="detail-item">
                                            <small>Type</small>
                                            <p class="mb-0">Réservation journalière</p>
                                        </div>
                                        <div class="detail-item">
                                            <small>Arrivée</small>
                                            <p class="mb-0">${start.toLocaleString('fr-FR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'})}</p>
                                        </div>
                                        <div class="detail-item">
                                            <small>Départ</small>
                                            <p class="mb-0">${end.toLocaleString('fr-FR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'})}</p>
                                        </div>
                                        <div class="detail-item">
                                            <small>Nuits</small>
                                            <p class="mb-0">${r.nbr_of_sejour}</p>
                                        </div>
                                    </div>
                                `}
                            </div>
                            
                            <div class="receipt-note mt-3 p-3 bg-light rounded">
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Afin de garantir votre réservation, merci de vous présenter au plus tard le 
                                    <strong>${dateLimit}</strong>. En cas de retard, votre reservation sera automatiquement annulée.
                                </small>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('final-receipt').innerHTML = receiptHTML;
                
                // Ajouter les styles pour le reçu
                const style = document.createElement('style');
                style.textContent = `
                    .receipt-container {
                        background: white;
                        border-radius: 10px;
                        padding: 20px;
                    }
                    .receipt-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 20px;
                        padding-bottom: 15px;
                        border-bottom: 2px solid #f0f0f0;
                    }
                    .receipt-item {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 10px;
                        padding-bottom: 8px;
                        border-bottom: 1px dashed #e0e0e0;
                    }
                    .detail-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                        gap: 15px;
                        margin-top: 15px;
                    }
                    .detail-item {
                        background: #f8f9fa;
                        padding: 12px;
                        border-radius: 8px;
                    }
                    .detail-item small {
                        color: #666;
                        font-size: 12px;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }
                `;
                document.head.appendChild(style);
            }

            // Télécharger le reçu
            window.downloadReceipt = function() {
                if (reservationUuid) {
                    receiptDownloaded = true;
                    
                    // Ajouter une animation de chargement
                    const btn = document.querySelector('.btn-download-receipt');
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Téléchargement...';
                    btn.disabled = true;
                    
                    setTimeout(() => {
                        window.location.href = '/api/reservation/download-receipt/' + reservationUuid;
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                        
                        // Notification de succès
                        showNotification('Reçu téléchargé avec succès !', 'success');
                    }, 1000);
                } else {
                    showNotification('Réservation introuvable !', 'error');
                }
            }

            // Annuler une réservation
            window.cancelReservation = function() {
                // Ici, ajouter la logique d'annulation
                showNotification('Réservation annulée avec succès', 'success');
                $('#cancelModal').modal('hide');
                
                // Recharger la page après 2 secondes
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }

            // Initialiser le reçu
            generateReceipt();

            // Fonction TouchPay
            reservationDataUpdated = {};

            function parseFrenchDate(dateStr) {
                // Supprimer le " à "
                const [datePart, timePart] = dateStr.split(' à ');
                const [day, month, year] = datePart.split('/');
                const [hour, minute] = timePart.split(':');

                // Créer un objet Date au format ISO
                return new Date(`${year}-${month}-${day}T${hour}:${minute}:00`);
            }


            function calltouchpay() {
                const order_number = reservationDataUpdated.reservation.code;
                const agency_code = "JSBEY11380";
                const secure_code = "UYnhBAw9f0A5DshXN8MKA6dg2VZSGs35VrXjETMZSGbJhGlhtw";
                const domain_name = 'jsbeyci.com';
                const url_redirection_success = urlWaiting.replace(':reservation_uuid', reservationUuid);
                const url_redirection_failed = urlFailed.replace(':reservation_uuid', reservationUuid);
                const amount = parseFloat(reservationDataUpdated.reservation.payment_amount);
                const city = "";
                const email = reservationDataUpdated.reservation.email || "";
                const clientFirstname = reservationDataUpdated.reservation.prenoms || "";
                const clientLastname = reservationDataUpdated.reservation.nom || "";
                const clientPhone = reservationDataUpdated.reservation.phone || "";

                sendPaymentInfos(
                    order_number,
                    agency_code,
                    secure_code,
                    domain_name,
                    url_redirection_success,
                    url_redirection_failed,
                    amount,
                    city,
                    email,
                    clientFirstname,
                    clientLastname,
                    clientPhone
                );
            }

            window.processPayment = async function() {
                Swal.fire({
                    title: 'Traitement du paiement...',
                    text: 'Veuillez patienter',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                const payload = {
                    ...reservationData
                };

                try {
                    const res = await fetch('/api/reservation/update-by-paiement/' + reservationUuid, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();

                    if (!data.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.message,
                        });
                        return;
                    }

                    // Mise à jour de reservationData avec la réponse du backend
                    // reservationData = data.data;
                    reservationDataUpdated.reservation = data.reservation;
                    // Récupérer la date actuelle
                    const now = new Date();
                    const dateLimit = parseFrenchDate(dateLimitTotalStr);
                    // alert(now);
                    // alert(dateLimit);
                    if (now > dateLimit) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Le paiement a expiré.',
                        });
                        return;
                    }else{
                        // Appel TouchPay avec les infos mises à jour
                        calltouchpay();
                    }
                    

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur réseau',
                        text: 'Impossible de traiter le paiement. Veuillez réessayer.'
                    });
                }
            }

            // Notification personnalisée
            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `notification notification-${type}`;
                notification.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                `;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.add('show');
                }, 10);
                
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }

            
            // Ajouter les styles pour les notifications
            const notificationStyle = document.createElement('style');
            notificationStyle.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 15px 25px;
                    border-radius: 8px;
                    color: white;
                    font-weight: 500;
                    z-index: 9999;
                    transform: translateX(100%);
                    opacity: 0;
                    transition: all 0.3s ease;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    display: flex;
                    align-items: center;
                }
                .notification.show {
                    transform: translateX(0);
                    opacity: 1;
                }
                .notification-success {
                    background: linear-gradient(135deg, #06d6a0, #04b486);
                }
                .notification-error {
                    background: linear-gradient(135deg, #ef476f, #e63946);
                }
            `;
            document.head.appendChild(notificationStyle);

            // Empêcher la fermeture si le reçu n'est pas téléchargé
            window.addEventListener('beforeunload', function(e) {
                if (!receiptDownloaded && reservationData.status !== 'cancelled' && paymentStatus === 'paid') {
                    e.preventDefault();
                    e.returnValue = "Veuillez télécharger votre reçu avant de quitter la page.";
                    return e.returnValue;
                }
            });

            // Initialisation de la carte (version simplifiée)
            // function initializeMap() {
            //     const latitude = @json($reservation->property->latitude ?? 0);
            //     const longitude = @json($reservation->property->longitude ?? 0);
                
            //     if (latitude && longitude) {
            //         // Simuler un chargement
            //         setTimeout(() => {
            //             document.getElementById('distance-info').textContent = '3.5 km';
            //             document.getElementById('duration-info').textContent = '15 min en voiture';
            //         }, 1500);
            //     }
            // }

            // function updateMapWithMode(mode) {
            //     // Ici, ajouter la logique pour mettre à jour la carte avec le mode de transport sélectionné
            //     const modeText = {
            //         'driving': 'en voiture',
            //         'walking': 'à pied',
            //         'bicycling': 'en vélo'
            //     };
                
            //     document.getElementById('duration-info').textContent = `Calcul ${modeText[mode]}...`;
                
            //     // Simuler un calcul
            //     setTimeout(() => {
            //         const durations = {
            //             'driving': '15 min',
            //             'walking': '45 min',
            //             'bicycling': '25 min'
            //         };
            //         document.getElementById('duration-info').textContent = `${durations[mode]} ${modeText[mode]}`;
            //     }, 1000);
            // }
        });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function () {

            const latitude = @json($reservation->property->latitude ?? 0);
            const longitude = @json($reservation->property->longitude ?? 0);

            let map, routingControl, userMarker;
            let currentMode = 'driving';
            let userLat = null, userLng = null;

            /* Carte */
            map = L.map('map-location-property-intinerary').setView([latitude, longitude], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            /* Marqueur logement */
            const propertyIcon = L.icon({
                iconUrl: "{{ asset('assets/images/location/map-icon.png') }}",
                iconSize: [40, 40],
                iconAnchor: [20, 40]
            });

            L.marker([latitude, longitude], { icon: propertyIcon })
                .addTo(map)
                .bindPopup("🏠 Logement");

            /* Icône utilisateur */
            const userIcon = L.divIcon({
                html: `<div style="
                    width:22px;height:22px;
                    background:#0d6efd;
                    border-radius:50%;
                    border:3px solid white;
                    box-shadow:0 0 10px rgba(13,110,253,.8)">
                </div>`,
                iconSize: [22,22],
                iconAnchor: [11,11]
            });

            /* Routing */
            function updateRoute() {
                if (!userLat || !userLng) return;

                if (routingControl) map.removeControl(routingControl);

                const profile = currentMode === 'walking'
                    ? 'foot'
                    : currentMode === 'bicycling'
                        ? 'bike'
                        : 'car';

                routingControl = L.Routing.control({
                    waypoints: [
                        L.latLng(userLat, userLng),
                        L.latLng(latitude, longitude)
                    ],
                    router: L.Routing.osrmv1({
                        serviceUrl: 'https://router.project-osrm.org/route/v1',
                        profile: profile
                    }),
                    addWaypoints: false,
                    draggableWaypoints: false,
                    show: false,
                    lineOptions: {
                        styles: [{ color: '#dc3545', weight: 6 }]
                    }
                }).addTo(map);

                routingControl.on('routesfound', function (e) {
                    const route = e.routes[0];
                    const distanceKm = (route.summary.totalDistance / 1000).toFixed(2);
                    const durationMin = Math.round(route.summary.totalTime / 60);

                    document.getElementById('distance-info').textContent = distanceKm + ' km';
                    document.getElementById('duration-info').textContent =
                        durationMin + ' min ' + getModeLabel();

                    const bounds = L.latLngBounds(
                        [userLat, userLng],
                        [latitude, longitude]
                    );
                    map.fitBounds(bounds, { padding: [50, 50] });

                    document.getElementById('googleMapsBtn').href =
                        `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${latitude},${longitude}&travelmode=${currentMode}&dir_action=navigate`;
                });
            }

            function getModeLabel() {
                return currentMode === 'walking'
                    ? 'à pied'
                    : currentMode === 'bicycling'
                        ? 'en vélo'
                        : 'en voiture';
            }

            /* GPS */
            navigator.geolocation.watchPosition(pos => {
                userLat = pos.coords.latitude;
                userLng = pos.coords.longitude;

                if (!userMarker) {
                    userMarker = L.marker([userLat, userLng], { icon: userIcon })
                        .addTo(map)
                        .bindPopup("📍 Vous");
                } else {
                    userMarker.setLatLng([userLat, userLng]);
                }

                updateRoute();
            }, () => alert("Position GPS indisponible"), { enableHighAccuracy: true });

            /* Boutons mode */
            document.querySelectorAll('.btn-transport').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.btn-transport')
                        .forEach(b => b.classList.remove('active'));

                    this.classList.add('active');
                    currentMode = this.dataset.mode;

                    document.getElementById('duration-info').textContent = 'Recalcul…';
                    updateRoute();
                });
            });

        });
    </script> --}}

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latitude = @json($reservation->property->latitude ?? 0);
            const longitude = @json($reservation->property->longitude ?? 0);
            const propertyAddress = @json($reservation->property->address ?? '');
            
            let currentMapMode = 'driving';
            let map, propertyMarker, userMarker, routingControl;
            let userPosition = null;

            // Fonds de carte améliorés
            const baseMaps = {
                "Standard": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19
                }),
                "Topographie": L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                    attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a>',
                    maxZoom: 17
                }),
                "Satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
                })
            };

            // Initialisation de la carte
            function initializeMap() {
                if (!latitude || !longitude) {
                    console.error('Coordonnées de la propriété non disponibles');
                    return;
                }

                map = L.map('map-location-property-intinerary', {
                    center: [latitude, longitude],
                    zoom: 15,
                    layers: [baseMaps["Standard"]]
                });

                // Ajout du contrôle des couches
                L.control.layers(baseMaps).addTo(map);

                // Icône personnalisée pour la propriété
                const propertyIcon = L.divIcon({
                    className: 'property-marker',
                    html: `<div style="
                        width: 50px;
                        height: 50px;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 0 15px rgba(102, 126, 234, 0.8);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 20px;
                    ">🏠</div>`,
                    iconSize: [50, 50],
                    iconAnchor: [25, 50]
                });

                // Marqueur de la propriété
                propertyMarker = L.marker([latitude, longitude], {
                    icon: propertyIcon,
                    zIndexOffset: 1000
                }).addTo(map)
                .bindPopup(`<b>${propertyAddress}</b>`)
                .openPopup();

                // Ajout d'un cercle de rayon
                L.circle([latitude, longitude], {
                    color: '#667eea',
                    fillColor: '#667eea',
                    fillOpacity: 0.1,
                    radius: 500
                }).addTo(map);

                // Initialisation du suivi de position
                initGeolocation();
            }

            // Initialisation de la géolocalisation
            function initGeolocation() {
                if (!navigator.geolocation) {
                    alert('La géolocalisation n\'est pas supportée par votre navigateur');
                    return;
                }

                // Suivi continu de la position
                navigator.geolocation.watchPosition(
                    position => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        userPosition = { lat: userLat, lng: userLng };
                        
                        updateUserMarker(userLat, userLng);
                        updateRoute(userLat, userLng, currentMapMode);
                        updateWalkingInfo(userLat, userLng);
                    },
                    error => {
                        console.error('Erreur de géolocalisation:', error);
                        document.getElementById('distance-info').textContent = 'GPS non disponible';
                        document.getElementById('duration-info').textContent = 'Activez la localisation';
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 30000,
                        timeout: 27000
                    }
                );
            }

            // Mise à jour du marqueur utilisateur
            function updateUserMarker(lat, lng) {
                const userIcon = L.divIcon({
                    className: 'user-location-marker',
                    html: `<div style="
                        width: 30px;
                        height: 30px;
                        background: radial-gradient(circle, #28a745 40%, #155724 70%);
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 0 10px rgba(40, 167, 69, 0.8);
                        animation: pulse 1.5s infinite;
                    "></div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                if (!userMarker) {
                    userMarker = L.marker([lat, lng], { icon: userIcon })
                        .addTo(map)
                        .bindPopup('📍 Votre position actuelle');
                } else {
                    userMarker.setLatLng([lat, lng]);
                }
            }

            // Calcul et affichage de l'itinéraire
            function updateRoute(startLat, startLng, mode) {
                // Supprimer l'ancien contrôle de routage
                if (routingControl) {
                    map.removeControl(routingControl);
                }

                // Configurer le style de la route selon le mode
                const lineStyle = {
                    driving: { color: '#667eea', weight: 5, opacity: 0.8 },
                    walking: { color: '#28a745', weight: 4, opacity: 0.8, dashArray: '5, 10' },
                    bicycling: { color: '#fd7e14', weight: 4, opacity: 0.8, dashArray: '8, 8' }
                };

                routingControl = L.Routing.control({
                    waypoints: [
                        L.latLng(startLat, startLng),
                        L.latLng(latitude, longitude)
                    ],
                    router: L.Routing.osrmv1({
                        serviceUrl: 'https://router.project-osrm.org/route/v1',
                        profile: mode // 'driving', 'walking', or 'cycling'
                    }),
                    lineOptions: {
                        styles: [lineStyle[mode]],
                        extendToWaypoints: false,
                        missingRouteTolerance: 0
                    },
                    routeWhileDragging: false,
                    showAlternatives: false,
                    show: false,
                    addWaypoints: false,
                    fitSelectedRoutes: 'smart'
                }).addTo(map);

                // Mettre à jour les informations lors de la découverte de l'itinéraire
                routingControl.on('routesfound', function(e) {
                    const routes = e.routes;
                    if (routes && routes.length > 0) {
                        const route = routes[0];
                        const distanceKm = (route.summary.totalDistance / 1000).toFixed(2);
                        const durationMin = Math.round(route.summary.totalTime / 60);
                        
                        // Mettre à jour l'interface
                        document.getElementById('distance-info').textContent = `${distanceKm} km`;
                        
                        const modeText = {
                            'driving': 'voiture',
                            'walking': 'marche',
                            'bicycling': 'vélo'
                        };
                        
                        document.getElementById('duration-info').textContent = 
                            `${durationMin} min en ${modeText[mode]}`;
                        
                        // Mettre à jour le lien Google Maps
                        const travelMode = mode === 'bicycling' ? 'bicycling' : mode;
                        document.getElementById('googleMapsBtn').href = 
                            `https://www.google.com/maps/dir/?api=1&origin=${startLat},${startLng}&destination=${latitude},${longitude}&travelmode=${travelMode}`;
                    }
                });

                routingControl.on('routingerror', function(e) {
                    console.error('Erreur de calcul d\'itinéraire:', e.error);
                    document.getElementById('duration-info').textContent = 'Erreur de calcul';
                });
            }

            // Calcul spécifique pour la marche
            function updateWalkingInfo(startLat, startLng) {
                const walkingRouter = L.Routing.osrmv1({
                    serviceUrl: 'https://router.project-osrm.org/route/v1',
                    profile: 'foot'
                });

                walkingRouter.route([
                    L.latLng(startLat, startLng),
                    L.latLng(latitude, longitude)
                ], function(err, routes) {
                    if (!err && routes && routes.length > 0) {
                        const route = routes[0];
                        const walkingDistanceKm = (route.summary.totalDistance / 1000).toFixed(2);
                        const walkingDurationMin = Math.round(route.summary.totalTime / 60);
                        
                        document.getElementById('walking-info').textContent = 
                            `${walkingDurationMin} min (${walkingDistanceKm} km)`;
                    }
                });
            }

            // Gestionnaire pour les boutons de mode de transport
            document.querySelectorAll('.btn-transport').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.btn-transport').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentMapMode = this.dataset.mode;
                    
                    if (userPosition) {
                        updateRoute(userPosition.lat, userPosition.lng, currentMapMode);
                    }
                });
            });

            // Bouton recentrer
            document.getElementById('recenterBtn').addEventListener('click', function() {
                if (userPosition) {
                    map.setView([userPosition.lat, userPosition.lng], 15);
                    userMarker.openPopup();
                } else if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(position => {
                        map.setView([position.coords.latitude, position.coords.longitude], 15);
                    });
                }
            });

            // Ajouter le CSS pour l'animation du marqueur
            const style = document.createElement('style');
            style.textContent = `
                @keyframes pulse {
                    0% { transform: scale(1); opacity: 1; }
                    50% { transform: scale(1.1); opacity: 0.8; }
                    100% { transform: scale(1); opacity: 1; }
                }
                .user-location-marker {
                    animation: pulse 1.5s infinite;
                }
            `;
            document.head.appendChild(style);

            // Initialiser la carte
            initializeMap();
        });
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latitude = parseFloat(@json($reservation->property->latitude ?? 0));
            const longitude = parseFloat(@json($reservation->property->longitude ?? 0));
            const propertyAddress = @json($reservation->property->address ?? '');
            const propertyTitle = @json($reservation->property->title ?? '');
            
            let currentMapMode = 'driving';
            let map, propertyMarker, userMarker, routingControl;
            let userPosition = null;
            let isMapInitialized = false;

            // Vérifier si les coordonnées sont valides
            if (!latitude || !longitude || isNaN(latitude) || isNaN(longitude)) {
                console.error('Coordonnées de la propriété invalides');
                document.getElementById('map-location-property-intinerary').innerHTML = 
                    '<div class="alert alert-warning p-4 text-center">Coordonnées GPS non disponibles pour cette propriété</div>';
                return;
            }

            // Fonds de carte améliorés
            const baseMaps = {
                "Standard": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19
                }),
                "Topographie": L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                    attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a>',
                    maxZoom: 17
                }),
                "Satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
                    maxZoom: 19
                })
            };

            // Initialisation de la carte
            function initializeMap() {
                try {
                    map = L.map('map-location-property-intinerary', {
                        center: [latitude, longitude],
                        zoom: 15,
                        layers: [baseMaps["Standard"]],
                        zoomControl: true,
                        scrollWheelZoom: true
                    });

                    // Ajout du contrôle des couches
                    L.control.layers(baseMaps).addTo(map);

                    // Icône personnalisée pour la propriété
                    const propertyIcon = L.divIcon({
                        className: 'property-marker',
                        html: `<div style="
                            width: 50px;
                            height: 50px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            border-radius: 50%;
                            border: 3px solid white;
                            box-shadow: 0 0 15px rgba(102, 126, 234, 0.8);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-size: 20px;
                        ">🏠</div>`,
                        iconSize: [50, 50],
                        iconAnchor: [25, 50],
                        popupAnchor: [0, -50]
                    });

                    // Marqueur de la propriété
                    propertyMarker = L.marker([latitude, longitude], {
                        icon: propertyIcon,
                        zIndexOffset: 1000
                    }).addTo(map)
                    .bindPopup(`
                        <div class="p-2">
                            <h5>Destination </h5>
                            <h6>${propertyTitle}</h6>
                            <b>${propertyAddress}</b>
                        </div>`)
                    .openPopup();

                    // Ajout d'un cercle de rayon pour visualisation
                    L.circle([latitude, longitude], {
                        color: '#667eea',
                        fillColor: '#667eea',
                        fillOpacity: 0.1,
                        radius: 500,
                        weight: 1
                    }).addTo(map);

                    isMapInitialized = true;
                    
                    // Initialisation du suivi de position
                    setTimeout(initGeolocation, 500); // Petit délai pour laisser la carte s'initialiser
                    
                } catch (error) {
                    console.error('Erreur lors de l\'initialisation de la carte:', error);
                    document.getElementById('map-location-property-intinerary').innerHTML = 
                        '<div class="alert alert-danger p-4 text-center">Erreur lors du chargement de la carte</div>';
                }
            }

            // Initialisation de la géolocalisation
            function initGeolocation() {
                if (!navigator.geolocation) {
                    showGeolocationError('La géolocalisation n\'est pas supportée par votre navigateur');
                    return;
                }

                // D'abord, essayer d'obtenir la position actuelle
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        userPosition = { lat: userLat, lng: userLng };
                        
                        updateUserMarker(userLat, userLng);
                        updateRoute(userLat, userLng, currentMapMode);
                        updateWalkingInfo(userLat, userLng);
                        
                        // Puis démarrer le suivi continu
                        startGeolocationWatch();
                    },
                    error => {
                        handleGeolocationError(error);
                        // Essayer quand même de démarrer le suivi
                        startGeolocationWatch();
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }

            function startGeolocationWatch() {
                navigator.geolocation.watchPosition(
                    position => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        
                        // Vérifier si la position a significativement changé
                        if (!userPosition || 
                            Math.abs(userPosition.lat - userLat) > 0.0001 || 
                            Math.abs(userPosition.lng - userLng) > 0.0001) {
                            
                            userPosition = { lat: userLat, lng: userLng };
                            updateUserMarker(userLat, userLng);
                            updateRoute(userLat, userLng, currentMapMode);
                            updateWalkingInfo(userLat, userLng);
                        }
                    },
                    error => {
                        // Ne pas alerter pour les erreurs de suivi, seulement pour l'initialisation
                        console.warn('Erreur de suivi GPS:', error);
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 30000,
                        timeout: 27000
                    }
                );
            }

            function handleGeolocationError(error) {
                let message = 'Impossible d\'obtenir votre position';
                
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Autorisation de localisation refusée. Activez la géolocalisation dans les paramètres de votre navigateur.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Position indisponible. Vérifiez votre connexion ou votre GPS.';
                        break;
                    case error.TIMEOUT:
                        message = 'La requête de localisation a expiré.';
                        break;
                }
                
                showGeolocationError(message);
            }

            function showGeolocationError(message) {
                document.getElementById('distance-info').textContent = 'GPS non disponible';
                document.getElementById('duration-info').textContent = 'Activez la localisation';
                // document.getElementById('walking-info').textContent = 'Non disponible';
                
                // Afficher un message sur la carte
                if (map) {
                    L.control.alert({
                        position: 'topright',
                        content: `<div class="alert alert-warning p-2"><small>${message}</small></div>`,
                        autoClose: 10000
                    }).addTo(map);
                }
            }

            // Mise à jour du marqueur utilisateur
            function updateUserMarker(lat, lng) {
                const userIcon = L.divIcon({
                    className: 'user-location-marker',
                    html: `<div style="
                        width: 30px;
                        height: 30px;
                        background: radial-gradient(circle, #2e28a7 40%, #191557 70%);
                        /* background: radial-gradient(circle, #28a745 40%, #155724 70%);*/
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 0 10px rgba(40, 167, 69, 0.8);
                    "></div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                    popupAnchor: [0, -15]
                });

                if (!userMarker) {
                    userMarker = L.marker([lat, lng], { 
                        icon: userIcon,
                        zIndexOffset: 500 
                    })
                    .addTo(map)
                    .bindPopup('<div class="p-2"><h6>Point de départ</h6><b>Votre position actuelle</b></div>');
                } else {
                    userMarker.setLatLng([lat, lng]);
                }
            }

            // Calcul et affichage de l'itinéraire
            // function updateRoute(startLat, startLng, mode) {
            //     // Supprimer l'ancien contrôle de routage
            //     if (routingControl) {
            //         map.removeControl(routingControl);
            //     }

            //     // Configurer le style de la route selon le mode
            //     const lineStyle = {
            //         driving: { color: '#002fff', weight: 6, opacity: 0.8 },
            //         walking: { color: '#ff0008', weight: 6, opacity: 0.8, dashArray: '5, 10' },
            //         bicycling: { color: '#ff4d00', weight: 6, opacity: 0.8, dashArray: '8, 8' }
            //     };

            //     // Convertir le mode OSRM selon le bouton sélectionné
            //     let osrmProfile;
            //     let googleTravelMode;
                
            //     switch(mode) {
            //         case 'driving':
            //             osrmProfile = 'driving';
            //             googleTravelMode = 'driving';
            //             break;
            //         case 'walking':
            //             osrmProfile = 'foot';
            //             googleTravelMode = 'walking';
            //             break;
            //         case 'bicycling':
            //             osrmProfile = 'cycling';
            //             googleTravelMode = 'bicycling';
            //             break;
            //         default:
            //             osrmProfile = 'driving';
            //             googleTravelMode = 'driving';
            //     }
                
            //     routingControl = L.Routing.control({
            //         waypoints: [
            //             L.latLng(startLat, startLng),
            //             L.latLng(latitude, longitude)
            //         ],
            //         router: L.Routing.osrmv1({
            //             serviceUrl: 'https://router.project-osrm.org/route/v1',
            //             profile: osrmProfile
            //         }),
            //         lineOptions: {
            //             styles: [lineStyle[mode]],
            //             extendToWaypoints: true,
            //             missingRouteTolerance: 10
            //         },
            //         routeWhileDragging: false,
            //         showAlternatives: false,
            //         show: false,
            //         addWaypoints: false,
            //         fitSelectedRoutes: true,
            //         createMarker: function() { return null; }
            //     }).addTo(map);

            //     // Fonctions utilitaires pour formater le temps et la distance
            //     function formatDuration(minutes) {
            //         if (minutes >= 60) {
            //             const hours = Math.floor(minutes / 60);
            //             const remainingMinutes = minutes % 60;
            //             if (remainingMinutes === 0) {
            //                 return `${hours}h`;
            //             }
            //             return `${hours}h${remainingMinutes}min`;
            //         }
            //         return `${minutes}min`;
            //     }

            //     function formatDistance(meters) {
            //         if (meters >= 1000) {
            //             const km = (meters / 1000).toFixed(2);
            //             // Retirer les décimales inutiles
            //             if (km.endsWith('.00')) {
            //                 return `${parseInt(km)} km`;
            //             } else if (km.endsWith('0')) {
            //                 return `${parseFloat(km).toFixed(1)} km`;
            //             }
            //             return `${km} km`;
            //         }
            //         return `${Math.round(meters)} m`;
            //     }

            //     // Mettre à jour les informations lors de la découverte de l'itinéraire
            //     routingControl.on('routesfound', function(e) {
            //         const routes = e.routes;
            //         if (routes && routes.length > 0) {
            //             const route = routes[0];
            //             const distanceMeters = route.summary.totalDistance;
            //             const durationSeconds = route.summary.totalTime;
            //             const durationMinutes = Math.round(durationSeconds / 60);
                        
            //             // Pour debug: afficher les données brutes
            //             console.log(`Mode: ${mode}, Distance: ${distanceMeters}m, Durée OSRM: ${durationMinutes}min`);
                        
            //             // Appliquer des facteurs de correction réalistes
            //             let adjustedMinutes = durationMinutes;
            //             let note = '';
                        
            //             // Vitesses moyennes réalistes (en km/h) :
            //             // Voiture : 40-50 km/h en ville
            //             // Vélo : 12-18 km/h
            //             // Marche : 4-6 km/h
                        
            //             // Si le temps semble irréaliste (trop court pour vélo/marche), on corrige
            //             if (mode === 'walking') {
            //                 // Temps minimal réaliste pour la marche : ~9.5 min/km
            //                 const realisticWalkMinutes = Math.round(distanceMeters / 1000 * 9.5);
            //                 if (durationMinutes < realisticWalkMinutes * 0.5) {
            //                     // Si le temps OSRM est moins de la moitié du temps réaliste
            //                     adjustedMinutes = realisticWalkMinutes;
            //                     note = ' (estimation)';
            //                     console.log(`Correction marche: ${durationMinutes}min → ${adjustedMinutes}min`);
            //                 }
            //             } else if (mode === 'bicycling') {
            //                 // Temps minimal réaliste pour le vélo : ~2.5 min/km
            //                 const realisticBikeMinutes = Math.round(distanceMeters / 1000 * 1.5);
            //                 if (durationMinutes < realisticBikeMinutes * 0.5) {
            //                     // Si le temps OSRM est moins de la moitié du temps réaliste
            //                     adjustedMinutes = realisticBikeMinutes;
            //                     note = ' (estimation)';
            //                     console.log(`Correction vélo: ${durationMinutes}min → ${adjustedMinutes}min`);
            //                 }
            //             } else if (mode === 'driving') {
            //                 // Temps minimal réaliste pour la voiture : ~1.2 min/km
            //                 const realisticCarMinutes = Math.round(distanceMeters / 1000 * 0.8);
            //                 if (durationMinutes < realisticCarMinutes * 0.5) {
            //                     adjustedMinutes = Math.max(1, realisticCarMinutes);
            //                     note = ' (estimation)';
            //                     console.log(`Correction voiture: ${durationMinutes}min → ${adjustedMinutes}min`);
            //                 }
            //             }
                        
            //             // S'assurer que le temps n'est pas inférieur à 1 minute
            //             adjustedMinutes = Math.max(1, adjustedMinutes);
                        
            //             // Formater la distance et la durée
            //             const formattedDistance = formatDistance(distanceMeters);
            //             const formattedDuration = formatDuration(adjustedMinutes);
                        
            //             // Mettre à jour l'interface
            //             document.getElementById('distance-info').textContent = formattedDistance;
            //             document.getElementById('distance-info').classList.remove('text-danger');
                        
            //             const modeText = {
            //                 'driving': 'voiture',
            //                 'walking': 'marche',
            //                 'bicycling': 'vélo'
            //             };
                        
            //             const durationText = `${formattedDuration} en ${modeText[mode]}${note}`;
            //             document.getElementById('duration-info').textContent = durationText;
            //             document.getElementById('duration-info').classList.remove('text-danger');
                        
            //             // Mettre à jour le lien Google Maps avec le bon mode
            //             const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${startLat},${startLng}&destination=${latitude},${longitude}&travelmode=${googleTravelMode}`;
            //             document.getElementById('googleMapsBtn').href = googleMapsUrl;
                        
            //             // Ajuster la vue pour voir les deux points
            //             const bounds = L.latLngBounds(
            //                 [startLat, startLng],
            //                 [latitude, longitude]
            //             );
            //             map.fitBounds(bounds, { padding: [50, 50] });
            //         }
            //     });

            //     routingControl.on('routingerror', function(e) {
            //         console.error('Erreur de calcul d\'itinéraire pour le mode', mode, ':', e.error);
                    
            //         // Si erreur, on peut estimer le temps basé sur la distance
            //         if (userPosition) {
            //             const distance = calculateDistance(userPosition.lat, userPosition.lng, latitude, longitude);
            //             let estimatedMinutes;
                        
            //             // Temps réalistes basés sur la distance
            //             switch(mode) {
            //                 case 'driving':
            //                     estimatedMinutes = Math.max(1, Math.round(distance / 1000 * 1.2)); // ~1.2 min/km
            //                     break;
            //                 case 'walking':
            //                     estimatedMinutes = Math.max(1, Math.round(distance / 1000 * 11.5)); // ~13 min/km
            //                     break;
            //                 case 'bicycling':
            //                     estimatedMinutes = Math.max(1, Math.round(distance / 1000 * 2.5)); // ~2.5 min/km
            //                     break;
            //             }
                        
            //             const formattedDuration = formatDuration(estimatedMinutes);
            //             const modeText = {
            //                 'driving': 'voiture',
            //                 'walking': 'marche',
            //                 'bicycling': 'vélo'
            //             };
                        
            //             document.getElementById('duration-info').textContent = `${formattedDuration} en ${modeText[mode]} (estimation)`;
            //         } else {
            //             document.getElementById('duration-info').textContent = `Erreur pour ${mode}`;
            //         }
                    
            //         document.getElementById('duration-info').classList.add('text-danger');
            //     });
            // }

            // // Fonction pour calculer la distance en ligne droite (Haversine)
            // function calculateDistance(lat1, lon1, lat2, lon2) {
            //     const R = 6371e3; // Rayon de la Terre en mètres
            //     const φ1 = lat1 * Math.PI/180;
            //     const φ2 = lat2 * Math.PI/180;
            //     const Δφ = (lat2-lat1) * Math.PI/180;
            //     const Δλ = (lon2-lon1) * Math.PI/180;

            //     const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
            //             Math.cos(φ1) * Math.cos(φ2) *
            //             Math.sin(Δλ/2) * Math.sin(Δλ/2);
            //     const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

            //     return R * c; // Distance en mètres
            // }

            function updateRoute(startLat, startLng, mode) {
                // Supprimer l'ancien contrôle de routage
                if (routingControl) {
                    map.removeControl(routingControl);
                }

                // Configurer le style de la route selon le mode
                const lineStyle = {
                    driving: { color: '#002fff', weight: 6, opacity: 0.8 },
                    walking: { color: '#ff0008', weight: 6, opacity: 0.8, dashArray: '5, 10' },
                    bicycling: { color: '#ff4d00', weight: 6, opacity: 0.8, dashArray: '8, 8' }
                };

                // Convertir le mode OSRM selon le bouton sélectionné
                let osrmProfile;
                let googleTravelMode;
                
                switch(mode) {
                    case 'driving':
                        osrmProfile = 'driving';
                        googleTravelMode = 'driving';
                        break;
                    case 'walking':
                        osrmProfile = 'foot';
                        googleTravelMode = 'walking';
                        break;
                    case 'bicycling':
                        osrmProfile = 'cycling';
                        googleTravelMode = 'bicycling';
                        break;
                    default:
                        osrmProfile = 'driving';
                        googleTravelMode = 'driving';
                }
                
                routingControl = L.Routing.control({
                    waypoints: [
                        L.latLng(startLat, startLng),
                        L.latLng(latitude, longitude)
                    ],
                    router: L.Routing.osrmv1({
                        serviceUrl: 'https://router.project-osrm.org/route/v1',
                        profile: osrmProfile
                    }),
                    lineOptions: {
                        styles: [lineStyle[mode]],
                        extendToWaypoints: true,
                        missingRouteTolerance: 10
                    },
                    routeWhileDragging: false,
                    showAlternatives: false,
                    show: false,
                    addWaypoints: false,
                    fitSelectedRoutes: true,
                    createMarker: function() { return null; }
                }).addTo(map);

                // Fonctions utilitaires pour formater le temps et la distance
                function formatDuration(minutes) {
                    if (minutes >= 60) {
                        const hours = Math.floor(minutes / 60);
                        const remainingMinutes = minutes % 60;
                        if (remainingMinutes === 0) {
                            return `${hours}h`;
                        }
                        return `${hours}h${remainingMinutes}min`;
                    }
                    return `${minutes}min`;
                }

                function formatDistance(meters) {
                    if (meters >= 1000) {
                        const km = (meters / 1000).toFixed(2);
                        // Retirer les décimales inutiles
                        if (km.endsWith('.00')) {
                            return `${parseInt(km)} km`;
                        } else if (km.endsWith('0')) {
                            return `${parseFloat(km).toFixed(1)} km`;
                        }
                        return `${km} km`;
                    }
                    return `${Math.round(meters)} m`;
                }

                // Mettre à jour les informations lors de la découverte de l'itinéraire
                routingControl.on('routesfound', function(e) {
                    const routes = e.routes;
                    if (routes && routes.length > 0) {
                        const route = routes[0];
                        const distanceMeters = route.summary.totalDistance;
                        const durationSeconds = route.summary.totalTime;
                        const durationMinutes = Math.round(durationSeconds / 60);
                        
                        // Pour debug: afficher les données brutes
                        console.log(`Mode: ${mode}, Distance: ${distanceMeters}m, Durée OSRM: ${durationMinutes}min`);
                        
                        // Basé sur les algorithmes de Google Maps:
                        // - Voiture : vitesse moyenne de 25-50 km/h selon le trafic et la zone
                        // - Vélo : vitesse moyenne de 12-20 km/h
                        // - Marche : vitesse moyenne de 5 km/h (avec facteur de terrain)
                        
                        let adjustedMinutes = durationMinutes;
                        let note = '';
                        
                        // Facteurs de correction basés sur l'expérience Google Maps
                        // Google Maps ajuste en fonction du trafic, des feux, stops, etc.
                        
                        if (mode === 'walking') {
                            // Google Maps utilise ~5 km/h pour la marche, mais avec facteurs supplémentaires
                            // Facteur: temps réel = temps théorique × 1.3 (pour arrêts, feux, etc.)
                            const theoreticalWalkMinutes = Math.round((distanceMeters / 1000) * 12); // 5 km/h = 12 min/km
                            adjustedMinutes = Math.round(theoreticalWalkMinutes * 1.3);
                            
                            // Si OSRM donne un temps trop court (moins de 70% du temps théorique)
                            if (durationMinutes < theoreticalWalkMinutes * 0.7) {
                                note = ' (estimation)';
                                console.log(`Correction marche Google: ${durationMinutes}min → ${adjustedMinutes}min`);
                            } else {
                                adjustedMinutes = durationMinutes;
                            }
                            
                        } else if (mode === 'bicycling') {
                            // Google Maps utilise ~15 km/h pour le vélo en ville
                            // Facteur: temps réel = temps théorique × 1.2
                            const theoreticalBikeMinutes = Math.round((distanceMeters / 1000) * 4); // 15 km/h = 4 min/km
                            adjustedMinutes = Math.round(theoreticalBikeMinutes * 1.2);
                            
                            // Si OSRM donne un temps trop court
                            if (durationMinutes < theoreticalBikeMinutes * 0.7) {
                                note = ' (estimation)';
                                console.log(`Correction vélo Google: ${durationMinutes}min → ${adjustedMinutes}min`);
                            } else {
                                adjustedMinutes = durationMinutes;
                            }
                            
                        } else if (mode === 'driving') {
                            // Google Maps utilise des données de trafic en temps réel
                            // Vitesse moyenne en ville: 30-40 km/h = 1.5-2 min/km
                            // Facteur de trafic: 1.1 à 1.5 selon l'heure
                            const theoreticalCarMinutes = Math.round((distanceMeters / 1000) * 1.8); // ~33 km/h
                            adjustedMinutes = Math.round(theoreticalCarMinutes * 1.2); // Facteur trafic léger
                            
                            // Si OSRM donne un temps trop optimiste
                            if (durationMinutes < theoreticalCarMinutes * 0.8) {
                                note = ' (estimation)';
                                console.log(`Correction voiture Google: ${durationMinutes}min → ${adjustedMinutes}min`);
                            } else {
                                adjustedMinutes = durationMinutes;
                            }
                        }
                        
                        // Garantir un minimum réaliste
                        // Pour de très courtes distances, Google ajoute un buffer
                        if (distanceMeters < 1000) {
                            adjustedMinutes = Math.max(adjustedMinutes, 3); // Minimum 3 min pour tout déplacement
                        } else if (distanceMeters < 500) {
                            adjustedMinutes = Math.max(adjustedMinutes, 2); // Minimum 2 min
                        }
                        
                        // S'assurer que le temps n'est pas inférieur à 1 minute
                        adjustedMinutes = Math.max(1, adjustedMinutes);
                        
                        // Formater la distance et la durée
                        const formattedDistance = formatDistance(distanceMeters);
                        const formattedDuration = formatDuration(adjustedMinutes);
                        
                        // Mettre à jour l'interface
                        document.getElementById('distance-info').textContent = formattedDistance;
                        document.getElementById('distance-info').classList.remove('text-danger');
                        
                        const modeText = {
                            'driving': 'voiture',
                            'walking': 'marche',
                            'bicycling': 'vélo'
                        };
                        
                        const durationText = `${formattedDuration} en ${modeText[mode]}${note}`;
                        document.getElementById('duration-info').textContent = durationText;
                        document.getElementById('duration-info').classList.remove('text-danger');
                        
                        // Mettre à jour le lien Google Maps avec le bon mode
                        const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${startLat},${startLng}&destination=${latitude},${longitude}&travelmode=${googleTravelMode}`;
                        document.getElementById('googleMapsBtn').href = googleMapsUrl;
                        
                        // Ajuster la vue pour voir les deux points
                        const bounds = L.latLngBounds(
                            [startLat, startLng],
                            [latitude, longitude]
                        );
                        map.fitBounds(bounds, { padding: [50, 50] });
                        
                        // Afficher un rapport détaillé
                        console.log(`RAPPORT FINAL - Distance: ${formattedDistance}`);
                        console.log(`- Voiture théorique: ${Math.round((distanceMeters / 1000) * 1.8)} min`);
                        console.log(`- Vélo théorique: ${Math.round((distanceMeters / 1000) * 4)} min`);
                        console.log(`- Marche théorique: ${Math.round((distanceMeters / 1000) * 12)} min`);
                        console.log(`- Résultat ${mode}: ${adjustedMinutes} min${note}`);
                    }
                });

                routingControl.on('routingerror', function(e) {
                    console.error('Erreur de calcul d\'itinéraire pour le mode', mode, ':', e.error);
                    
                    // Si erreur, on peut estimer le temps basé sur la distance (selon Google Maps)
                    if (userPosition) {
                        const distance = calculateDistance(userPosition.lat, userPosition.lng, latitude, longitude);
                        const distanceKm = distance / 1000;
                        let estimatedMinutes;
                        
                        // Estimations Google Maps standards
                        switch(mode) {
                            case 'driving':
                                // 1.8 min/km pour trafic normal (33 km/h)
                                estimatedMinutes = Math.max(1, Math.round(distanceKm * 1.8 * 1.2));
                                break;
                            case 'walking':
                                // 12 min/km pour marche (5 km/h) avec facteur
                                estimatedMinutes = Math.max(1, Math.round(distanceKm * 12 * 1.3));
                                break;
                            case 'bicycling':
                                // 4 min/km pour vélo (15 km/h) avec facteur
                                estimatedMinutes = Math.max(1, Math.round(distanceKm * 4 * 1.2));
                                break;
                        }
                        
                        // Minimum pour courtes distances
                        if (distance < 1000) estimatedMinutes = Math.max(estimatedMinutes, 3);
                        if (distance < 500) estimatedMinutes = Math.max(estimatedMinutes, 2);
                        
                        const formattedDuration = formatDuration(estimatedMinutes);
                        const modeText = {
                            'driving': 'voiture',
                            'walking': 'marche',
                            'bicycling': 'vélo'
                        };
                        
                        document.getElementById('duration-info').textContent = `${formattedDuration} en ${modeText[mode]} (estimation Google)`;
                    } else {
                        document.getElementById('duration-info').textContent = `Erreur pour ${mode}`;
                    }
                    
                    document.getElementById('duration-info').classList.add('text-danger');
                });
            }

            // Fonction pour calculer la distance en ligne droite (Haversine)
            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371e3; // Rayon de la Terre en mètres
                const φ1 = lat1 * Math.PI/180;
                const φ2 = lat2 * Math.PI/180;
                const Δφ = (lat2-lat1) * Math.PI/180;
                const Δλ = (lon2-lon1) * Math.PI/180;

                const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                        Math.cos(φ1) * Math.cos(φ2) *
                        Math.sin(Δλ/2) * Math.sin(Δλ/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

                return R * c; // Distance en mètres
            }

            // Gestionnaire pour les boutons de mode de transport
            document.querySelectorAll('.btn-transport').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.btn-transport').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentMapMode = this.dataset.mode;
                    
                    // Mettre à jour l'icône active
                    const icon = this.querySelector('i');
                    const allIcons = document.querySelectorAll('.btn-transport i');
                    allIcons.forEach(i => i.style.opacity = '0.7');
                    icon.style.opacity = '1';
                    
                    if (userPosition) {
                        // Mettre à jour l'itinéraire avec le mode sélectionné
                        updateRoute(userPosition.lat, userPosition.lng, currentMapMode);
                    }
                });
            });

            // Bouton recentrer
            document.getElementById('recenterBtn').addEventListener('click', function() {
                if (userPosition) {
                    map.setView([userPosition.lat, userPosition.lng], 15);
                    if (userMarker) userMarker.openPopup();
                } else if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(position => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        map.setView([lat, lng], 15);
                        updateUserMarker(lat, lng);
                    }, () => {
                        alert('Impossible d\'obtenir votre position actuelle');
                    });
                }
            });

            // Extension pour les alertes Leaflet (si non disponible)
            if (!L.control.alert) {
                L.Control.Alert = L.Control.extend({
                    options: {
                        position: 'topright'
                    },
                    
                    onAdd: function(map) {
                        this._container = L.DomUtil.create('div', 'leaflet-control-alert');
                        L.DomEvent.disableClickPropagation(this._container);
                        this._container.innerHTML = this.options.content;
                        
                        if (this.options.autoClose) {
                            setTimeout(() => {
                                this.remove();
                            }, this.options.autoClose);
                        }
                        
                        return this._container;
                    },
                    
                    remove: function() {
                        if (this._container && this._container.parentNode) {
                            this._container.parentNode.removeChild(this._container);
                        }
                    }
                });
                
                L.control.alert = function(options) {
                    return new L.Control.Alert(options);
                };
            }

            // Initialiser la carte
            initializeMap();
            
            // Redimensionner la carte quand la fenêtre change de taille
            window.addEventListener('resize', function() {
                if (map) {
                    setTimeout(() => {
                        map.invalidateSize();
                    }, 100);
                }
            });
        });
    </script>

<!-- CSS additionnel minimal -->
<style>
    .leaflet-control-alert {
        background: transparent;
        border: none;
        box-shadow: none;
    }

    .leaflet-control-alert .alert {
        max-width: 300px;
        margin: 10px;
    }

    .btn-transport i {
        transition: opacity 0.3s;
    }

    .btn-transport.active i {
        opacity: 1 !important;
    }

    .btn-transport:not(.active) i {
        opacity: 0.7;
    }

    /* .map-container {
        min-height: 400px;
    } */

    /* Animation pour le marqueur utilisateur */
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }

    .user-location-marker div {
        animation: pulse 2s infinite;
    }

    /* Pour les petits écrans */
    /* @media (max-width: 992px) {
        .map-container {
            height: 350px !important;
            margin-bottom: 20px;
        }
    } */
</style>

@endsection