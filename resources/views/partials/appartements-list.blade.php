{{-- @forelse ($apparts->where('nbr_available', '>', 0) as $item)
    @php
        $tarifHeure = $item->tarifications->where('sejour', 'Heure')->sortBy('price')->first();
        $tarifJour = $item->tarifications->where('sejour', 'Jour')->sortBy('price')->first();
        $distanceKm = $item->property->distance_km ?? null;
        $tempsPied = $distanceKm ? ($distanceKm * 1000) / 80 : null;
        $tempsVoiture = $distanceKm ? ($distanceKm / 40) * 60 : null;
        $distanceAffiche = formatDistance($distanceKm);
        $tempsPiedAffiche = formatTemps($tempsPied);
        $tempsVoitureAffiche = formatTemps($tempsVoiture);
    @endphp

    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="homeya-box">
            <div class="archive-top">
                <a href="{{ route('appart.detail.show', $item->uuid) }}" class="images-group">
                    <div class="images-style" style="min-height: 260px;max-height: 260px">
                        <img src="{{ asset($item->image) }}" alt="{{ $item->title }}">
                    </div>
                    <div class="top">
                        <ul class="d-flex gap-8">
                            <li class="flag-tag success">{{ !empty ($item->property->category) ? $item->property->category->libelle : '' }}</li>
                        </ul>
                        <ul class="d-flex gap-4">
                            <li class="box-icon w-32">
                                <span class="icon icon-eye"></span>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom">
                        <span class="flag-tag style-2">{{ $item->type->libelle ?? '' }}</span>
                    </div>
                </a>
                <div class="content">
                    <div class="h7 text-capitalize fw-7">
                        <a href="{{ route('appart.detail.show', $item->uuid) }}"
                            class="link">{{ $item->title ?? '' }}</a>
                    </div>
                    
                    @if (!empty($item->property->address_name))
                        <div class="desc"><i class="fs-16 icon icon-mapPin"></i><p>{{ $item->property->address_name ?? '' }}</p> </div>
                    @endif
                     <ul class="meta-list">
                        <li class="item">
                            <i class="icon icon-bed"></i>
                            <span>{{ $item->nbr_room ?? 0 }}</span>
                        </li>
                        <li class="item">
                            <i class="icon icon-bathtub"></i>
                            <span>{{ $item->nbr_bathroom ?? 0 }}</span>
                        </li>
                        <li class="item">
                            <i class="icon icon-money"></i>
                            <span>
                                @if ($tarifHeure)
                                    À partir de {{ number_format($tarifHeure->price, 0, ',', ' ') }}
                                    FCFA/{{ $tarifHeure->nbr_of_sejour ?? '' }}{{ $tarifHeure->nbr_of_sejour <= 1 ? 'hre' : 'hres' }}
                                @elseif ($tarifJour)
                                    À partir de {{ number_format($tarifJour->price, 0, ',', ' ') }}
                                    FCFA/{{ $tarifJour->nbr_of_sejour ?? '' }}{{ $tarifJour->nbr_of_sejour <= 1 ? 'jr' : 'jrs' }}
                                @else
                                    Prix non disponible
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            @if ($distanceKm)
                <div class="archive-bottom d-flex flex-column justify-content-between align-items-center">
                    <ul class="meta-list p-3 border border-1 border-bottom d-flex justify-content-between w-100">
                        <li class="item d-flex align-items-center">
                            <i class="fa-solid fa-ruler-horizontal me-1 text-dark"></i>
                            <span>{{ $distanceAffiche ?? '' }}</span>
                        </li>
                        @if ($tempsPiedAffiche)
                            <li class="item d-flex align-items-center">
                                <i class="fa-solid fa-person-walking me-1 text-dark"></i>
                                <span>{{ $tempsPiedAffiche ?? '' }}</span>
                            </li>
                        @endif
                        @if ($tempsVoitureAffiche)
                            <li class="item d-flex align-items-center">
                                <i class="fa-solid fa-car-side me-1 text-dark"></i>
                                <span>{{ $tempsVoitureAffiche ?? '' }}</span>
                            </li>
                        @endif
                    </ul>
                    <p class="item d-flex align-items-center">
                        <i class="fa-solid fa-map-location-dot me-1 text-dark"></i>
                        <span class="fw-bold fs-6">{{ $item->property->city_name ?? '' }} - {{ $item->property->commune_name ?? '' }}</span>
                    </p>
                </div>
            @endif
        </div>
    </div>
@empty
    @if (request()->has('search') || request()->has('type') || request()->has('location') || request()->has('categorie') || request()->has('rooms') || request()->has('bathrooms') || request()->has('sejour') || request()->has('commodities') || request()->has('min_price') || request()->has('max_price'))
        <div class="d-flex flex-column align-items-center w-100">
            <i class="fas fa-home fa-3x text-muted pb-3 opacity-50"></i>
            <h5 class="fw-semibold">Aucun hébergement trouvé</h5>
            <p class="text-muted">Aucun hébergement ne correspond à vos critères de recherche</p>
            <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="resetFiltersBtn">
                <i class="fas fa-sync-alt me-1"></i> Réinitialiser les filtres
            </button>
        </div>
    @else
        <div class="d-flex flex-column align-items-center w-100">
            <i class="fas fa-home fa-3x text-muted pb-3 opacity-50"></i>
            <h5 class="fw-semibold">Aucun hébergement pour le moment</h5>
        </div>
    @endif
@endforelse --}}

