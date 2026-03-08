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
    {{-- Vérifie s'il y a une recherche effectuée --}}
    @php
        $hasSearch = request()->has('search') || 
                     request()->has('type') || 
                     request()->has('location') || 
                     request()->has('categorie') || 
                     request()->has('rooms') || 
                     request()->has('bathrooms') || 
                     request()->has('sejour') || 
                     request()->has('commodities') || 
                     request()->has('min_price') || 
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
                
                {{-- Suggestions --}}
                <div class="mt-5 w-100">
                    <h5 class="text-center mb-4">Suggestions :</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-tag fa-2x text-primary mb-3"></i>
                                    <h6>Modifiez vos filtres</h6>
                                    <p class="small text-muted">Essayez d'élargir votre fourchette de prix ou de supprimer certains filtres</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-map-marker-alt fa-2x text-primary mb-3"></i>
                                    <h6>Changez de localisation</h6>
                                    <p class="small text-muted">Explorez d'autres quartiers ou villes à proximité</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-alt fa-2x text-primary mb-3"></i>
                                    <h6>Vérifiez les disponibilités</h6>
                                    <p class="small text-muted">Certains hébergements peuvent être complets pour vos dates</p>
                                </div>
                            </div>
                        </div>
                    </div>
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

@push('styles')
<style>
/* Styles supplémentaires pour les cartes d'hébergement */
.homeya-box {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.homeya-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.archive-top {
    flex: 1;
}

.images-style {
    position: relative;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
}

.images-style img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.homeya-box:hover .images-style img {
    transform: scale(1.05);
}

.flag-tag {
    z-index: 2;
}

.meta-list {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin: 0;
    padding: 0;
    list-style: none;
}

.meta-list .item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.meta-list .item i {
    font-size: 1.1rem;
    color: #6c757d;
}

.archive-bottom {
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa;
    border-radius: 0 0 12px 12px;
    padding: 0.75rem;
}

.archive-bottom .meta-list {
    width: 100%;
    justify-content: space-between;
}

.archive-bottom .meta-list .item {
    font-size: 0.85rem;
}

.archive-bottom .meta-list .item i {
    font-size: 0.9rem;
}

/* Style pour le bouton de réinitialisation */
#resetFiltersBtn {
    transition: all 0.3s ease;
}

#resetFiltersBtn:hover {
    transform: scale(1.05);
}

/* Style pour les cartes de suggestion */
.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
}

/* Animation pour le chargement */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.col-xl-4.col-lg-6.col-md-6 {
    animation: fadeIn 0.5s ease forwards;
}

/* Style pour le message "Aucun résultat" */
.d-flex.flex-column.align-items-center {
    min-height: 400px;
    width: 100%;
}

/* Responsive */
@media (max-width: 768px) {
    .meta-list {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .archive-bottom .meta-list {
        flex-direction: row;
        flex-wrap: wrap;
    }
    
    .archive-bottom .meta-list .item {
        flex: 1 1 auto;
    }
}

/* Loading state */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Tooltip personnalisé */
[data-tooltip] {
    position: relative;
    cursor: help;
}

[data-tooltip]:before {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 0.5rem;
    background-color: rgba(0,0,0,0.8);
    color: white;
    font-size: 0.8rem;
    border-radius: 4px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s;
    z-index: 10;
}

[data-tooltip]:hover:before {
    opacity: 1;
    visibility: visible;
}
</style>
@endpush

@push('scripts')
<script>
// Animation supplémentaire pour les cartes
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un effet de parallaxe léger sur les images
    const cards = document.querySelectorAll('.homeya-box');
    
    cards.forEach(card => {
        const img = card.querySelector('.images-style img');
        
        card.addEventListener('mousemove', function(e) {
            if (!img) return;
            
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const moveX = (x - centerX) / 20;
            const moveY = (y - centerY) / 20;
            
            img.style.transform = `scale(1.05) translate(${moveX}px, ${moveY}px)`;
        });
        
        card.addEventListener('mouseleave', function() {
            if (!img) return;
            img.style.transform = 'scale(1) translate(0, 0)';
        });
    });
    
    // Gestion du clic sur les boutons de réinitialisation
    const resetBtn = document.getElementById('resetFiltersBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Trouver le formulaire parent
            const form = document.getElementById('searchAppartsForm');
            if (form) {
                // Réinitialiser tous les champs
                form.reset();
                
                // Vider les champs cachés de géolocalisation
                const latInput = document.getElementById('user_lat');
                const lngInput = document.getElementById('user_lng');
                if (latInput) latInput.value = '';
                if (lngInput) lngInput.value = '';
                
                // Déclencher une nouvelle recherche
                if (typeof window.performSearch === 'function') {
                    window.performSearch();
                }
            }
        });
    }
});
</script>
@endpush