@extends('layouts.app')

@section('content')
    {{-- <div class="main-content-inner wrap-dashboard-content">
        <div class="button-show-hid show-m">
            <span class="body-1">Affichage des réservations</span>
        </div>
        <div class="row">
            
        </div>
        <div class="widget-box-2 wd-listing">
            <div class="row align-items-start justify-content-start">
                <div class="flat-bt-top col-md-12">
                    <h6 class="title">{{ count($reservations) }} Reservations</h6>
                </div>
            </div>
             
            <div class="wrap-table">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Bien reserve</th>
                                <th>Crée le</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservations as $reservation)
                                <tr class="file-delete">
                                    <td>
                                        <span>{{ $reservation->nom ?? '' }} {{ $reservation->prenoms ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $reservation->email ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $reservation->phone ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $reservation->room_id ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $reservation->created_at->format('d/M/Y') ?? '' }}</span>
                                    </td>
                                    <td>
                                        <ul class="list-action d-flex align-items-center justify-content-center">
                                            <li class="border rounded me-2" data-bs-target="#showReservationModal{{ $reservation->id }}" data-bs-toggle="modal">
                                                <a class="item p-2" href="javascript:void(0);"><i class="icon icon-eye"></i></a>
                                            </li>
                                            <li class="border rounded me-2"><a class="item p-2"><i class="icon icon-trash"></i></a></li>
                                        </ul>
                                    </td>
                                </tr>

                                @include('reservations.showModal' , ['reservation' => $reservation])

                            @endforeach
                        </tbody>
                    </table>
                </div>

                <ul class="wd-navigation">
                    <li><a href="#" class="nav-item active">1</a></li>
                    <li><a href="#" class="nav-item">2</a></li>
                    <li><a href="#" class="nav-item">3</a></li>
                    <li><a href="#" class="nav-item"><i class="icon icon-arr-r"></i></a></li>
                </ul>
            </div>
        </div>
    </div> --}}

    <div class="main-content-inne wrap-dashboard-conten">


        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        {{-- <i class="fas fa-bed  me-2 text-danger"></i> --}}
                        <i class="fas fa-hotel  me-2 text-danger"></i>
                         Affichage des réservations
                    </h3>
                </div>
                
            </div>
        </div>

        <!-- Cards Counters -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Reservations</h6>
                                <h3 class="mb-0">{{ count($reservations) }}</h3>
                            </div>
                            <div class="counter-icon text-primary">
                                <i class="fas fa-bed"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">En Attente</h6>
                                <h3 class="mb-0">{{ count($reservations->where('status', 'pending')) }}</h3>
                            </div>
                            <div class="counter-icon text-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Confirmées</h6>
                                <h3 class="mb-0">{{ count($reservations->where('status', 'confirmed')) }}</h3>
                            </div>
                            <div class="counter-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">

            <div class="col-md-4 mb-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Séjour terminé</h6>
                                <h3 class="mb-0">{{ count($reservations->where('status', 'completed')) }}</h3>
                            </div>
                            <div class="counter-icon text-secondary"> 
                                 <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Réconduites</h6>
                                <h3 class="mb-0">{{ count($reservations->where('status', 'reconducted')) }}</h3>
                            </div>
                            <div class="counter-icon text-info">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Annulées</h6>
                                <h3 class="mb-0">{{ count($reservations->where('status', 'cancelled')) }}</h3>
                            </div>
                            <div class="counter-icon text-danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <!-- Filtres et recherche -->
                <div class="widget-box-2 mb-4">
                    <form action="{{ Auth::user()->user_type == 'admin' ? route('admin.reservation.index') : route('partner.reservation.index') }}" method="get">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Rechercher</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" name="search"
                                        value="{{ request('search') }}" placeholder="Nom, email, téléphone, code...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Statut</label>
                                <select class="form-control" name="status">
                                    <option value="">Tous</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                    <option value="reconducted" {{ request('status') == 'reconducted' ? 'selected' : '' }}>Reconduite</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Type de séjour</label>
                                <select class="form-control" name="sejour">
                                    <option value="">Tous</option>
                                    <option value="Jour" {{ request('sejour') == 'Jour' ? 'selected' : '' }}>Jour</option>
                                    <option value="Heure" {{ request('sejour') == 'Heure' ? 'selected' : '' }}>Heure</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button class="btn btn-outline-danger py-3 w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="widget-box-2 wd-listing">
           
            <div class="wrap-table border-0 p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="example2">
                        <thead class="table-light text-nowrap">
                            <tr>
                                <th>Code</th>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Bien réservé</th>
                                <th>Séjour</th>
                                <th>Dates</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Créé le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reservations as $reservation)
                                <tr class="file-delete">
                                    <td>
                                        <span class="fw-bold text-primary">{{ $reservation->code }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ $reservation->nom }} {{ $reservation->prenoms }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="d-block">{{ $reservation->email }}</small>
                                            <small class="text-muted">{{ $reservation->phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span>{{ $reservation->property->title ?? 'N/A' }}</span>
                                        <br>
                                        <small class="text-muted">{{ $reservation->appartement->title ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $reservation->sejour }}</span>
                                        <br>
                                        <small>{{ $reservation->nbr_of_sejour }} {{ $reservation->sejour == 'Heure' ? 'heure(s)' : 'jour(s)' }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            @if($reservation->sejour == 'Heure')
                                            <small class="d-block">Du: {{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y à H:i') }}</small>
                                            <small class="text-muted">Au: {{ \Carbon\Carbon::parse($reservation->end_time)->format('d/m/Y à H:i') }}</small>
                                            @else
                                                <small class="d-block">Du: {{ $reservation->start_time->format('d/m/Y à H:i') }}</small>
                                                <small class="text-muted">Au: {{ $reservation->end_time->format('d/m/Y à H:i') }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</span>
                                            @if($reservation->still_to_pay > 0)
                                                <br><small class="text-danger">Reste: {{ number_format($reservation->still_to_pay, 0, ',', ' ') }} FCFA</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
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
                                                <span class="badge bg-success">Séjour terminé</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $reservation->status }}</span>
                                        @endswitch
                                        <br>
                                        @if($reservation->paiement)
                                            @switch($reservation->paiement->payment_status)
                                                @case('paid')
                                                    <small class="badge bg-success">Payé</small>
                                                    @break
                                                @case('partial')
                                                    <small class="badge bg-warning">Partiel</small>
                                                    @break
                                                @case('pending' || 'unpaid')
                                                    <small class="badge bg-danger">En attente de paiement</small>
                                                    @break
                                                @default
                                                    <small class="badge bg-secondary">{{ $reservation->paiement->payment_status }}</small>
                                            @endswitch
                                        @endif
                                    </td>
                                    <td>
                                        <span>{{ $reservation->created_at->format('d/m/Y') }}</span>
                                        <br>
                                        <small class="text-muted">{{ $reservation->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-icon btn-outline-primary rounded-circle" title="Voir détails" href="{{ Auth::user()->user_type == 'admin' ? route('admin.reservation.show', $reservation->uuid) : route('partner.reservation.show', $reservation->uuid) }}">
                                            <i class="icon icon-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="icon icon-calendar mb-2" style="font-size: 48px;"></i>
                                            <h5 class="fw-semibold">Aucune réservation trouvée</h5>
                                            <p class="text-muted">Aucune réservation ne correspond à vos critères de recherche</p>
                                            <a href="{{ Auth::user()->user_type == 'admin' ? route('admin.reservation.index') : route('partner.reservation.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="fas fa-sync-alt me-1"></i> Réinitialiser les filtres
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
