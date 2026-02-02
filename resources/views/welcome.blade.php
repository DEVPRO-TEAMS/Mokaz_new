@extends('layouts.main')
@section('content')
    <style>
        @media (max-width: 768px) {

            /* Moyens écrans et plus */
            .flat-map.my-content {
                margin-top: 80px;
            }
        }
    </style>
    <!-- Map -->
    <section class="flat-map my-content">

        <div id="map" class="top-map" data-map-zoom="16" data-map-scroll="true"></div>

        <div class="container">
            <div class="wrap-filter-search">
                <div class="flat-tab flat-tab-form">
                    <ul class="nav-tab-form style-3 justify-content-center" role="tablist">
                        <li class="nav-tab-item" role="presentation">
                            <a href="#forRent" class="nav-link-item active" data-bs-toggle="tab">Localisation </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" role="tabpanel">
                            <div class="form-sl pt-5">
                                <form id="searchAppartsForm" action="{{ route('welcome') }}" method="get">
                                    <div class="wd-find-select shadow-st">
                                        <div class="inner-group">
                                            <div class="form-group-1 search-form form-style">
                                                <label>Mot-clé</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Par Mot-clé." name="search"
                                                    value="{{ request('search') }}">
                                            </div>

                                            <div class="form-group-2 form-style">
                                                <label for="ville">Ville</label>
                                                <div class="group-ip">
                                                    <select name="ville" id="ville" class="nice-select form-select selection">
                                                    <option value="" selected>Toutes les villes</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->code . ' ' . $city->label }}"
                                                                {{ request('ville') == $city->code . ' ' . $city->label ? 'selected' : '' }}>
                                                                {{ $city->label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group-2 form-style d-none">
                                                <label for="">Localisation</label>
                                                <div class="group-ip">
                                                    <input type="text" class="form-control"
                                                        placeholder="Par Localisation" name="location"
                                                        value="{{ request('location') }}">
                                                </div>
                                            </div>

                                            <div class="form-group-3 form-style">
                                                <label>Type</label>
                                                <div class="group-select">
                                                    <select name="type" id="type" class="nice-select form-select">
                                                        <option value="" selected>Tous</option>
                                                        @foreach ($typeAppart as $type)
                                                            <option value="{{ $type->libelle }}"
                                                                {{ request('type') == $type->libelle ? 'selected' : '' }}>
                                                                {{ $type->libelle }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group-3 form-style">
                                                <label>Categorie</label>
                                                <div class="group-select">
                                                    <select name="categorie" id="categorie" class="nice-select form-select">
                                                        <option value="">Tous</option>
                                                        @foreach ($categories as $categorie)
                                                            <option value="{{ $categorie->libelle }}"
                                                                {{ request('categorie') == $categorie->libelle ? 'selected' : '' }}>
                                                                {{ $categorie->libelle }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group-4 box-filter">
                                                <a class="filter-advanced pull-right">
                                                        <span class="icon icon-faders"></span> 
                                                        <span class="text-1">Avancé</span>                                                                      
                                                </a>
                                            </div>
                                        </div>

                                        <input type="hidden" name="lat" id="user_lat" value="{{ request('lat') }}">
                                        <input type="hidden" name="lng" id="user_lng" value="{{ request('lng') }}">

                                        <button type="submit" class="tf-btn primary">Rechercher</button>
                                    </div>
                                    <div class="wd-search-form">
                                        <div class="grid-1 group-box group-price">
                                            <div class="widget-price">
                                                <div class="box-title-price">
                                                    <span class="title-price">Prix</span>
                                                    <div class="caption-price">
                                                        <span>entre</span>
                                                        <span id="slider-range-value1" class="fw-7"></span>
                                                        <span>et</span>
                                                        <span id="slider-range-value2" class="fw-7"></span>
                                                    </div>
                                                </div>

                                                <div id="slider-range"
                                                    data-min="{{ $minPrice }}"
                                                    data-max="{{ $maxPrice }}">
                                                </div>

                                                <div class="slider-labels">
                                                    <input type="hidden"
                                                        name="min_price"
                                                        id="min_price"
                                                        value="{{ request('min_price', $minPrice) }}">

                                                    <input type="hidden"
                                                        name="max_price"
                                                        id="max_price"
                                                        value="{{ request('max_price', $maxPrice) }}">
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="grid-1 group-box">
                                            <div class="group-select grid-3">
                                                <div class="box-select">
                                                    <label class="title-select text-variant-1">Nombre de chambres</label>
                                                    <input type="number" class="form-control" placeholder="Chambres" name="rooms" value="{{ request('rooms') }}">
                                                </div>
                                                <div class="box-select">
                                                    <label class="title-select text-variant-1">Nombre de salle de bains</label>
                                                    <input type="number" class="form-control" placeholder="Salle de bains" name="bathrooms" value="{{ request('bathrooms') }}">
                                                </div>
                                                <div class="box-select">
                                                    <label class="title-select text-variant-1">Type de séjour</label>
                                                    <select name="sejour" id="sejour" class="nice-select form-select">
                                                        <option value="">Tous</option>
                                                        <option value="Heure">séjour en heures</option>
                                                        <option value="Jour">séjour en jours</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @if(count($commodities))
                                            <div class="group-checkbox">
                                                <div class="text-1">Commodités :</div>

                                                <div class="group-amenities mt-8 grid-6">
                                                    @foreach($commodities as $index => $commodity)
                                                        <div class="box-amenities">
                                                            <fieldset class="amenities-item">
                                                                <input
                                                                    type="checkbox"
                                                                    name="commodities[]"
                                                                    class="tf-checkbox style-1"
                                                                    id="cb{{ $index }}"
                                                                    value="{{ $commodity }}"
                                                                    {{ in_array($commodity, request('commodities', [])) ? 'checked' : '' }}
                                                                >
                                                                <label for="cb{{ $index }}" class="text-cb-amenities">
                                                                    {{ ucfirst($commodity) }}
                                                                </label>
                                                            </fieldset>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    @php
        // ✅ Fonctions utilitaires définies une seule fois
        if (!function_exists('formatTemps')) {
            function formatTemps($minutes)
            {
                if (!$minutes) {
                    return null;
                }
                if ($minutes >= 60) {
                    $heures = floor($minutes / 60);
                    $mins = round($minutes % 60);
                    return $heures . 'h ' . ($mins > 0 ? $mins . 'min' : '');
                }
                return round($minutes) . ' min';
            }
        }

        if (!function_exists('formatDistance')) {
            function formatDistance($km)
            {
                if (!$km) {
                    return null;
                }
                $metres = $km * 1000;
                return $metres >= 1000
                    ? number_format($km, 1, ',', ' ') . ' km'
                    : number_format($metres, 0, ',', ' ') . ' m';
            }
        }
    @endphp
    <!-- Map -->
    <!-- Recommended -->
    <section class="flat-section-v5 bg-surface flat-recommended flat-recommended-v2">
        <div class="container">
            <div class="box-title style-2 text-center wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                <h5 class="mt-4">Découvrez les meilleures propriétés pour un séjour de rêve</h5>
            </div>

            <div class="row wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                @forelse ($apparts->where('nbr_available', '>', 0) as $item)
                    @php
                        // Récupérer les tarifications les moins chères
                        $tarifHeure = $item->tarifications->where('sejour', 'Heure')->sortBy('price')->first();
                        $tarifJour = $item->tarifications->where('sejour', 'Jour')->sortBy('price')->first();

                        // ✅ Récupérer la distance si disponible
                        $distanceKm = $item->property->distance_km ?? null;

                        // ✅ Calculs des temps de trajet
                        $tempsPied = $distanceKm ? ($distanceKm * 1000) / 80 : null; // 80 m/min à pied
                        $tempsVoiture = $distanceKm ? ($distanceKm / 40) * 60 : null; // 40 km/h en voiture


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
                                    
                                    {{-- <div class="desc"><i class="fs-16 icon icon-mapPin"></i><p>{{ $item->address ?? '' }}</p> </div> --}}
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
                            {{-- 🚗 Distance + Temps de trajet --}}
                            @if ($distanceKm)
                                <div class="archive-bottom d-flex flex-column justify-content-between align-items-center">
                                    <ul class="meta-list p-3 border border-1 border-bottom d-flex justify-content-between w-100">
                                        {{-- 📏 Distance --}}
                                        <li class="item d-flex align-items-center">
                                            <i class="fa-solid fa-ruler-horizontal me-1 text-dark"></i>
                                            <span>{{ $distanceAffiche ?? '' }}</span>
                                        </li>

                                        {{-- 🚶 Temps à pied --}}
                                        @if ($tempsPiedAffiche)
                                            <li class="item d-flex align-items-center">
                                                <i class="fa-solid fa-person-walking me-1 text-dark"></i>
                                                <span>{{ $tempsPiedAffiche ?? '' }}</span>
                                            </li>
                                        @endif

                                        {{-- 🚗 Temps en voiture --}}
                                        @if ($tempsVoitureAffiche)
                                            <li class="item d-flex align-items-center">
                                                <i class="fa-solid fa-car-side me-1 text-dark"></i>
                                                <span>{{ $tempsVoitureAffiche ?? '' }}</span>
                                            </li>
                                        @endif
                                    </ul>
                                    {{-- <hr> --}}
                                    <h6 class="item d-flex align-items-center">
                                        <i class="fa-solid fa-map-location-dot me-1 text-dark"></i>
                                        <span>{{ $item->property->ville->label ?? '' }} - {{ $item->property->pays->label ?? '' }}</span>
                                    </h6>
                                </div>
                            @endif
                            {{-- <div class="archive-bottom d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-8 align-items-center">
                                    <div class="avatar avt-40 round">
                                        <img src="images/avatar/avt-6.jpg" alt="avt">
                                    </div>
                                    <span>Arlene McCoy</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h6>$7250,00</h6>
                                    <span class="text-variant-1">/SqFT</span>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    {{-- <div class="col-xl-4 col-md-6">
                        
                        <div class="homeya-box style-3">
                                <div class="images-group">
                                    <div class="images-style">
                                        <img src="{{ asset($item->image) }}" alt="{{ $item->title }}">
                                    </div>
                               
                                    <div class="top">
                                        <ul class="d-flex gap-8"></ul>
                                        <ul class="d-flex gap-4">
                                            <li class="box-icon w-32 d-none">
                                                <span class="icon icon-heart"></span>
                                            </li>
                                            <li class="box-icon w-32">
                                                <a href="{{ route('appart.detail.show', $item->uuid) }}">
                                                    <span class="icon icon-eye"></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="top">
                                        <span class="flag-tag style-2">{{ $item->type->libelle }} {{ !empty($item->property->category) ? ' | ' . $item->property->category->libelle : '' }}</span>
                                    </div>

                                    <div class="content">
                                        <a href="{{ route('appart.detail.show', $item->uuid) }}" class="link">
                                            <div class="title text-1 text-capitalize">
                                                <a href="{{ route('appart.detail.show', $item->uuid) }}"
                                                    class="link text-white">{{ $item->title ?? '' }}</a>
                                            </div>

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

                                            
                                            @if ($distanceKm)
                                                <ul class="meta-list justify-content-between w-100">
                                                    <li class="item d-flex align-items-center">
                                                        <i class="fa-solid fa-ruler-horizontal me-1 text-white"></i>
                                                        <span>{{ $distanceAffiche ?? '' }}</span>
                                                    </li>

                                                    @if ($tempsPiedAffiche)
                                                        <li class="item d-flex align-items-center">
                                                            <i class="fa-solid fa-person-walking me-1 text-white"></i>
                                                            <span>{{ $tempsPiedAffiche ?? '' }}</span>
                                                        </li>
                                                    @endif

                                                    @if ($tempsVoitureAffiche)
                                                        <li class="item d-flex align-items-center">
                                                            <i class="fa-solid fa-car-side me-1 text-white"></i>
                                                            <span>{{ $tempsVoitureAffiche ?? '' }}</span>
                                                        </li>
                                                    @endif
                                                    <li class="item d-flex align-items-center">
                                                        <i class="fa-solid fa-map-location-dot me-1 text-white"></i>
                                                        <span>{{ $item->property->ville->label ?? '' }} - {{ $item->property->pays->label ?? '' }}</span>
                                                    </li>
                                                </ul>
                                            @endif
                                        </a>
                                    </div>
                                </div>
                        </div>
                    </div> --}}
                @empty
                    {{-- Vérifie s’il y a une recherche effectuée --}}
                    @if (request()->has('search') || request()->has('type') || request()->has('location') || request()->has('categorie') || request()->has('rooms') || request()->has('bathrooms') || request()->has('sejour') || request()->has('commodities') || request()->has('min_price') || request()->has('max_price'))
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-home fa-3x text-muted pb-3 opacity-50"></i>
                            <h5 class="fw-semibold">Aucun hébergement trouvé</h5>
                            <p class="text-muted">Aucun hébergement ne correspond à vos critères de recherche</p>
                            <a href="{{ route('welcome') }}" class="btn btn-sm btn-outline-danger mt-2">
                                <i class="fas fa-sync-alt me-1"></i> Réinitialiser les filtres
                            </a>
                        </div>
                    @else
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-home fa-3x text-muted pb-3 opacity-50"></i>
                            <h5 class="fw-semibold">Aucun hébergement pour le moment</h5>
                        </div>
                    @endif
                @endforelse
            </div>

            <div class="nav-pagination pt-4">
                {{ $apparts->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

            @if ($apparts->count() > 0)
                <div class="text-center pt-4">
                    <a href="{{ route('appart.all') }}" class="tf-btn primary size-1">Voir tous les biens</a>
                </div>
                {{-- <div class="text-center pt-4">
                    <a href="{{ route('contratPrestataire', 'jkfeejk') }}" target="_blank" class="tf-btn primary size-1">Contrat </a>
                </div> --}}
            @endif
        </div>
    </section>
    <!-- End Recommended -->


    <section class="flat-section-v3 flat-location bg-surface">
        <div class="container-full">
            <div class="box-title text-center wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                <div class="text-subtitle text-primary">Explorer les villes</div>
                <h4 class="mt-4">Notre emplacement pour vous</h4>
            </div>

            <div class="wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
                <div class="swiper tf-sw-location overlay" data-preview-lg="4.1" data-preview-md="3" data-preview-sm="2"
                    data-space="30" data-centered="true" data-loop="true">

                    <div class="swiper-wrapper">
                        @foreach ($locations as $location => $properties)
                            @php
                                $firstProperty = $properties->first();
                                $city = $firstProperty->ville?->label ?? 'Ville inconnue';
                                $country = $firstProperty->pays?->label ?? 'Pays inconnu';
                                $count = $properties->count();
                                $image =
                                    $firstProperty->ville?->locationImage?->image ??
                                    'assets/images/location/abidjan.jpg';
                            @endphp
                            @if ($firstProperty->ville?->locationImage)
                                <div class="swiper-slide">
                                    <a href="javascript:void(0)" class="box-location">
                                        <div class="image">
                                            <img src="{{ asset($image) }}" alt="image-location" style="min-height: 265px;max-height: 265px">
                                        </div>
                                        <div class="content">
                                            <span class="sub-title">{{ $count }} Propriété(s)</span>
                                            <h6 class="title">{{ $country }}, {{ $city }}</h6>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="box-navigation">
                        <div class="navigation swiper-nav-next nav-next-location"><span class="icon icon-arr-l"></span>
                        </div>
                        <div class="navigation swiper-nav-prev nav-prev-location"><span class="icon icon-arr-r"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Location -->
    <!-- Property  -->
    <!-- Property  -->
    @if ($bestApparts->count() > 0)
        <section class="flat-section flat-property">
            <div class="container">
                <div class="box-title style-1 wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                    <div class="box-left">
                        <div class="text-subtitle text-primary">Recommandations</div>
                        <h4 class="mt-4">Meilleure valeur immobilière</h4>
                    </div>
                    <a href="{{ route('appart.all') }}" class="tf-btn primary size-1">Voir Plus</a>
                </div>
                <div class="wrap-property">
                    <div class="box-left  wow fadeInLeftSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                        @php
                            $firstAppart = $bestApparts->first();
                            $tarifHeure = $firstAppart->tarifications
                                ->where('sejour', 'Heure')
                                ->sortBy('price')
                                ->first();
                            $tarifJour = $firstAppart->tarifications->where('sejour', 'Jour')->sortBy('price')->first();
                        @endphp

                        <div class="homeya-box lg">
                            <div class="archive-top">
                                {{-- Exemple d’image --}}
                                <a href="{{ route('appart.detail.show', $firstAppart->uuid) }}" class="images-group">
                                    <div class="images-style">
                                        <img src="{{ $firstAppart->image ?? '' }}" alt="img" style="min-height: 270px;max-height: 270px">
                                    </div>
                                    <div class="top">
                                        <ul class="d-flex gap-8">
                                            {{-- <li class="flag-tag success style-3">En vedette</li> --}}
                                        </ul>
                                        <ul class="d-flex gap-4">
                                            {{-- <li class="box-icon w-40"><span class="icon icon-arrLeftRight"></span></li> --}}
                                            <li class="box-icon w-40 d-none"><span class="icon icon-heart"></span></li>
                                            <li class="box-icon w-40"><span class="icon icon-eye"></span></li>
                                        </ul>
                                    </div>
                                    <div class="bottom"><span
                                            class="flag-tag style-2">{{ $firstAppart->type->libelle }} {{ !empty($firstAppart->property->category) ? ' | ' . $firstAppart->property->category->libelle : '' }}</span></div>
                                </a>
                                <div class="content">
                                    <h5 class="text-capitalize"><a
                                            href="{{ route('appart.detail.show', $firstAppart->uuid) }}"
                                            class="link">{{ $firstAppart->title }}</a></h5>
                                    <div class="desc"><i class="icon icon-mapPin"></i>
                                        <p>{{ $firstAppart->property->adresse ?? 'Adresse non définie' }}</p>
                                    </div>
                                    <p class="note">{!! Str::limit($firstAppart->description, 100) !!}</p>
                                    <ul class="meta-list">
                                        <li class="item"><i
                                                class="icon icon-bed"></i><span>{{ $firstAppart->nbr_room }}</span></li>
                                        <li class="item"><i
                                                class="icon icon-bathtub"></i><span>{{ $firstAppart->nbr_bathroom }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="archive-bottom d-flex justify-content-between align-items-center">
                                <div class="avatar avt-40 round">
                                    <img src="{{ asset('assets/images/avatar/user-profile.webp') }}" alt="avt">
                                </div>
                                <div class="d-flex align-items-center">
                                    <h6>
                                        @if ($tarifHeure)
                                            À partir de {{ number_format($tarifHeure->price, 0, ',', ' ') }} FCFA
                                        @elseif ($tarifJour)
                                            À partir de {{ number_format($tarifJour->price, 0, ',', ' ') }} FCFA
                                        @else
                                            Prix non disponible
                                        @endif
                                    </h6>
                                    <span class="text-variant-1">
                                        @if ($tarifHeure)
                                            /{{ $tarifHeure->nbr_of_sejour ?? '' }}{{ $tarifHeure->nbr_of_sejour <= 1 ? 'heure' : 'heures' }}
                                        @elseif ($tarifJour)
                                            /{{ $tarifJour->nbr_of_sejour ?? '' }}{{ $tarifJour->nbr_of_sejour <= 1 ? 'jour' : 'jours' }}
                                        @else
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-right wow fadeInRightSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
                        @foreach ($bestApparts->slice(1) as $item)
                            @php
                                $tarifHeure = $item->tarifications->where('sejour', 'Heure')->sortBy('price')->first();
                                $tarifJour = $item->tarifications->where('sejour', 'Jour')->sortBy('price')->first();
                            @endphp

                            <div class="homeya-box list-style-1">
                                <a href="{{ route('appart.detail.show', $item->uuid) }}" class="images-group">
                                    <div class="images-style">
                                        <img src="{{ $item->image ?? '' }}" alt="img" style="min-height: 270px;max-height: 270px;">
                                    </div>
                                    <div class="top">
                                        <ul class="d-flex gap-4 flex-wrap flex-column">
                                            {{-- <li class="flag-tag success">En vedette</li> --}}
                                        </ul>
                                        <ul class="d-flex gap-4">
                                            {{-- <li class="box-icon w-28"><span class="icon icon-arrLeftRight"></span></li> --}}
                                            <li class="box-icon w-28 d-none"><span class="icon icon-heart"></span></li>
                                            <li class="box-icon w-28"><span class="icon icon-eye"></span></li>
                                        </ul>
                                    </div>
                                    <div class="bottom"><span class="flag-tag style-2">{{ $item->type->libelle }} {{ !empty($item->property->category) ? ' | ' . $item->property->category->libelle : '' }}</span>
                                    </div>
                                </a>
                                <div class="content">
                                    <div class="archive-top">
                                        <div class="h7 text-capitalize fw-7"><a
                                                href="{{ route('appart.detail.show', $item->uuid) }}"
                                                class="link">{{ $item->title }}</a></div>
                                        <div class="desc"><i class="icon icon-mapPin"></i>
                                            <p>{{ $item->property->adresse ?? '' }}</p>
                                        </div>
                                        <ul class="meta-list">
                                            <li class="item"><i
                                                    class="icon icon-bed"></i><span>{{ $item->nbr_room }}</span></li>
                                            <li class="item"><i
                                                    class="icon icon-bathtub"></i><span>{{ $item->nbr_bathroom }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avt-40 round">
                                                <img src="{{ asset('assets/images/avatar/user-profile.webp') }}"
                                                    alt="avt">
                                            </div>
                                            <div class="h7 fw-7">
                                                @if ($tarifHeure)
                                                    À partir de {{ number_format($tarifHeure->price, 0, ',', ' ') }} FCFA /
                                                    {{ $tarifHeure->nbr_of_sejour ?? '' }}{{ $tarifHeure->nbr_of_sejour <= 1 ? 'hr' : 'hrs' }}
                                                @elseif ($tarifJour)
                                                    À partir de {{ number_format($tarifJour->price, 0, ',', ' ') }} FCFA /
                                                    {{ $tarifJour->nbr_of_sejour ?? '' }}{{ $tarifJour->nbr_of_sejour <= 1 ? 'jr' : 'jrs' }}
                                                @else
                                                    Prix non disponible
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!-- End Property  -->
    <!-- Testimonial -->
    @if (count($testimonials) > 0)
        <section class="flat-section flat-testimonial-v4 wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
            <div class="container">
                <div class="box-titl text-center mb-5">
                    <div class="text-subtitle text-primary">Témoignages</div>
                    <h4 class="mt-4">Ce que disent les gens</h4>
                </div>
                <div class="swiper tf-sw-testimonial" data-preview-lg="2" data-preview-md="2" data-preview-sm="2"
                    data-space="30">
                    <div class="swiper-wrapper">
                        @foreach($testimonials as $item)
                            <div class="swiper-slide">
                                <div class="box-tes-item style-2">
                                    <ul class="list-star">
                                        <li class="icon icon-star"></li>
                                        <li class="icon icon-star"></li>
                                        <li class="icon icon-star"></li>
                                        <li class="icon icon-star"></li>
                                        <li class="icon icon-star"></li>
                                    </ul>
                                    <p class="note body-1">
                                        "{!! $item->content !!}"
                                    </p>
                                    <div class="box-avt d-flex align-items-center gap-12">
                                        <div class="avatar avt-60 round">
                                            <img src="{{ asset('assets/images/avatar/user-profile.webp') }}" alt="avatar">
                                        </div>
                                        <div class="info">
                                            <div class="h7 fw-7">{{ $item->name }}</div>
                                            <p class="text-variant-1 mt-4">{{ $item->fonction }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="sw-pagination sw-pagination-testimonial"></div>

                </div>
            </div>
        </section>
    @endif
    <!-- End Testimonial -->
    <!-- banner -->
    <section class="flat-section pt-0 flat-banner wow fadeInUpSmall" data-wow-delay=".2s" data-wow-duration="2000ms">
        <div class="container">
            <div class="wrap-banner bg-surface">
                <div class="box-left">
                    <div class="box-title">
                        <div class="text-subtitle text-primary">Devenir partenaire</div>
                        <h4 class="mt-4">Inscrivez vos propriétés sur Mokaz, rejoignez-nous maintenant !</h4>
                    </div>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#demandPartnariaModal"
                        class="tf-btn primary size-1">Devenir hébergeur</a>
                </div>
                <div class="box-right">
                    <img src="{{ asset('assets/images/banner/banner.png') }}" alt="image">
                </div>
            </div>
        </div>
    </section>


    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier si les coordonnées sont déjà présentes
            const latInput = document.getElementById('user_lat');
            const lngInput = document.getElementById('user_lng');
            // const lngInput = document.getElementById('search');

            if (!latInput.value || !lngInput.value) {
                // Récupérer automatiquement la position
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        latInput.value = position.coords.latitude;
                        lngInput.value = position.coords.longitude;

                        // Soumettre automatiquement le formulaire
                        document.getElementById('searchAppartsForm').submit();
                    }, function(error) {
                        console.warn("Impossible de récupérer la position :", error.message);
                        // On peut charger les appartements sans géolocalisation si refusé
                    });
                } else {
                    console.warn("Géolocalisation non supportée par le navigateur.");
                }
            }
        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('user_lat');
            const lngInput = document.getElementById('user_lng');
            const form = document.getElementById('searchAppartsForm');

            // On ne lance la détection que si les champs sont vides
            if (!latInput.value || !lngInput.value) {
                if (navigator.geolocation) {
                    
                    const options = {
                        enableHighAccuracy: true,
                        timeout: 5000, // On attend max 5 secondes
                        maximumAge: 0
                    };

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            latInput.value = position.coords.latitude;
                            lngInput.value = position.coords.longitude;
                            // Soumission uniquement si on a récupéré les données
                            form.submit();
                        }, 
                        function(error) {
                            // Gestion explicite des erreurs pour le débug
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    console.warn("L'utilisateur a refusé la demande de géolocalisation.");
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    console.warn("L'information de localisation est indisponible.");
                                    break;
                                case error.TIMEOUT:
                                    console.warn("La demande de localisation a expiré.");
                                    break;
                                case error.UNKNOWN_ERROR:
                                    console.warn("Une erreur inconnue est survenue.");
                                    break;
                            }
                        }, 
                        options
                    );
                } else {
                    console.warn("Géolocalisation non supportée par ce navigateur.");
                }
            }
        });
    </script>
    <!-- end banner -->
@endsection
