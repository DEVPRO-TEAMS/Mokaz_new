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
                $now = now();
                $isActive = $start <= $now && $end >= $now;
                $isUpcoming = $start > $now;
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
                        @switch($reservation->status)
                            @case('confirmed')
                                <span class="badge-status confirmed">
                                    <i class="fas fa-check-circle me-1"></i> Confirmée
                                </span>
                                @break
                            @case('pending')
                                <span class="badge-status pending">
                                    <i class="fas fa-clock me-1"></i> En attente
                                </span>
                                @break
                            @case('cancelled')
                                <span class="badge-status cancelled">
                                    <i class="fas fa-times-circle me-1"></i> Annulée
                                </span>
                                @break
                            @case('completed')
                                <span class="badge-status completed">
                                    <i class="fas fa-flag-checkered me-1"></i> Terminée
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
                                <i class="fas fa-play-circle me-1"></i> En cours
                            </span>
                        @elseif($isUpcoming)
                            <span class="badge-status upcoming">
                                <i class="fas fa-clock me-1"></i> À venir
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
                    <div class="modern-card" id="receipt-card">
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
                            @if($reservation->status === 'confirmed' && $isUpcoming)
                                <button class="btn-action btn-cancel" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times-circle me-2"></i>Annuler la réservation
                                </button>
                            @endif
                            
                            <button class="btn-action btn-secondary mt-2" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Imprimer le reçu
                            </button>
                            
                            <a href="{{ route('contact') }}" class="btn-action btn-support mt-2">
                                <i class="fas fa-headset me-2"></i>Contacter le support
                            </a>
                            <a href="{{ route('reservation.reconduction', $reservation->uuid) }}" class="btn-action btn-support mt-2">
                                <i class="fas fa-redo me-1"></i>Reconduire la reservation
                            </a>
                        </div>
                    </div>

                    <!-- Carte informations de paiement -->
                    <div class="modern-card">
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
                                    <span>{{ now()->format('d/m/Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Mode de paiement :</span>
                                    <span class="badge-payment">Carte bancaire</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section carte interactive -->
            <div class="modern-card mt-4">
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
                            <div id="map-location-property-intinerary" class="map-container"></div>
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
            const reservationUuid = reservationData.uuid || null;
            let receiptDownloaded = false;
            let currentMapMode = 'driving';

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
                            <small class="text-muted">${new Date().toLocaleString('fr-FR')}</small>
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

            // Gestionnaire pour les boutons de mode de transport
            document.querySelectorAll('.btn-transport').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.btn-transport').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentMapMode = this.dataset.mode;
                    // Ici, ajouter la logique pour recalculer l'itinéraire avec le nouveau mode
                    updateMapWithMode(currentMapMode);
                });
            });

            // Initialiser la carte
            initializeMap();

            // Initialiser le reçu
            generateReceipt();

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
                if (!receiptDownloaded && reservationData.status !== 'cancelled') {
                    e.preventDefault();
                    e.returnValue = "Veuillez télécharger votre reçu avant de quitter la page.";
                    return e.returnValue;
                }
            });

            // Initialisation de la carte (version simplifiée)
            function initializeMap() {
                const latitude = @json($reservation->property->latitude ?? 0);
                const longitude = @json($reservation->property->longitude ?? 0);
                
                if (latitude && longitude) {
                    // Simuler un chargement
                    setTimeout(() => {
                        document.getElementById('distance-info').textContent = '3.5 km';
                        document.getElementById('duration-info').textContent = '15 min en voiture';
                    }, 1500);
                }
            }

            function updateMapWithMode(mode) {
                // Ici, ajouter la logique pour mettre à jour la carte avec le mode de transport sélectionné
                const modeText = {
                    'driving': 'en voiture',
                    'walking': 'à pied',
                    'bicycling': 'en vélo'
                };
                
                document.getElementById('duration-info').textContent = `Calcul ${modeText[mode]}...`;
                
                // Simuler un calcul
                setTimeout(() => {
                    const durations = {
                        'driving': '15 min',
                        'walking': '45 min',
                        'bicycling': '25 min'
                    };
                    document.getElementById('duration-info').textContent = `${durations[mode]} ${modeText[mode]}`;
                }, 1000);
            }
        });
    </script>
@endsection