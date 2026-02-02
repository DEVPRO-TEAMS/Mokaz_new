@extends('layouts.main')

@section('content')
<style>
    /* Effet de survol sur les cartes */
    .appart-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        border: 1px solid #eee !important;
    }
    .appart-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    /* Scrollbar personnalisée pour la liste */
    .scroll-container::-webkit-scrollbar {
        width: 6px;
    }
    .scroll-container::-webkit-scrollbar-thumb {
        background-color: #ddd;
        border-radius: 10px;
    }
    /* Style pour les petits textes */
    .text-xs { font-size: 0.75rem; }
    .price-tag { font-size: 1.1rem; font-weight: 800; color: #0d6efd; }
</style>

<div class="container-fluid bg-light min-vh-100">
    <div class="row pt-4">
        
        {{-- COL 8 : LISTE DES APPARTEMENTS --}}
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark">Unités disponibles</h4>
                <span class="badge bg-white text-dark shadow-sm border px-3 py-2">
                    {{ $apparts->where('nbr_available', '>', 0)->count() }} appartements trouvés
                </span>
            </div>

            <div class="scroll-container" style="max-height: 85vh; overflow-y: auto; padding-right: 15px;">
                <div class="row">
                    @forelse($apparts->where('nbr_available', '>', 0) as $appart)
                        @php
                            $tarifHeure = $appart->tarifications->where('sejour', 'Heure')->sortBy('price')->first();
                            $tarifJour = $appart->tarifications->where('sejour', 'Jour')->sortBy('price')->first();
                        @endphp

                        <div class="col-md-6 col-xl-4 mb-4">
                            <div class="card h-100 shadow-sm border-0 appart-card">
                                
                                {{-- IMAGE AVEC BADGE DISPONIBILITÉ --}}
                                <div class="position-relative">
                                    @if ($appart && $appart->image)
                                        <img src="{{ asset($appart->image) }}" class="card-img-top" style="height:190px; object-fit:cover">
                                        
                                    @else
                                        <img src="https://placehold.co/600x400" class="card-img-top" style="height:190px; object-fit:cover">
                                        
                                    @endif
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-dark opacity-75">
                                        <i class="fas fa-door-open me-1"></i> {{ $appart->nbr_available }} dispos
                                    </span>
                                </div>

                                <div class="card-body d-flex flex-column p-3">
                                    <div class="mb-2">
                                        <h6 class="fw-bold mb-1 text-truncate">{{ $appart->title }}</h6>
                                        <p class="text-muted text-xs mb-0">
                                            <i class="fas fa-map-marker-alt"></i> {{ $appart->property->ville->label ?? 'N/A' }}, {{ $appart->property->pays->label ?? '' }}
                                        </p>
                                    </div>

                                    <div class="d-flex gap-3 mb-3 pb-2 border-bottom">
                                        <span class="text-xs text-secondary"><i class="fas fa-bed"></i> {{ $appart->nbr_room }} Ch.</span>
                                        <span class="text-xs text-secondary"><i class="fas fa-bath"></i> {{ $appart->nbr_bathroom }} SdB</span>
                                    </div>

                                    {{-- SECTION PRIX --}}
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-end">
                                            <div>
                                                @if($tarifHeure)
                                                    <div class="text-xs text-muted mb-n1">À partir de</div>
                                                    <span class="fw-bold text-dark">{{ number_format($tarifHeure->price, 0, ',', ' ') }} <small>FCFA</small></span>
                                                    <small class="text-xs text-muted">/{{ $tarifHeure->nbr_of_sejour }}h</small>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                @if($tarifJour)
                                                    <div class="price-tag mb-0">
                                                        {{ number_format($tarifJour->price, 0, ',', ' ') }} <small class="text-xs text-dark">FCFA</small>
                                                    </div>
                                                    <div class="text-xs text-muted">/ {{ $tarifJour->nbr_of_sejour }} jour(s)</div>
                                                @endif
                                            </div>
                                        </div>

                                        <a class="btn btn-primary w-100 mt-3 fw-bold btn-sm btn-recondui" 
                                        href="{{ route('reconduction.show', ['uuid' => $appart->uuid,'reservation_uuid' => $reservation->uuid]) }}">Choisir cet appartement</a>

                                        {{-- <button class="btn btn-primary w-100 mt-3 fw-bold btn-sm btn-reconduire"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reserRecondModal"
                                            data-appart="{{ $appart->title }}"
                                            data-price="{{ $tarifJour->price ?? 0 }}"
                                            data-image="{{ asset($appart->image) }}">
                                            Choisir cet appartement
                                        </button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="opacity-25 mb-3">
                            <p class="text-muted">Aucun appartement disponible pour le moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- COL 4 : RÉCAPITULATIF STICKY --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top:20px; border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-bold mb-0 text-uppercase text-xs tracking-wider">Votre sélection actuelle</h6>
                </div>
                
                <div class="p-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset($reservation->appartement->image ?? '') }}" class="rounded me-3" style="width: 80px; height: 60px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $reservation->appartement->title ?? 'N/A' }}</h6>
                            <small class="text-muted">{{ $reservation->appartement->property->ville->label ?? '' }}</small>
                        </div>
                    </div>

                    <div class="bg-light rounded p-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs text-muted">Total appartement</span>
                            <span class="fw-bold">{{ number_format($reservation->total_price, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs text-muted">Acompte payé (10%)</span>
                            <span class="text-success fw-bold">{{ number_format($reservation->payment_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-xs text-muted">Statut</span>
                            @if($reservation->statut_paiement === 'paid')
                                <span class="badge bg-success-soft text-success border border-success px-2 py-1" style="font-size: 0.65rem;">PAYÉ</span>
                            @else
                                <span class="badge bg-danger-soft text-danger border border-danger px-2 py-1" style="font-size: 0.65rem;">EN ATTENTE</span>
                            @endif
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start" style="border-radius: 10px;">
                        <i class="fas fa-info-circle me-2 mt-1"></i>
                        <small style="line-height: 1.4;">
                            <strong>Règle de changement :</strong> Le nouveau bien doit être d'une valeur égale ou supérieure à celle-ci.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection