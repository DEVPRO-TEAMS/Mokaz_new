@extends('layouts.app')

@section('content')
    

    <div class="main-content-inn">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-handshake me-2 text-danger"></i> Demandes de Partenariat
                    </h3>
                    <div>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-filter me-1"></i> Filtres actifs: 
                            @if(request('search')) Recherche: "{{ request('search') }}" @endif
                            @if(request('date_debut')) Du {{ request('date_debut') }} @endif
                            @if(request('date_fin')) au {{ request('date_fin') }} @endif
                            @if(request('etat')) Statut: {{ ucfirst(request('etat')) }} @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
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
                                <h6 class="text-muted mb-2">Total Demandes</h6>
                                <h3 class="mb-0">{{ count($demandePartenariats) }}</h3>
                            </div>
                            <div class="counter-icon text-primary">
                                <i class="fas fa-list"></i>
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
                                <h3 class="mb-0">{{ count($demandePartenariats->where('etat', 'pending')) }}</h3>
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
                                <h6 class="text-muted mb-2">Acceptées</h6>
                                <h3 class="mb-0">{{ count($demandePartenariats->where('etat', 'actif')) }}</h3>
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
                                <h6 class="text-muted mb-2">Rejetées</h6>
                                <h3 class="mb-0">{{ count($demandePartenariats->where('etat', 'inactif')) }}</h3>
                            </div>
                            <div class="counter-icon text-danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="search-box">
                    <form action="{{ route('admin.demande.view') }}" method="get">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Recherche..." name="search" value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <input type="date" class="form-control" name="date_debut" value="{{ request('date_debut') }}" placeholder="Date début">
                            </div>
                            
                            <div class="col-md-2">
                                <input type="date" class="form-control" name="date_fin" value="{{ request('date_fin') }}" placeholder="Date fin">
                            </div>
                            
                            <div class="col-md-3">
                                <select name="etat" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="pending" {{ request('etat') == 'pending' ? 'selected' : '' }}>En Attente</option>
                                    <option value="actif" {{ request('etat') == 'actif' ? 'selected' : '' }}>Accepté</option>
                                    <option value="inactif" {{ request('etat') == 'inactif' ? 'selected' : '' }}>Rejeté</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="wrapper-content row">
            <div class="col-xl-12">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive-lg p-3">
                            <table class="table table-hover align-middle mb-0" id="example2">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>Entreprise</th>
                                        <th>Contact</th>
                                        <th width="140">Date</th>
                                        <th width="120">Statut</th>
                                        <th width="140">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($demandePartenariats as $demande)
                                    <tr class="position-relative">
                                        <td class="fw-semibold">#{{ $demande->id ?? '' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-initials bg-primary bg-opacity-10 text-primary me-3">
                                                    {{ substr($demande->company ?? '', 0, 2) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $demande->company ?? '' }}</h6>
                                                    <small class="text-muted d-block">{{ $demande->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-phone text-muted me-2"></i>
                                                <span>{{ $demande->phone ?? 'Non renseigné' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $demande->created_at->format('d/m/Y') ?? '' }}</span>
                                                <small class="text-muted">{{ $demande->created_at->diffForHumans() ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($demande->etat == 'actif')
                                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                                    <i class="fas fa-check-circle me-1"></i> Accepté
                                                </span>
                                            @elseif ($demande->etat == 'inactif')
                                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Rejeté
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning">
                                                    <i class="fas fa-clock me-1"></i> En attente
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-icon btn-outline-primary rounded-circle" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#showDemandeModal{{ $demande->id }}"
                                                        title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                {{-- @if($demande->etat == 'pending')
                                                    <button class="btn btn-sm btn-icon btn-outline-success rounded-circle"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveDemandModal{{ $demande->id }}"
                                                            title="Approuver">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    
                                                    <button class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                                            onclick="rejectDemande({{ $demande->id }})"
                                                            title="Rejeter">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif --}}
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    @include('admins.pages.demandesPartenariat.showDemande', ['demandePartenariat' => $demande])
                                    
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-50"></i>
                                                <h5 class="fw-semibold">Aucune demande trouvée</h5>
                                                <p class="text-muted">Aucune demande ne correspond à vos critères de recherche</p>
                                                <a href="{{ route('admin.demande.view') }}" class="btn btn-sm btn-outline-primary mt-2">
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

@endsection
