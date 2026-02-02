<!-- MODAL Bootstrap simplifiée -->
<div class="modal fade" id="showApartmentModal{{ $apartement->code }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-light">
                <h5 class="modal-title text-white">
                    <i class="bi bi-building text-white"></i> {{ $apartement->title ?? '' }}
                    <span class="badge bg-secondary">{{ $apartement->code ?? '' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                @php
                    // Récupérer la tarification à l'heure la moins chère
$tarifHeure = $apartement->tarifications->where('sejour', 'Heure')->sortBy('price')->first();

// Récupérer la tarification à la journée la moins chère
$tarifJour = $apartement->tarifications->where('sejour', 'Jour')->sortBy('price')->first();
                @endphp
                <!-- Image principale -->
                <div class="position-relative">
                    <img src="{{ asset($apartement->image) ?? '' }}" class="img-fluid rounded"
                        style="width: 100%; height: 360px; object-fit: cover" alt="{{ $apartement->title ?? '' }}">
                    @if ($apartement->nbr_available > 0)
                        <span class="badge bg-success position-absolute top-0 start-0 m-3">
                            <i class="bi bi-check-circle"></i> Disponible
                        </span>
                    @else
                        <span class="badge bg-danger position-absolute top-0 start-0 m-3">
                            <i class="bi bi-x-circle"></i> Indisponible
                        </span>
                    @endif

                    <span class="badge bg-danger position-absolute bottom-0 end-0 m-3 fs-6">
                        @if ($tarifHeure)
                            À partir de {{ number_format($tarifHeure->price, 0, ',', ' ') }}
                            FCFA/{{ $tarifHeure->nbr_of_sejour ?? '' }}h
                        @elseif ($tarifJour)
                            À partir de {{ number_format($tarifJour->price, 0, ',', ' ') }}
                            FCFA/{{ $tarifJour->nbr_of_sejour ?? '' }}j
                        @else
                            Prix non disponible
                        @endif
                    </span>
                </div>

                <div class="pt-4">
                    <h6 class="text-danger"><i class="bi bi-images"></i> Images</h6>
                    <div class="row">
                        @foreach ($apartement->images as $image)
                            <div class="col-4">
                                <img src="{{ asset($image->doc_url) ?? '' }}" alt=""
                                    class="img-fluid rounded img-thumbnail">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Infos générales -->
                <div class="row pt-4">
                    <div class="col-md-6">
                        <h6 class="text-danger"><i class="bi bi-info-circle"></i> Informations générales</h6>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item"><i class="bi bi-house-door"></i> Type : <strong>
                                    {{ $apartement->type->libelle ?? '' }} </strong></li>
                            <li class="list-group-item"><i class="bi bi-bed"></i> Chambres :
                                <strong>{{ $apartement->nbr_room ?? 0 }}</strong>
                            </li>
                            <li class="list-group-item"><i class="bi bi-droplet"></i> Salles de bain :
                                <strong>{{ $apartement->nbr_bathroom ?? 0 }}</strong>
                            </li>
                            <li class="list-group-item"><i class="bi bi-geo-alt"></i> Code Propriété :
                                <strong>{{ $apartement->property->code ?? '' }}</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-danger"><i class="bi bi-gear"></i> État & Gestion</h6>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item">
                                <i class="bi bi-toggle-on"></i> État :
                                <strong>
                                    @if ($apartement->etat == 'actif')
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-check-circle me-1"></i> Active
                                        </span>
                                    @elseif ($apartement->etat == 'inactif')
                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">
                                            <i class="fas fa-times-circle me-1"></i> Inactive
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning">
                                            <i class="fas fa-clock me-1"></i> En attente
                                        </span>
                                    @endif
                                </strong>
                            </li>
                            <li class="list-group-item"><i class="bi bi-person-plus"></i> Créé par : <strong>
                                    {{ $apartement->createdBy->email ?? '' }} </strong></li>
                            <li class="list-group-item"><i class="bi bi-person-gear"></i> MAJ par : <strong>
                                    {{ $apartement->updatedBy->email ?? 'Aucune MAJ' }} </strong></li>
                            <li class="list-group-item"><i class="bi bi-calendar3"></i> Dernière MAJ : <strong>
                                    {{ $apartement->updated_at->diffForHumans() ?? '' }}</strong></li>
                        </ul>
                    </div>
                </div>

                <!-- Description -->
                <div class="pt-4">
                    <h6 class="text-danger"><i class="bi bi-align-start"></i> Description</h6>
                    <div class="bg-white border border-danger rounded-3 p-3">
                        <p class="text-muted">
                            {!! $apartement->description ?? '' !!}
                        </p>
                    </div>
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

                    $videoUrl = generateEmbedUrl($apartement->video_url);
                    $commodities = explode(',', $apartement->commodities);
                @endphp
                <!-- Équipements -->
                @if (!empty($commodities))
                    <div class="pt-4">
                        <h6 class="text-danger"><i class="bi bi-star"></i> Équipements & Commodités</h6>
                        <div class="row py-4">
                            <div class="col-12">
                                @foreach ($commodities as $item)
                                    <span class="badge rounded-pill bg-light text-secondary p-3 fs-6 border"><i
                                            class="bi bi-star"></i> {{ trim($item) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif



                <div class="mt-4">
                    <h6 class="text-danger"><i class="bi bi-camera-video"></i> Visite virtuelle</h6>
                    <div class="ratio ratio-16x9 mt-2">
                        <iframe src="{{ $videoUrl ?? '' }}" title="Visite virtuelle" allowfullscreen></iframe>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer btn-container">
                <input type="hidden" name="appart_uuid" id="appart_uuid" value="{{ $apartement->uuid }}">
                @if (Auth::user()->user_type == 'admin')
                    @if ($apartement->property->etat == 'actif')
                        @if ($apartement->etat == 'pending')
                            <button class="btn btn-success me-2">
                                <a class="deleteConfirmation text-white" data-uuid="{{ $apartement->uuid }}"
                                    data-type="confirmation_redirect" data-placement="top" data-token="{{ csrf_token() }}"
                                    data-url="{{ route('admin.approveAppart', $apartement->uuid) }}"
                                    data-title="Vous êtes sur le point d'accepter l'hebergement {{ $apartement->code }} "
                                    data-id="{{ $apartement->uuid }}" data-param="0"
                                    data-route="{{ route('admin.approveAppart', $apartement->uuid) }}" title="Approuver">
                                    <i class="fas fa-check" style="cursor: pointer"></i> Accepter</a>
                            </button>

                            <button class="btn btn-danger">
                                <a class="deleteConfirmation  text-white" data-uuid="{{ $apartement->uuid }}"
                                    data-type="confirmation_redirect" data-placement="top"
                                    data-token="{{ csrf_token() }}"
                                    data-url="{{ route('admin.rejectAppart', $apartement->uuid) }}"
                                    data-title="Vous êtes sur le point de rejeter l'hebergement {{ $apartement->code }}"
                                    data-id="{{ $apartement->uuid }}" data-param="0"
                                    data-route="{{ route('admin.rejectAppart', $apartement->uuid) }}" title="Rejeter">
                                    <i class="fas fa-times" style="cursor: pointer"></i> Rejeter</a>
                            </button>
                        @elseif ($apartement->etat == 'actif')
                            <button class="btn btn-danger">
                                <a class="deleteConfirmation  text-white" data-uuid="{{ $apartement->uuid }}"
                                    data-type="confirmation_redirect" data-placement="top"
                                    data-token="{{ csrf_token() }}"
                                    data-url="{{ route('admin.rejectAppart', $apartement->uuid) }}"
                                    data-title="Vous êtes sur le point de desactiver l'hebergement {{ $apartement->code }}"
                                    data-id="{{ $apartement->uuid }}" data-param="0"
                                    data-route="{{ route('admin.rejectAppart', $apartement->uuid) }}" title="Rejeter">
                                    <i class="fas fa-times" style="cursor: pointer"></i> Désactiver</a>
                            </button>
                        @elseif ($apartement->etat == 'inactif')
                            <button class="btn btn-success me-2">
                                <a class="deleteConfirmation text-white" data-uuid="{{ $apartement->uuid }}"
                                    data-type="confirmation_redirect" data-placement="top"
                                    data-token="{{ csrf_token() }}"
                                    data-url="{{ route('admin.approveAppart', $apartement->uuid) }}"
                                    data-title="Vous êtes sur le point d'activer l'hebergement {{ $apartement->code }} "
                                    data-id="{{ $apartement->uuid }}" data-param="0"
                                    data-route="{{ route('admin.approveAppart', $apartement->uuid) }}" title="Approuver">
                                    <i class="fas fa-check" style="cursor: pointer"></i> Activer</a>
                            </button>
                        @endif
                    @endif
                @else
                    <button type="button" class="btn btn-danger deleteAppart"> <i class="bi bi-trash"></i>
                        Supprimer</button>
                    <a href="{{ route('partner.apartments.edit', [$apartement->uuid, $apartement->property->uuid]) }}"
                        class="btn btn-success"> <i class="bi bi-pencil"></i> Modifier</a>
                @endif
            </div>

        </div>
    </div>
</div>
