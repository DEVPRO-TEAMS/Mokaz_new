@extends('layouts.app')

@section('content')
    <div class="main-content-inne pt-5 mt-5 wrap-dashboard-content">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="bi bi-chat-left me-2 text-danger"></i> Commentaires et avis
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
                                <h6 class="text-muted mb-2">Total Commentaires</h6>
                                <h3 class="mb-0">{{ count($comments) }}</h3>
                            </div>
                            <div class="counter-icon text-primary">
                                <i class="bi bi-chat-left"></i>
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
                                <h3 class="mb-0">{{ count($comments->where('etat', 'pending')) }}</h3>
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
                                <h6 class="text-muted mb-2">Actifs</h6>
                                <h3 class="mb-0">{{ count($comments->where('etat', 'actif')) }}</h3>
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
                                <h6 class="text-muted mb-2">Inactifs</h6>
                                <h3 class="mb-0">{{ count($comments->where('etat', 'inactif')) }}</h3>
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
                    <form action="{{ route('partner.comment.index') }}" method="get">
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
                                    <option value="actif" {{ request('etat') == 'actif' ? 'selected' : '' }}>Actif
                                    </option>
                                    <option value="inactif" {{ request('etat') == 'inactif' ? 'selected' : '' }}>Inactif
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
                    <h6 class="title">Liste des commentaires et avis</h6>
                </div>
                <div class="flat-bt-top col-md-3 text-end">
                    {{-- <a class="tf-btn primary" href="{{ route('partner.properties.create') }}"><i
                            class="icon icon-plus"></i> Ajouter une propriété</a> --}}
                </div>
            </div>

            <div class="wrapper-content row">
                <div class="col-xl-12">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-body p-0">
                            <div class="table-responsive-lg p-3">
                                <table class="table table-hover table-striped table-bordered align-middle" id="example2">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Propriété</th>
                                            <th>Hébergement</th>
                                            <th>Nom du client</th>
                                            <th>Email du client</th>
                                            <th width="140">Date</th>
                                            <th width="120">Statut</th>
                                            <th width="140">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($comments->where('etat','!=', "inactif") as $item)
                                            <tr class="position-relative text-wrap property-row">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="property-image me-3">

                                                            @if ($item->property->image)
                                                                <img src="{{ asset($item->property->image) }}"
                                                                    alt="{!! Str::words($item->property->title ?? '', 3, '...') ?? '' !!}" class="rounded-2"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="avatar-initials bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center rounded-2"
                                                                    style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-home"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-semibold">{!! Str::words($item->property->title ?? '', 3, '...') ?? '' !!}</h6>
                                                            {{-- <small
                                                                class="text-muted d-block">{!! Str::words($property->description ?? '', 4, '...') !!}</small> --}}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="property-image me-3">

                                                            @if ($item->appart->image)
                                                                <img src="{{ asset($item->appart->image) }}"
                                                                    alt="{!! Str::words($item->appart->title ?? '', 3, '...') !!}" class="rounded-2"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="avatar-initials bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center rounded-2"
                                                                    style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-home"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-semibold">{!! Str::words($item->appart->title ?? '', 3, '...') !!}</h6>
                                                            <small
                                                                class="text-muted d-block">{!! Str::words($item->appart->description ?? '', 4, '...') !!}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $item->name ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $item->email ?? '' }}
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span>{{ $item->created_at->format('d/m/Y') ?? '' }}</span>
                                                        <small
                                                            class="text-muted">{{ $item->created_at->diffForHumans() ?? '' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($item->etat == 'actif')
                                                        <span
                                                            class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                                            <i class="fas fa-check-circle me-1"></i> Active
                                                        </span>
                                                    @elseif ($item->etat == 'inactif')
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
                                                    <input type="hidden" name="uuid"
                                                        value="{{ $item->uuid ?? '' }}">
                                                    <div class="d-flex gap-2">
                                                        <a title="Voir détails"
                                                            class="btn btn-sm btn-icon btn-outline-primary rounded-circle"
                                                            href="javascript:void(0);" data-bs-toggle="modal"
                                                            data-bs-target="#showCommentModal{{ $item->uuid }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if (Auth::user()->user_type == 'partner')
                                                            <button
                                                                class="btn btn-sm btn-icon btn-outline-danger rounded-circle deleteComment"
                                                                title="Supprimer">
                                                                <i class="icon icon-trash"></i>
                                                            </button>
                                                        @endif


                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="fas fa-home fa-3x text-muted mb-3 opacity-50"></i>
                                                        <h5 class="fw-semibold">Aucun commentaire trouvée</h5>
                                                        <p class="text-muted">Aucun commentaire ne correspond à vos
                                                            critères
                                                            de recherche</p>
                                                        <a href="{{ route('partner.comment.index') }}"
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

    @foreach ($comments->where('etat', '!=', 'inactif') as $item)
        @include('comments.showModal', ['comment' => $item])
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // supprimer un appartement
            document.querySelectorAll('.deleteComment').forEach(function(button) {
                button.addEventListener('click', function() {
                    const propertyRow = this.closest('.property-row'); // corrige ici le sélecteur
                    const commentUuid = propertyRow.querySelector('input[name="uuid"]').value;

                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Ce commentaire sera définitivement supprimée.",
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

                            fetch(`/api/comment/destroy/${commentUuid}`, {
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
                                        text: 'Une erreur s’est produite lors de la suppression du commentaire.',
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
