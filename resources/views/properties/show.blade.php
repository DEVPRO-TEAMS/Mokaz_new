@extends('layouts.app')

@section('content')
    <style>
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #cd380f 0%, #a02f0c 100%);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
        }

        .modal-header h5 {
            font-weight: 600;
            margin: 0;
        }

        .modal-header .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .modal-header .btn-close:hover {
            opacity: 1;
        }

        .partner-info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #cd380f;
            transition: all 0.3s ease;
            animation: slideInUp 0.5s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .partner-info-card:hover {
            box-shadow: 0 5px 15px rgba(205, 56, 15, 0.1);
            transform: translateY(-2px);
        }

        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .info-section {
            margin-bottom: 2rem;
        }

        .info-section h6 {
            color: #cd380f;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-section h6::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, #cd380f, transparent);
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item:hover {
            background: rgba(205, 56, 15, 0.05);
            margin: 0 -1rem;
            padding-left: 1rem;
            padding-right: 1rem;
            border-radius: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-value {
            color: #212529;
            flex: 1;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-approved {
            background: #d1edff;
            color: #0c5460;
            border: 1px solid #b8daff;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .contact-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .contact-btn {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            border: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-email {
            background: #cd380f;
            color: white;
        }

        .btn-phone {
            background: #28a745;
            color: white;
        }

        .btn-website {
            background: #007bff;
            color: white;
        }

        .message-box {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .message-box::before {
            content: '"';
            position: absolute;
            top: -10px;
            left: 20px;
            font-size: 3rem;
            color: #cd380f;
            font-family: serif;
        }

        .timeline-item {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.5rem;
            width: 12px;
            height: 12px;
            background: #cd380f;
            border-radius: 50%;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 1.2rem;
            width: 2px;
            height: calc(100% + 1rem);
            background: #e9ecef;
        }

        .timeline-item:last-child::after {
            display: none;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            background: #f8f9fa;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .fade-in {
            animation: fadeIn 0.6s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #cd380f;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
    <div class="main-content-inne pt-5 mt-5 wrap-dashboard-content">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-home me-2 text-danger"></i> Détails de la propriété
                    </h3>
                </div>
            </div>
        </div>

        <div class=" widget-box-2 wd-listing">
            <div class="row align-items-center justify-content-center">
                <div class="flat-bt-top col-md-9">
                    <h3 class="title"></h3>
                </div>
                <div class="flat-bt-top col-md-3 text-end">
                    @if (Auth::user()->user_type == 'admin')
                        @if($property->etat == 'pending')

                            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#propertyCategoriziedModal{{ $property->uuid }}">
                                <i class="fas fa-check" style="cursor: pointer"></i> Accepter
                            </button>
                            {{-- <button class="btn btn-success me-2">
                                <a class="deleteConfirmation text-white" data-uuid="{{$property->uuid}}"
                                data-type="confirmation_redirect" data-placement="top"
                                data-token="{{ csrf_token() }}"
                                data-url="{{route('admin.approveProperty',$property->uuid)}}"
                                data-title="Vous êtes sur le point d'accepter la propriété {{$property->code}} "
                                data-id="{{$property->uuid}}" data-param="0"
                                data-route="{{route('admin.approveProperty',$property->uuid)}}" title="Approuver">
                                <i class="fas fa-check" style="cursor: pointer"></i> Accepter</a>
                            </button> --}}
                        
                            <button class="btn btn-danger">
                                <a class="deleteConfirmation  text-white" data-uuid="{{$property->uuid}}"
                                data-type="confirmation_redirect" data-placement="top"
                                data-token="{{ csrf_token() }}"
                                data-url="{{route('admin.rejectProperty',$property->uuid)}}"
                                data-title="Vous êtes sur le point de rejeter la propriété {{$property->code}} "
                                data-id="{{$property->uuid}}" data-param="0"
                                data-route="{{route('admin.rejectProperty',$property->uuid)}}" title="Rejeter">
                                <i class="fas fa-times" style="cursor: pointer"></i> Rejeter</a>
                            </button>
                        @elseif ($property->etat == 'actif')
                            @if (empty($property->category))
                                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#propertyCategoriziedModal{{ $property->uuid }}">
                                    <i class="fas fa-tags" style="cursor: pointer"></i> Catégoriser la propriété
                                </button>
                            @endif
                            <button class="btn btn-danger">
                                <a class="deleteConfirmation  text-white" data-uuid="{{$property->uuid}}"
                                data-type="confirmation_redirect" data-placement="top"
                                data-token="{{ csrf_token() }}"
                                data-url="{{route('admin.rejectProperty',$property->uuid)}}"
                                data-title="Vous êtes sur le point de desactiver la propriété {{$property->code}} "
                                data-id="{{$property->uuid}}" data-param="0"
                                data-route="{{route('admin.rejectProperty',$property->uuid)}}" title="Rejeter">
                                <i class="fas fa-times" style="cursor: pointer"></i> Désactiver</a>
                            </button>
                        @elseif($property->etat == 'inactif')
                            {{-- <button class="btn btn-success">
                                <a class="deleteConfirmation  text-white" data-uuid="{{$property->uuid}}"
                                data-type="confirmation_redirect" data-placement="top"
                                data-token="{{ csrf_token() }}"
                                data-url="{{route('admin.approveProperty',$property->uuid)}}"
                                data-title="Vous êtes sur le point d'activer la propriété {{$property->code}} "
                                data-id="{{$property->uuid}}" data-param="0"
                                data-route="{{route('admin.approveProperty',$property->uuid)}}" title="Approuver">
                                <i class="fas fa-check" style="cursor: pointer"></i> Activer</a>
                            </button> --}}
                            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#propertyCategoriziedModal{{ $property->uuid }}">
                                <i class="fas fa-check" style="cursor: pointer"></i> Activer
                            </button>
                        @endif
                    @else
                        <a class="tf-btn primary" href="{{ route('partner.apartments.create', $property->uuid) }}"><i class="icon icon-plus"></i>
                            Ajouter un hébergement</a>
                    @endif
                </div>
            </div>
            @include('admins.pages.propreties.propretyCategoriziedModal')

            <div class="wrap-table p-3">
                <!-- Informations Personnelles -->
                <div class="info-section fade-in table-responsive">
                    <h6><i class="fas fa-building text-danger"></i>Informations sur la propriété #{{ $property->code }}</h6>
                    <div class="partner-info-card">

                        <div class="row g-0">
                            <div class="property-image-container col-12 mb-3" style="height: 380px; background: url('{{ asset($property->image) }}') no-repeat center center; background-size: cover; border-radius: 10px;">
                                
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-tag me-2"></i>
                                        Titre :
                                    </div>
                                    <div class="info-value" id="show-first-name">{{ $property->title ?? '' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-handshake me-2"></i>
                                        Partenaire :
                                    </div>
                                    <div class="info-value" id="show-last-name">{{ $property->partner->raison_social ?? '' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-toggle-on me-2"></i>Statut :
                                    </div>
                                    <div class="info-value" id="show-last-name">
                                        <p class="mb-0">
                                            @if ($property->etat == 'actif')
                                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @elseif ($property->etat == 'inactif')
                                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Inactive
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning">
                                                    <i class="fas fa-clock me-1"></i> En attente
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-handshake me-2"></i>
                                        Créer par :
                                    </div>
                                    <div class="info-value" id="show-last-name">{{ $property->createdBy->email ?? '' }}</div>
                                </div>
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-toggle-on me-2"></i>Catégorie :
                                    </div>
                                    <div class="info-value" id="show-last-name">
                                        <p class="mb-0">
                                            <span class="badge rounded-pill bg-{{!empty($property->category) ? 'success' : 'primary'}} text-light">
                                                <i class="fas fa-tags me-1"></i>
                                                {{ $property->category->libelle ?? 'Non categorisé pour le moment' }}
                                            </span>
                                        </p>
                                    </div>
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="info-section fade-in table-responsive">
                    <h6><i class="fas fa-map-marker-alt me-2 text-danger"></i>Localisation</h6>
                    <div class="partner-info-card">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-building text-muted"></i>
                                        Adresse :
                                    </div>
                                    <div class="info-value" id="show-company">{{ $property->address ?? '' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-home text-muted"></i>
                                        Ville :
                                    </div>
                                    <div class="info-value" id="show-property-type">{{ $property->ville->label ?? '' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-map-marker-alt text-muted"></i>
                                        Pays :
                                    </div>
                                    <div class="info-value" id="show-activity-zone">{{ $property->pays->label ?? '' }}</div>
                                </div>
                            </div>


                        </div>
                        <style>
                            #map-location-property {
                                height: 300px;
                                border-radius: 10px;
                            }
                        </style>

                        <div class="contact-actions row mt-3">
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-calendar-alt text-muted"></i>
                                    Emplacement :
                                </div>
                            </div>
                            <div class="col-12" style="height: 300px">
                                <div id="map-location-property"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message -->
                <div class="info-section fade-in">
                    <h6><i class="fas fa-comment-dots"></i>Description</h6>
                    <div class="message-box">
                        <p id="show-message" class="mb-0">
                            {!! $property->description ?? '' !!}
                        </p>
                    </div>
                </div>

                <!-- Statut et Préférences -->
                <div class="info-section fade-in">
                    <h6><i class="fas fa-building text-danger"></i>Hébergements associés</h6>
                    <div class="partner-info-card">
                        <div class="row g-0">
                            <div class="col-12">
                                <div class="table-responsive p-3">
                                    <table class="table table-hover align-middle mb-0" id="example2">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="80">Code</th>
                                                <th>Libellé</th>
                                                <th>Type</th>
                                                <th width="140">Date</th>
                                                <th>Statut</th>
                                                <th>Qté disponible</th>
                                                <th>Etat</th>
                                                <th width="140">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="apartements-row">
                                            @forelse ($property->apartements as $apartement)
                                                <tr class="position-relative">
                                                    <td class="fw-semibold">#{{ $apartement->code ?? '' }}</td>
                                                    <td class="fw-semibold">{{ $apartement->title ?? '' }}</td>
                                                    <td class="fw-semibold">{{ $apartement->type->libelle ?? '' }}</td>
                                                    <td class="fw-semibold">{{ \Carbon\Carbon::parse($apartement->created_at)->translatedFormat('d F Y') ?? '' }}</td>
                                                    {{-- <td class="fw-semibold">{{ $apartement->created_at->format('d/M/Y') ?? '' }}</td> --}}
                                                    <td class="fw-semibold">
                                                        @if($apartement->nbr_available > 0)
                                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                                {{-- <i class="fas fa-handshake me-1"></i> --}}
                                                                Disponible
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger bg-opacity-10 text-danger">
                                                                {{-- <i class="fas fa-handshake me-1"></i> --}}
                                                                Indisponible
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="fw-semibold">{{ $apartement->nbr_available ?? '0' }}</td>
                                                    <td class="fw-semibold">
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
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <button class="btn btn-sm btn-icon btn-outline-primary rounded-circle" 
                                                               data-bs-toggle="modal" data-bs-target="#showApartmentModal{{ $apartement->code }}"
                                                                title="Voir détails">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <input type="hidden" name="appart_uuid" id="appart_uuid" value="{{ $apartement->uuid}}">
                                            @empty
                                                <tr class="default-row">
                                                    <td colspan="8" class="text-center py-5">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-home fa-3x text-muted mb-3 opacity-50"></i>
                                                            <h5 class="fw-semibold">Aucun hébergement trouvée</h5>
                                                            <p class="text-muted">Aucun hébergement n'est associé à la propriété</p>
                                                            
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
                </div>
            </div>
        </div>

        @foreach($property->apartements as $apartement)
            @include('properties.apparts.showModal' , ['apartement' => $apartement])
        @endforeach


        
    </div>

    

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Récupération des coordonnées depuis les variables Blade (Laravel)
            const latitude = @json($property->latitude ?? 0);
            const longitude = @json($property->longitude ?? 0);
            

            // Initialisation de la carte
            const map = L.map('map-location-property').setView([latitude, longitude], 16);

            // Chargement des tuiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);

            // Ajout d’un marqueur à l’emplacement
            L.marker([latitude, longitude]).addTo(map)
                .bindPopup("Emplacement de la propriété")
                .openPopup();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // supprimer un appartement
            document.querySelectorAll('.deleteAppart').forEach(function (button) {
                button.addEventListener('click', function () {
                    const btnContainer = this.closest('.btn-container'); // corrige ici le sélecteur
                    // const defaultItem = this.closest('.default-row'); // corrige ici le sélecteur
                    const appartUuid = btnContainer.querySelector('input[name="appart_uuid"]').value;

                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Cet hébergement sera définitivement supprimée.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Oui, supprimer !',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            Swal.fire({
                                icon: 'info',
                                title: 'Traitement en cours...',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            fetch(`/api/appart/destroy/${appartUuid}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Succès',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end',
                                        timerProgressBar: true,
                                    });

                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                    // previewItem.remove();
                                    // defaultItem.classList.add();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erreur',
                                        text: data.message,
                                        showConfirmButton: true,
                                    });
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: 'Une erreur s’est produite lors de la suppression de l’hébergement.',
                                    showConfirmButton: true,
                                });
                            });
                        }
                    });
                });
            });

        });
    </script>
@endsection