@forelse ($apparts->where('nbr_available', '>', 0) as $item)
    @php
        $tarifHeure = $item->tarifications->where('sejour', 'Heure')->sortBy('price')->first();
        $tarifJour = $item->tarifications->where('sejour', 'Jour')->sortBy('price')->first();
        $distanceKm = $item->property->distance_km ?? null;
        $tempsPied = $distanceKm ? ($distanceKm * 1000) / 80 : null;
        $tempsVoiture = $distanceKm ? ($distanceKm / 40) * 60 : null;
        $distanceAffiche = formatDistance($distanceKm);
        $tempsPiedAffiche = formatTemps($tempsPied);
        $tempsVoitureAffiche = formatTemps($tempsVoiture);
    @endphp

    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="homeya-box">
            <div class="archive-top">
                <a href="{{ route('appart.detail.show', $item->uuid) }}" class="images-group">
                    <div class="images-style" style="min-height: 260px;max-height: 260px">
                        <img src="{{ asset($item->image) }}" alt="{{ $item->title }}" loading="lazy">
                    </div>
                    <div class="top">
                        <ul class="d-flex gap-8">
                            <li class="flag-tag success">{{ !empty($item->property->category) ? $item->property->category->libelle : '' }}</li>
                        </ul>
                        <ul class="d-flex gap-4">
                            <li class="box-icon w-32">
                                <span class="icon icon-eye"></span>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom">
                        <span class="flag-tag style-2">{{ $item->type->libelle ?? '' }}</span>
                    </div>
                </a>
                <div class="content">
                    <div class="h7 text-capitalize fw-7">
                        <a href="{{ route('appart.detail.show', $item->uuid) }}" class="link">{{ $item->title ?? '' }}</a>
                    </div>
                    
                    @if (!empty($item->property->address_name))
                        <div class="desc">
                            <i class="fs-16 icon icon-mapPin"></i>
                            <p>{{ $item->property->address_name ?? '' }}</p>
                        </div>
                    @endif
                    
                    <ul class="meta-list">
                        <li class="item">
                            <i class="icon icon-bed"></i>
                            <span>{{ $item->nbr_room ?? 0 }}</span>
                        </li>
                        <li class="item">
                            <i class="icon icon-bathtub"></i>
                            <span>{{ $item->nbr_bathroom ?? 0 }}</span>
                        </li>
                        <li class="item">
                            <i class="icon icon-money"></i>
                            <span>
                                @if ($tarifHeure)
                                    À partir de {{ number_format($tarifHeure->price, 0, ',', ' ') }}
                                    FCFA/{{ $tarifHeure->nbr_of_sejour ?? '' }}{{ $tarifHeure->nbr_of_sejour <= 1 ? 'hre' : 'hres' }}
                                @elseif ($tarifJour)
                                    À partir de {{ number_format($tarifJour->price, 0, ',', ' ') }}
                                    FCFA/{{ $tarifJour->nbr_of_sejour ?? '' }}{{ $tarifJour->nbr_of_sejour <= 1 ? 'jr' : 'jrs' }}
                                @else
                                    Prix non disponible
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            @if ($distanceKm)
                <div class="archive-bottom d-flex flex-column justify-content-between align-items-center">
                    <ul class="meta-list p-3 border border-1 border-bottom d-flex justify-content-between w-100">
                        <li class="item d-flex align-items-center">
                            <i class="fa-solid fa-ruler-horizontal me-1 text-dark"></i>
                            <span>{{ $distanceAffiche ?? '' }}</span>
                        </li>
                        @if ($tempsPiedAffiche)
                            <li class="item d-flex align-items-center">
                                <i class="fa-solid fa-person-walking me-1 text-dark"></i>
                                <span>{{ $tempsPiedAffiche ?? '' }}</span>
                            </li>
                        @endif
                        @if ($tempsVoitureAffiche)
                            <li class="item d-flex align-items-center">
                                <i class="fa-solid fa-car-side me-1 text-dark"></i>
                                <span>{{ $tempsVoitureAffiche ?? '' }}</span>
                            </li>
                        @endif
                    </ul>
                    <p class="item d-flex align-items-center mt-2">
                        <i class="fa-solid fa-map-location-dot me-1 text-dark"></i>
                        <span class="fw-bold fs-6">
                            {{ $item->property->city_name ?? '' }} 
                            @if($item->property->commune_name)
                                - {{ $item->property->commune_name ?? '' }}
                            @endif
                        </span>
                    </p>
                </div>
            @endif
        </div>
    </div>
@empty
    @php
        $hasSearch = request()->has('search') || request()->has('type') || request()->has('location') || 
                     request()->has('categorie') || request()->has('rooms') || request()->has('bathrooms') || 
                     request()->has('sejour') || request()->has('commodities') || request()->has('min_price') || 
                     request()->has('max_price');
    @endphp

    @if ($hasSearch)
        <div class="col-12">
            <div class="d-flex flex-column align-items-center justify-content-center py-5">
                <div class="text-center">
                    <i class="fas fa-home fa-4x text-muted pb-3 opacity-50"></i>
                    <h4 class="fw-semibold mb-3">Aucun hébergement trouvé</h4>
                    <p class="text-muted mb-4">Aucun hébergement ne correspond à vos critères de recherche</p>
                    <button type="button" class="btn btn-outline-danger" id="resetFiltersBtn">
                        <i class="fas fa-sync-alt me-2"></i>Réinitialiser les filtres
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="col-12">
            <div class="d-flex flex-column align-items-center justify-content-center py-5">
                <i class="fas fa-home fa-4x text-muted pb-3 opacity-50"></i>
                <h4 class="fw-semibold">Aucun hébergement pour le moment</h4>
                <p class="text-muted">Soyez le premier à découvrir nos prochains hébergements</p>
            </div>
        </div>
    @endif
@endforelse