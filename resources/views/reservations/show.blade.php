@extends('layouts.app')

@section('content')
    <div class="main-content-inner wrap-dashboard-content">
        <!-- En-tête avec breadcrumb -->
        <div class="row mb-4">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a class="nav-link active d-flex align-items-center py-2 px-3 rounded bg-danger bg-opacity-10 text-danger"
                                href="{{ Auth::user()->user_type == 'admin' ? route('admin.index') : route('admin.index') }}">
                                <i class="bi bi-house-door me-3 fs-5"></i>
                                <span>Tableau de Bord</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ Auth::user()->user_type == 'admin' ? route('admin.reservation.index') : route('partner.reservation.index') }}">Réservations</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $reservation->code }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Actions principales -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-start align-items-center">
                    <h4 class="mb-0">Réservation {{ $reservation->code }}</h4>
                </div>
                <div class="d-flex justify-content-end align-items-center">
                    @if($reservation->paiement && $reservation->paiement->payment_status == 'paid')
                        @if ($reservation->status == 'pending' && Auth::user()->user_type == 'partner')
                            <form action="{{ route('partner.reservation.confirm', $reservation->uuid) }}" method="POST"
                                class="submitForm">
                                @csrf
                                <button class="btn btn-success" type="submit">
                                    <i class="icon icon-mail"></i> Envoyer confirmation
                                </button>
                            </form>
                        @elseif (($reservation->status == 'pending' || $reservation->status == 'confirmed') && $reservation->is_present == 0 && Auth::user()->user_type == 'partner')
                            <form id="presentForm">
                                @csrf
                                <span>Le client est-il arrivé ? &nbsp; </span>
                                <input type="hidden" name="reservation_uuid" id="reservation_uuid"
                                    value="{{ $reservation->uuid }}">
                                <label for="is_presentNo" class="btn btn-danger" data-value="0">Non</label>
                                <label for="is_presentYes" class="btn btn-success" data-value="1">Oui</label>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Informations client -->
            <div class="col-md-6">
                <div class="widget-box-2 mb-4">
                    <div class="flat-bt-top">
                        <h6 class="title"><i class="icon icon-user me-2"></i>Informations Client</h6>
                    </div>
                    <div class="wrap-content">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Nom complet :</label>
                                    <p class="mb-2">{{ $reservation->nom }} {{ $reservation->prenoms }}</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Email :</label>
                                    <p class="mb-2">
                                        <a href="mailto:{{ $reservation->email }}">{{ $reservation->email }}</a>
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Téléphone :</label>
                                    <p class="mb-2">
                                        <a href="tel:{{ $reservation->phone }}">{{ $reservation->phone }}</a>
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Date de reservation :</label>
                                    <p class="mb-2">{{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations réservation -->
            <div class="col-md-6">
                <div class="widget-box-2 mb-4">
                    <div class="flat-bt-top">
                        <h6 class="title"><i class="icon icon-calendar me-2"></i>Détails de la Réservation</h6>
                    </div>
                    <div class="wrap-content">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Code :</label>
                                    <p class="mb-2 text-primary fw-bold">{{ $reservation->code }}</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Type de séjour :</label>
                                    <p class="mb-2">
                                        <span class="badge bg-info">{{ $reservation->sejour }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Durée :</label>
                                    <p class="mb-2">{{ $reservation->nbr_of_sejour }}
                                        {{ $reservation->sejour == 'Heure' ? 'heure(s)' : 'jour(s)' }}</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Statut :</label>
                                    <p class="mb-2">
                                        @switch($reservation->status)
                                            @case('confirmed')
                                                <span class="badge bg-success">Confirmé</span>
                                            @break

                                            @case('pending')
                                                <span class="badge bg-warning">En attente</span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-danger">Annulé</span>
                                            @break
                                            @case('completed')
                                                <span class="badge bg-secondary">Séjour Terminé</span>
                                            @break

                                            @case('reconducted')
                                                <span class="badge bg-info">Reconduite</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">{{ $reservation->status }}</span>
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            \Carbon\Carbon::setLocale('fr');
        @endphp
        <div class="row">
            <!-- Dates et horaires -->
            <div class="col-md-6">
                <div class="widget-box-2 mb-4">
                    <div class="flat-bt-top">
                        <h6 class="title"><i class="icon icon-clock me-2"></i>Dates et Horaires</h6>
                    </div>
                    <div class="wrap-content">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="fw-bold">Date et heure d'arrivée :</label>
                                    <p class="mb-3 text-success">
                                        <i class="icon icon-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($reservation->start_time)->translatedFormat('l d F Y à H:i') ?? 'Non renseigné' }}
                                        {{-- {{ $reservation->start_time->format('l d F Y à H:i') ??  'Non renseigné' }} --}}
                                    </p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="fw-bold">Date et heure de départ :</label>
                                    <p class="mb-3 text-danger">
                                        <i class="icon icon-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($reservation->end_time)->translatedFormat('l d F Y à H:i') ?? 'Non renseigné' }}
                                        {{-- {{ $reservation->end_time->format('l d F Y à H:i') ??  'Non renseigné' }} --}}
                                    </p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="fw-bold">Durée totale :</label>
                                    <p class="mb-2">
                                        @php
                                            $start_time = \Carbon\Carbon::parse(
                                                $reservation->start_time->format('Y-m-d H:i'),
                                            );
                                            $end_time = \Carbon\Carbon::parse(
                                                $reservation->end_time->format('Y-m-d H:i'),
                                            );
                                            $duration = $start_time->diff($end_time);
                                            if ($reservation->sejour === 'Heure') {
                                                echo $duration->h . 'h ' . $duration->i . 'min';
                                            } else {
                                                echo $duration->days . ' jour(s)';
                                            }
                                        @endphp
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations sur le bien -->
            <div class="col-md-6">
                <div class="widget-box-2 mb-4">
                    <div class="flat-bt-top">
                        <h6 class="title"><i class="icon icon-home me-2"></i>Bien Réservé</h6>
                    </div>
                    <div class="wrap-content">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="fw-bold">Propriété :</label>
                                    <p class="mb-2">{{ $reservation->property->title ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="fw-bold">Hébergement/Chambre :</label>
                                    <p class="mb-2">{{ $reservation->appartement->title ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if ($reservation->partner_uuid)
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="fw-bold">Partenaire :</label>
                                        <p class="mb-2">{{ $reservation->partner->raison_social ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Informations financières -->
            <div class="col-md-6">
                <div class="widget-box-2 mb-4">
                    <div class="flat-bt-top">
                        <h6 class="title"><i class="icon icon-credit-card me-2"></i>Informations Financières</h6>
                    </div>
                    <div class="wrap-content">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Prix unitaire :</label>
                                    <p class="mb-2">{{ number_format($reservation->unit_price, 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Prix total du sejour :</label>
                                    <p class="mb-2 text-primary fw-bold">
                                        {{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                            @if($reservation->paiement)
                                @if ($reservation->paiement->payment_status === 'paid')
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="fw-bold">Montant payé :</label>
                                            <p class="mb-2 text-success">
                                                {{ number_format($reservation->payment_amount, 0, ',', ' ') }} FCFA</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="fw-bold">Reste à payer :</label>
                                            <p
                                                class="mb-2 {{ $reservation->still_to_pay > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($reservation->still_to_pay, 0, ',', ' ') }} FCFA
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                @if (
                                    $reservation->paiement->payment_status === 'paid' &&
                                        ($reservation->paiement->payment_mode !== '' || $reservation->paiement->payment_mode !== null))
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="fw-bold">Mode de paiement :</label>
                                            
                                            <p class="mb-2">
                                                @switch($reservation->paiement->payment_mode)
                                                    @case('PAIEMENTMARCHANDOMPAYCIDIRECT')
                                                        <span class="badge bg-success" style="background-color: #FFA500 !important">Orange Money</span>
                                                    @break

                                                    @case('PAIEMENTMARCHAND_MTN_CI')
                                                        <span class="badge bg-danger" style="background-color: #ffee00 !important">MTN Money</span>
                                                    @break
                                                    @case('PAIEMENTMARCHAND_MOOV_CI')
                                                        <span class="badge bg-danger" style="background-color: #0080ff !important">Moov Money</span>
                                                    @break
                                                    @case('CI_PAIEMENTWAVE_TP')
                                                        <span class="badge bg-info" style="background-color: #00b3ff !important">Wave</span>
                                                    @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $reservation->paiement->payment_mode }}</span>
                                                @endswitch
                                                {{-- {{ ucfirst($reservation->paiement->payment_mode) }}</p> --}}
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="fw-bold">Statut paiement :</label>
                                    <p class="mb-2">
                                        @if($reservation->paiement)
                                            @switch($reservation->paiement->payment_status)
                                                @case('paid')
                                                    <span class="badge bg-success">Payé</span>
                                                @break

                                                @case('pending' || 'unpaid')
                                                    <span class="badge bg-danger">En attente de paiement</span>
                                                @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $reservation->paiement->payment_status }}</span>
                                            @endswitch
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes et remarques -->
            <div class="col-md-6">
                <div class="widget-box-2 mb-4">
                    <div class="flat-bt-top">
                        <h6 class="title"><i class="icon icon-note me-2"></i>Notes et Remarques</h6>
                    </div>
                    <div class="wrap-content">
                        @if ($reservation->notes)
                            <div class="form-group">
                                <label class="fw-bold">Notes client :</label>
                                <div class="p-3 bg-light rounded">
                                    {{ $reservation->notes }}
                                </div>
                            </div>
                        @else
                            <p class="text-muted">Aucune note particulière</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget-box-2">
                    <div class="flat-bt-top">
                        <h6 class="title"><i class="icon icon-history me-2"></i>Historique</h6>
                    </div>
                    <div class="wrap-content">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Réservation créée</h6>
                                    <p class="timeline-text">{{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                            @if ($reservation->paiement && $reservation->paiement->payment_status === 'paid')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Paiement effectué</h6>
                                        <p class="timeline-text">
                                            {{ number_format($reservation->payment_amount, 0, ',', ' ') }} FCFA via
                                            
                                            @switch($reservation->paiement->payment_mode)
                                                @case('PAIEMENTMARCHANDOMPAYCIDIRECT')
                                                    Orange Money
                                                @break

                                                @case('PAIEMENTMARCHAND_MTN_CI')
                                                    MTN Money
                                                @break
                                                @case('PAIEMENTMARCHAND_MOOV_CI')
                                                    Moov Money
                                                @break
                                                @case('CI_PAIEMENTWAVE_TP')
                                                    Wave
                                                @break
                                                @default
                                                    {{ ucfirst($reservation->paiement->payment_mode) }}
                                            @endswitch
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if ($reservation->status === 'confirmed')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Réservation confirmée</h6>
                                        <p class="timeline-text">{{ $reservation->updated_at->format('d/m/Y à H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions en bas -->
        <div class="row my-4">
            <div class="col-md-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center justify-content-start">
                        <a href="{{ Auth::user()->user_type == 'admin' ? route('admin.reservation.index') : route('partner.reservation.index') }}"
                            class="btn btn-secondary">
                            <i class="icon icon-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-end">
                        @if($reservation->paiement && $reservation->paiement->payment_status == 'paid')
                            <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal"
                                data-bs-target="#receiptModal{{ $reservation->uuid }}">
                                <i class="fas fa-receipt"></i> Voir le reçu
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.receip')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('presentForm');
            const reservationUuid = document.getElementById('reservation_uuid').value;

            // Ajouter un event listener sur les labels Oui / Non
            form.querySelectorAll("label[data-value]").forEach(label => {
                label.addEventListener("click", function() {
                    let isPresent = this.getAttribute("data-value");

                    axios.post("/api/customerIsPresent", {
                            reservation_uuid: reservationUuid,
                            is_present: isPresent
                        }, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            console.log(response.data.message);
                            Swal.fire({
                                icon: 'success',
                                text: response.data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                text: "Erreur lors de l'enregistrement"
                            });
                        });
                });
            });
        });
    </script>
@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 5px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }

        .timeline-title {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: 600;
        }

        .timeline-text {
            margin: 0;
            font-size: 13px;
            color: #6c757d;
        }
    </style>
@endpush
