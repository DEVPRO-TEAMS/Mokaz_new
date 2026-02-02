@extends('layouts.main')
@section('content')
    <section class="flat-location flat-slider-detail-v1">
        <div class="swiper tf-sw-location" data-preview-lg="2.03" data-preview-md="2" data-preview-sm="2" data-space="20"
            data-centered="true" data-loop="true">
            <div class="swiper-wrapper">
                @foreach ($appart->images as $image)
                    <div class="swiper-slide" style="height: 70vh; width: 100%;">
                        <a href="{{ asset($image->doc_url) }}" target="_blank" data-fancybox="gallery"
                            class="box-imgage-detail d-block">
                            <center>
                                <img src="{{ asset($image->doc_url) }}" class="img-fluid"
                                    style="max-height: 70vh; max-width: 100%; object-fit: cover;" alt="img-appart">
                            </center>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="box-navigation">
                <div class="navigation swiper-nav-next nav-next-location"><span class="icon icon-arr-l"></span></div>
                <div class="navigation swiper-nav-prev nav-prev-location"><span class="icon icon-arr-r"></span></div>
            </div>
        </div>
    </section>

    <section class="flat-section pt-0 flat-property-detail">
        
        @php
            // Récupérer la tarification à l'heure la moins chère
            $tarifHeure = $appart->tarifications->where('sejour', 'Heure')->sortBy('price')->first();

            // Récupérer la tarification à la journée la moins chère
            $tarifJour = $appart->tarifications->where('sejour', 'Jour')->sortBy('price')->first();
        @endphp
        <div class="container border rounded shadow p-4">
            <div class="header-property-detail">
                <a href="javascript:history.back()" class="btn btn-outline-dark mb-3">
                    <i class="fa fa-arrow-left"></i> Retour
                </a>
                <div class="content-top d-flex justify-content-between align-items-center">
                    <div class="box-name">
                        {{-- <a href="#" class="flag-tag primary">En </a> --}}
                        <h4 class="title link">{{ $appart->title }}</h4>
                    </div>
                    <span class="body-1 text-variant-1">À partir de :</span>
                    <div class="box-price d-flex align-items-center flex-column">
                        <div style="display: flex; align-items: center; width: 100%;">
                            <h5>
                                @if ($tarifHeure)
                                    {{ number_format($tarifHeure->price, 0, ',', ' ') }} FCFA &nbsp;
                                @endif
                            </h5>
                            <span class="body-1 text-variant-1">
                                @if ($tarifHeure)
                                    /
                                    {{ $tarifHeure->nbr_of_sejour ?? '' }}{{ $tarifHeure->nbr_of_sejour <= 1 ? 'hre' : 'hres' }}
                                @endif
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; width: 100%;">
                            <h5>
                                @if ($tarifJour)
                                    {{ number_format($tarifJour->price, 0, ',', ' ') }} FCFA &nbsp;
                                @endif
                            </h5>
                            <span class="body-1 text-variant-1">
                                @if ($tarifJour)
                                    / {{ $tarifJour->nbr_of_sejour ?? '' }}jr
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="content-bottom">
                    <div class="info-box">
                        <div class="label text-uppercase">caractéristiques:</div>
                        <ul class="meta">
                            <li class="meta-item"><span class="icon icon-bed"></span> {{ $appart->nbr_room ?? '0' }}
                                Chambres à coucher</li>
                            <li class="meta-item"><span class="icon icon-bathtub"></span>
                                {{ $appart->nbr_bathroom ?? '0' }} Salle de bains</li>
                        </ul>
                    </div>
                    <div class="info-box">
                        <div class="label">LOCALISATION:</div>
                        <p class="meta-item"><span
                                class="icon icon-mapPin"></span>{{ $appart->property->ville->label ?? '' }},
                            {{ $appart->property->pays->label ?? '' }}</p>
                    </div>
                    <ul class="icon-box">
                        <li><a href="#" class="item"><span class="icon icon-heart"></span></a></li>
                    </ul>

                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    @php
                        $fullDescription = $appart->description;
                        $wordLimit = 100;

                        // Nettoyage initial et normalisation des espaces
                        $normalizedContent = preg_replace('/\s+/', ' ', $fullDescription);
                        $textOnly = strip_tags($normalizedContent);

                        // Découpage des mots en gérant les espaces particuliers
                        $words = preg_split('/\s+/', trim($textOnly));
                        $wordCount = count($words);

                        if ($wordCount > $wordLimit) {
                            // On prend les premiers mots (sans HTML)
                            $limitedText = implode(' ', array_slice($words, 0, $wordLimit));

                            // On trouve la position approximative dans le HTML original
                            $pos = 0;
                            $currentWordCount = 0;
                            $pattern = '/(?:<[^>]+>)|(?:\s*\S+\s*)/';
                            preg_match_all($pattern, $fullDescription, $matches, PREG_OFFSET_CAPTURE);

                            foreach ($matches[0] as $match) {
                                if (!preg_match('/^<[^>]+>$/', $match[0])) {
                                    // Si ce n'est pas une balise HTML
                                    $currentWordCount++;
                                    if ($currentWordCount >= $wordLimit) {
                                        $pos = $match[1];
                                        break;
                                        }
                                    }
                                }

                            if ($pos > 0) {
                                $limitedHtml = substr($fullDescription, 0, $pos);
                                $remainingHtml = substr($fullDescription, $pos);
                            } else {
                                // Fallback si la méthode échoue
                                $limitedHtml = Str::words($fullDescription, $wordLimit, '');
                                $remainingHtml = Str::after($fullDescription, $limitedHtml);
                            }
                        } else {
                            $limitedHtml = $fullDescription;
                            $remainingHtml = '';
                        }

                        $hasMoreContent = trim(strip_tags($remainingHtml)) !== '';
                    @endphp

                    <div class="single-property-element single-property-desc">
                        <div class="h7 title fw-7">Description</div>

                        <div class="body-2 text-variant-1">
                            {!! $limitedHtml !!}

                            @if ($hasMoreContent)
                                <span class="more-content collapse" id="collapseDescription">
                                    {!! $remainingHtml !!}
                                </span>

                                <a class="read-more-toggle mt-2 d-inline-block" data-bs-toggle="collapse"
                                    href="#collapseDescription" role="button" aria-expanded="false"
                                    aria-controls="collapseDescription">
                                    <span class="read-more-text">Voir plus</span>
                                    <span class="read-less-text d-none">Voir moins</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="single-property-element single-property-overview">
                        <div class="h7 title fw-7">Aperçu</div>
                        <ul class="info-box row">
                            <li class="item col-lg-4 col-md-6">
                                <a href="#" class="box-icon w-52"><i class="icon icon-house-line"></i></a>
                                <div class="content">
                                    <span class="label">ID:</span>
                                    <span>{{ $appart->id ?? '' }}</span>
                                </div>
                            </li>
                            <li class="item col-lg-4 col-md-6">
                                <a href="#" class="box-icon w-52"><i class="icon icon-arrLeftRight"></i></a>
                                <div class="content">
                                    <span class="label">Type:</span>
                                    <span>{{ $appart->type->libelle ?? '' }}</span>
                                </div>
                            </li>
                            <li class="item col-lg-4 col-md-6">
                                <a href="#" class="box-icon w-52"><i class="icon icon-bed"></i></a>
                                <div class="content">
                                    <span class="label">Chambre à coucher:</span>
                                    <span>{{ $appart->nbr_room ?? '0' }} Chambre{{ $appart->nbr_room > 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </li>
                            <li class="item col-lg-4 col-md-6">
                                <a href="#" class="box-icon w-52"><i class="icon icon-bathtub"></i></a>
                                <div class="content">
                                    <span class="label">Salle de bains:</span>
                                    <span>{{ $appart->nbr_bathroom ?? '0' }}
                                        salle{{ $appart->nbr_bathroom > 1 ? 's' : '' }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    @php

                        if (!function_exists('generateEmbedUrl')) {
                            function generateEmbedUrl($url)
                            {
                                if (!$url) {
                                    return null;
                                }

                                $pattern =
                                    '%(?:youtube(?:-nocookie)?\.com/(?:shorts/|watch\?v=|embed/|v/)|youtu\.be/)([^"&?/ ]{11})%i';

                                preg_match($pattern, $url, $matches);

                                return isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : null;
                            }
                        }

                        $videoUrl = generateEmbedUrl($appart->video_url);
                        $commodities = explode(',', $appart->commodities);
                    @endphp
                    @if (
                        (!empty($videoUrl) && !is_null($videoUrl) && $videoUrl != '') ||
                            $appart->video_url != null ||
                            $appart->video_url != '')
                        <div class="single-property-element single-property-video">
                            <div class="h7 title fw-7">Video</div>
                            <div class="img-video">
                                <img src="{{ asset($appart->image) }}" alt="img-video">
                                <a href="{{ $videoUrl }}" target="_blank" data-fancybox="gallery2"
                                    class="btn-video">
                                    <span class="icon icon-play"></span></a>
                            </div>
                        </div>
                    @endif
                    <div class="single-property-element single-property-info">
                        <div class="h7 title fw-7">Détails de l'hébergement</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Code:</span>
                                    <div class="content fw-7">{{ $appart->code ?? '' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Chambre:</span>
                                    <div class="content fw-7">{{ $appart->nbr_room ?? '0' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Prix:</span>
                                    <div class="content fw-7">
                                        <span class="caption-1 fw-4 text-variant-1">À partir de &nbsp;</span>
                                        @if ($tarifHeure)
                                            {{ number_format($tarifHeure->price, 0, ',', ' ') }} FCFA
                                        @elseif ($tarifJour)
                                            {{ number_format($tarifJour->price, 0, ',', ' ') }} FCFA
                                        @else
                                            Prix non disponible
                                        @endif
                                        <span class="caption-1 fw-4 text-variant-1">
                                            @if ($tarifHeure)
                                                /{{ $tarifHeure->nbr_of_sejour ?? '' }}{{ $tarifHeure->nbr_of_sejour <= 1 ? 'heure' : 'heures' }}
                                            @elseif ($tarifJour)
                                                /{{ $tarifJour->nbr_of_sejour ?? '' }}{{ $tarifJour->nbr_of_sejour <= 1 ? 'jour' : 'jours' }}
                                            @else
                                                Prix non disponible
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Salle de bain:</span>
                                    <div class="content fw-7">{{ $appart->nbr_bathroom ?? '0' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="inner-box">
                                    <span class="label">Type:</span>
                                    <div class="content fw-7">{{ $appart->type->libelle ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (!empty($commodities))
                        <div class="single-property-element single-property-feature">
                            <div class="h7 title fw-7">Commodités et caractéristiques</div>
                            <div class="wrap-feature">
                                <div class="box-feature">
                                    <ul class="row">
                                        @foreach ($commodities as $item)
                                            <li class="feature-item col-md-4">
                                                <i class="fa fa-check"></i> {{ trim($item) }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="single-property-element single-property-map">
                        <div class="h7 title fw-7">Map</div>
                        <div id="map-location-property" class="map-single" data-map-zoom="16" data-map-scroll="true">
                        </div>
                    </div>
                    <div class="single-property-element single-wrapper-review">
                        <div class="box-title-review d-flex justify-content-between align-items-center flex-wrap gap-20">
                            <div class="h7 fw-7">Avis des clients</div>
                            {{-- <a href="#" class="tf-btn">Voir tous les avis</a> --}}
                        </div>
                        <div class="wrap-review">
                            <ul class="box-review">

                            </ul>
                        </div>
                        <div class="wrap-form-comment">
                            <div class="h7">Laisser un commentaire</div>
                            <div id="comments" class="comments">
                                <div class="respond-comment">
                                    <form class="comment-form form-submit" id="comment-form">

                                        <div class="form-wg group-ip">
                                            <fieldset>
                                                <label class="sub-ip">Nom</label>
                                                <input type="text" class="form-control" name="name"
                                                    placeholder="Votre nom et prénom(s)" required>

                                                <input type="hidden" class="form-control" id="appart_uuid"
                                                    name="appart_uuid" value="{{ $appart->uuid }}">

                                                <input type="hidden" class="form-control" name="property_uuid"
                                                    value="{{ $appart->property_uuid ?? '' }}">

                                                <input type="hidden" class="form-control" name="partner_uuid"
                                                    value="{{ $appart->property->partner_uuid ?? '' }}">
                                            </fieldset>
                                            <fieldset>
                                                <label class="sub-ip">Email</label>
                                                <input type="email" class="form-control" name="email"
                                                    placeholder="Votre adresse email" required>
                                            </fieldset>
                                        </div>
                                        <!-- ⭐ Ajout des étoiles -->
                                        <fieldset class="form-wg">
                                            <label class="sub-ip d-block">Votre note</label>
                                            <div class="rating">
                                                <input type="radio" id="star5" name="rating" value="5"
                                                    required />
                                                <label for="star5" title="5 étoiles">★</label>
                                                <input type="radio" id="star4" name="rating" value="4" />
                                                <label for="star4" title="4 étoiles">★</label>
                                                <input type="radio" id="star3" name="rating" value="3" />
                                                <label for="star3" title="3 étoiles">★</label>
                                                <input type="radio" id="star2" name="rating" value="2" />
                                                <label for="star2" title="2 étoiles">★</label>
                                                <input type="radio" id="star1" name="rating" value="1" />
                                                <label for="star1" title="1 étoile">★</label>
                                            </div>
                                        </fieldset>
                                        <fieldset class="form-wg">
                                            <label class="sub-ip">Commentaire</label>
                                            <textarea id="comment-message" name="comment" rows="4" tabindex="4" placeholder="Votre commentaire"
                                                aria-required="true"></textarea>
                                        </fieldset>
                                        <button class="form-wg tf-btn primary" type="submit">
                                            <span>Envoyer</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 px-1">
                    <div class="widget-sidebar fixed-sidebar wrapper-sidebar-right p-0">
                        <div class="hero-section my-4 card">
                            {{-- <div class="card-header text-center">
                                <h6><i class="fas fa-hotel text-danger"></i></h6>
                            </div> --}}

                            <div class="card-body">
                                <p>Découvrez le confort et l'élégance dans notre établissement de prestige</p>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-outline-danger btn-lg w-100" data-bs-toggle="modal"
                                    data-bs-target="#reservationReconductionModal"
                                    title="Les reservations ne sont pas disponible pour l'instant ...">
                                    <i class="fas fa-calendar-plus"></i> Reserver maintenant
                                </button>
                            </div>
                        </div>
                        <div class="widget-box single-property-whychoose bg-surface">
                            <div class="h7 title fw-7">Pourquoi nous choisir ?</div>
                            <ul class="box-whychoose">
                                <li class="item-why">
                                    <i class="icon icon-secure"></i>
                                    Réservation sécurisée
                                </li>
                                <li class="item-why">
                                    <i class="icon icon-guarantee"></i>
                                    Garantie du meilleur prix
                                </li>
                                <li class="item-why">
                                    <i class="icon icon-booking"></i>
                                    Processus de réservation facile
                                </li>
                                <li class="item-why">
                                    <i class="icon icon-support"></i>
                                    Assistance disponible 24h/24 et 7j/7
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>

    @include('reservations.reconduction.reserRecondModal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.querySelector('.read-more-toggle');
            if (toggle) {
                toggle.addEventListener('click', function() {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    this.querySelector('.read-more-text').classList.toggle('d-none', isExpanded);
                    this.querySelector('.read-less-text').classList.toggle('d-none', !isExpanded);
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Récupération des coordonnées depuis les variables Blade (Laravel)
            const latitude = @json($appart->property->latitude ?? 0);
            const longitude = @json($appart->property->longitude ?? 0);


            // Initialisation de la carte
            const map = L.map('map-location-property').setView([latitude, longitude], 16);

            // Chargement des tuiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);
            // L.tileLayer('https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=hgHWK7yUfl2sMR3BI4H2', {
            //     attribution: '&copy; <a href="https://www.maptiler.com/">MapTiler</a>',
            //     maxZoom: 19
            // }).addTo(map);

            // Ajout d’un marqueur à l’emplacement
            L.marker([latitude, longitude]).addTo(map)
                .bindPopup("Emplacement de la propriété")
                .openPopup();
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('comment-form');
            const appartUuid = document.getElementById('appart_uuid').value;
            const commentsList = document.querySelector('.box-review');
            const commentsWrapper = document.querySelector('.wrap-review');
            let currentPage = 1;
            const perPage = 2;

            // Charger les commentaires au démarrage
            loadComments(currentPage);

            // ✅ Soumission du formulaire
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(form);

                try {
                    const response = await fetch("/api/add-comments", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        form.reset();
                        loadComments(1);
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: data.message,
                            showConfirmButton: true,
                            confirmButtonText: 'OK'
                        });
                    }
                } catch (err) {
                    console.error("Erreur Fetch:", err);
                }
            });

            function timeAgo(dateString) {
                const now = new Date();
                const commentDate = new Date(dateString);
                const diff = Math.floor((now - commentDate) / 1000); // différence en secondes

                if (diff < 60) return `il y a ${diff} seconde${diff > 1 ? 's' : ''}`;
                if (diff < 3600) {
                    const minutes = Math.floor(diff / 60);
                    return `il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
                }
                if (diff < 86400) {
                    const hours = Math.floor(diff / 3600);
                    return `il y a ${hours} heure${hours > 1 ? 's' : ''}`;
                }
                const days = Math.floor(diff / 86400);
                return `il y a ${days} jour${days > 1 ? 's' : ''}`;
            }
            // ✅ Charger les commentaires avec pagination
            function loadComments(page = 1) {
                fetch(`/api/get-comments?page=${page}&perPage=${perPage}&appart_uuid=${appartUuid}`)
                    .then(res => res.json())
                    .then(data => {
                        commentsList.innerHTML = "";

                        if (data.data.length === 0) {
                            commentsList.innerHTML = "<li>Aucun commentaire pour le moment.</li>";
                            return;
                        }

                        data.data.forEach(comment => {
                            let rating = parseInt(comment.rating) ||
                                0; // on s'assure que c'est un nombre
                            let stars = "";

                            for (let i = 0; i < 5; i++) {
                                stars +=
                                    `<li class="icon-star ${i < rating ? 'text-warning' : ''}"></li>`;
                            }

                            commentsList.innerHTML += `
                            <li class="list-review-item">
                                <div class="avatar avt-60 round">
                                    <img src="/assets/images/avatar/user-profile.webp" alt="avatar">
                                </div>
                                <div class="content">
                                    <div class="name h7 fw-7 text-black">${comment.name}</div>
                                    <span class="mt-4 d-inline-block date body-3 text-variant-2">
                                        ${timeAgo(comment.created_at)}
                                    </span>
                                    <ul class="mt-8 list-star-note">${stars}</ul>
                                    <p class="mt-12 body-2 text-black">${comment.comment}</p>
                                </div>
                            </li>
                        `;
                        });

                        renderPagination(data.meta);
                    })
                    .catch(err => console.error(err));
            }

            function renderPagination(meta) {
                const totalPages = meta.last_page;
                const currentPage = meta.current_page;
                let paginationHTML = "";

                if (totalPages > 1) {
                    paginationHTML += `
                    <nav class="pt-4">
                        <ul class="pagination justify-content-center">`;

                    // Bouton "Précédent"
                    paginationHTML += `
                        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <a class="page-link" href="#" data-page="${currentPage - 1}">Précédent</a>
                        </li>`;

                    // Pages
                    for (let i = 1; i <= totalPages; i++) {
                        // Afficher toujours la première et dernière page
                        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                            paginationHTML += `
                            <li class="page-item ${i === currentPage ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>`;
                        } else if (i === 2 && currentPage > 3) {
                            paginationHTML +=
                                `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        } else if (i === totalPages - 1 && currentPage < totalPages - 2) {
                            paginationHTML +=
                                `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                    }

                    // Bouton "Suivant"
                    paginationHTML += `
                        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                            <a class="page-link" href="#" data-page="${currentPage + 1}">Suivant</a>
                        </li>`;

                    paginationHTML += `
                        </ul>
                    </nav>`;
                }

                // Supprime l'ancienne pagination et ajoute la nouvelle
                commentsWrapper.querySelector(".pagination")?.remove();
                commentsWrapper.insertAdjacentHTML("beforeend", paginationHTML);

                // Gestion des clics
                document.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (!isNaN(page) && page >= 1 && page <= totalPages) {
                            loadComments(page);
                        }
                    });
                });
            }

        });
    </script>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reservationOld = @json($reservationOld);
        // console.log(reservationOld.id);
       
    })

</script>
@endsection
