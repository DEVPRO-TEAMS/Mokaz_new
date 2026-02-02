@extends('layouts.app')

@section('content')
    <style>
        .desktop-version {
            display: none;
        }

        /* Afficher la version desktop à partir de 576px (sm et +) */
        @media (min-width: 576px) {
            .desktop-version {
                display: block;
            }

            .mobile-version {
                display: none;
                /* cacher la version mobile dès sm */
            }
        }
    </style>
    <div class="main-content-inne pt-5 mt-5 wrap-dashboard-content">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-home me-2 text-danger"></i> Gestion des Propriétés
                    </h3>
                </div>

            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-end align-items-center">
                    <div>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-filter me-1"></i> Filtres actifs:
                            @if (request('search'))
                                Recherche: "{{ request('search') }}"
                            @endif
                            @if (request('date_debut'))
                                Du {{ request('date_debut') }}
                            @endif
                            @if (request('date_fin'))
                                au {{ request('date_fin') }}
                            @endif
                            @if (request('etat'))
                                Statut: {{ ucfirst(request('etat')) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif
        <!-- Cards Counters -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Propriétés</h6>
                                <h3 class="mb-0">{{ count($properties) }}</h3>
                            </div>
                            <div class="counter-icon text-primary">
                                <i class="fas fa-home"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">En Attente</h6>
                                <h3 class="mb-0">{{ count($properties->where('etat', 'pending')) }}</h3>
                            </div>
                            <div class="counter-icon text-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Actives</h6>
                                <h3 class="mb-0">{{ count($properties->where('etat', 'actif')) }}</h3>
                            </div>
                            <div class="counter-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-counter bg-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Inactives</h6>
                                <h3 class="mb-0">{{ count($properties->where('etat', 'inactif')) }}</h3>
                            </div>
                            <div class="counter-icon text-danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="search-box">
                    <form action="{{ route('partner.properties.index') }}" method="get">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Recherche..." name="search"
                                        value="{{ request('search') }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <input type="date" class="form-control" name="date_debut"
                                    value="{{ request('date_debut') }}" placeholder="Date début">
                            </div>

                            <div class="col-md-2">
                                <input type="date" class="form-control" name="date_fin"
                                    value="{{ request('date_fin') }}" placeholder="Date fin">
                            </div>

                            <div class="col-md-3">
                                <select name="etat" class="nice-select form-select list style-1">
                                    <option value="">Tous les statuts</option>
                                    <option value="pending" {{ request('etat') == 'pending' ? 'selected' : '' }}>En Attente
                                    </option>
                                    <option value="actif" {{ request('etat') == 'actif' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactif" {{ request('etat') == 'inactif' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-filter me-1"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="widget-box-2 wd-listing">
            <div class="row align-items-center justify-content-center">
                <div class="flat-bt-top col-md-9">
                    <h6 class="title">Mes Propriétés</h6>
                </div>
                <div class="flat-bt-top col-md-3 text-end">
                    <a class="tf-btn primary" href="{{ route('partner.properties.create') }}"><i
                            class="icon icon-plus"></i> Ajouter une propriété</a>
                </div>
            </div>

            <div class="mobile-version">
                <div class="row g-3 py-4">
                    @forelse ($propertiesForSmallDevice as $property)
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card" style="width: 100%;">
                                @if ($property->image)
                                    <img src="{{ asset($property->image) }}" alt="{{ $property->title }}"
                                        class="card-img-top">
                                @else
                                    <div class="avatar-initials bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center rounded-2"
                                        style="width: 150px; height: 150px;">
                                        <i class="fas fa-home"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{!! Str::words($property->title ?? '', 3, '...') ?? '' !!}</h5>
                                    <p class="card-text">{!! Str::words($property->description ?? '', 4, '...') !!}</p>

                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $property->ville->label ?? '' }},
                                            {{ $property->pays->label ?? '' }}</span>
                                        <small class="text-muted">{{ $property->address ?? '' }}</small>

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row mb-4">
                                        <input type="hidden" name="property_uuid" value="{{ $property->uuid ?? '' }}">
                                        <div class="d-flex gap-2 col-8">
                                            <a title="Voir détails"
                                                class="btn btn-sm btn-icon btn-outline-primary rounded-circle"
                                                href="{{ route('partner.properties.show', $property->uuid) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a title="Modifier"
                                                href="{{ route('partner.properties.edit', $property->uuid) }}"
                                                class="btn btn-sm btn-icon btn-outline-secondary rounded-circle"
                                                title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button
                                                class="btn btn-sm btn-icon btn-outline-danger rounded-circle deleteProperty"
                                                title="Supprimer">
                                                <i class="icon icon-trash"></i>
                                            </button>
                                        </div>

                                        <div class="col-4">
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
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <a href="{{ route('partner.apartments.create', $property->uuid) }}"
                                                class="btn btn-sm tf-btn primary w-100">
                                                <i class="icon icon-plus"></i> Ajouter un hébergement
                                            </a>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    @empty
                        <div colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-home fa-3x text-muted mb-3 opacity-50"></i>
                                <h5 class="fw-semibold">Aucune propriété trouvée</h5>
                                <p class="text-muted">Aucune propriété ne correspond à vos critères
                                    de recherche</p>
                                <a href="{{ route('partner.properties.index') }}"
                                    class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-sync-alt me-1"></i> Réinitialiser les filtres
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="nav-pagination pt-4">
                    {{ $propertiesForSmallDevice->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <div class="wrapper-content row desktop-version">
                <div class="col-xl-12">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-body p-0">
                            <div class="table-responsive-lg p-3">
                                <table class="table table-hover table-striped table-bordered align-middle" id="example2">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Code</th>
                                            <th>Propriété</th>
                                            <th>Localisation</th>
                                            <th>Nombre d'appart</th>
                                            <th width="140">Date</th>
                                            <th width="120">Statut</th>
                                            <th width="140">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($properties as $property)
                                            <tr class="position-relative text-wrap property-row">
                                                <td class="fw-semibold">#{{ $property->property_code ?? '' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="property-image me-3">

                                                            @if ($property->image)
                                                                <img src="{{ asset($property->image) }}"
                                                                    alt="{{ $property->title }}" class="rounded-2"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="avatar-initials bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center rounded-2"
                                                                    style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-home"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-semibold">{!! Str::words($property->title ?? '', 3, '...') ?? '' !!}</h6>
                                                            <small
                                                                class="text-muted d-block">{!! Str::words($property->description ?? '', 4, '...') !!}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $property->ville->label ?? '' }},
                                                            {{ $property->pays->label ?? '' }}</span>
                                                        <small class="text-muted">{{ $property->address ?? '' }}</small>

                                                    </div>
                                                </td>
                                                <td>
                                                    <span>{{ count($property->apartements->where('etat', '!=', 'inactif')) ?? '0' }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span>{{ $property->created_at->format('d/m/Y') ?? '' }}</span>
                                                        <small
                                                            class="text-muted">{{ $property->created_at->diffForHumans() ?? '' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($property->etat == 'actif')
                                                        <span
                                                            class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                                            <i class="fas fa-check-circle me-1"></i> Active
                                                        </span>
                                                    @elseif ($property->etat == 'inactif')
                                                        <span
                                                            class="badge rounded-pill bg-danger bg-opacity-10 text-danger">
                                                            <i class="fas fa-times-circle me-1"></i> Inactive
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge rounded-pill bg-warning bg-opacity-10 text-warning">
                                                            <i class="fas fa-clock me-1"></i> En attente
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="">
                                                    <input type="hidden" name="property_uuid"
                                                        value="{{ $property->uuid ?? '' }}">
                                                    <div class="d-flex gap-2">
                                                        <a title="Voir détails"
                                                            class="btn btn-sm btn-icon btn-outline-primary rounded-circle"
                                                            href="{{ route('partner.properties.show', $property->uuid) }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <a title="Modifier"
                                                            href="{{ route('partner.properties.edit', $property->uuid) }}"
                                                            class="btn btn-sm btn-icon btn-outline-secondary rounded-circle"
                                                            title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button
                                                            class="btn btn-sm btn-icon btn-outline-danger rounded-circle deleteProperty"
                                                            title="Supprimer">
                                                            <i class="icon icon-trash"></i>
                                                        </button>


                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="fas fa-home fa-3x text-muted mb-3 opacity-50"></i>
                                                        <h5 class="fw-semibold">Aucune propriété trouvée</h5>
                                                        <p class="text-muted">Aucune propriété ne correspond à vos critères
                                                            de recherche</p>
                                                        <a href="{{ route('partner.properties.index') }}"
                                                            class="btn btn-sm btn-outline-primary mt-2">
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
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // supprimer un appartement
            document.querySelectorAll('.deleteProperty').forEach(function(button) {
                button.addEventListener('click', function() {
                    const propertyRow = this.closest('.property-row'); // corrige ici le sélecteur
                    const propertyUuid = propertyRow.querySelector('input[name="property_uuid"]')
                        .value;

                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Cette propriété sera définitivement supprimée.",
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

                            fetch(`/api/property/destroy/${propertyUuid}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]')?.content,
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

                                        // setTimeout(() => {
                                        //     location.reload();
                                        // }, 1000);

                                        propertyRow.remove();
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
                                        text: 'Une erreur s’est produite lors de la suppression de la propriété.',
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
