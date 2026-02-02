@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- ✅ BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded px-4 py-2 shadow-sm">
            <li class="breadcrumb-item"><a href="javascript:void(0);"><i class="fas fa-home me-1"></i> Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mes collaborateurs</li>
        </ol>
    </nav>

    <!-- ✅ HEADER + BUTTON AJOUT -->
    <div class="row align-items-center my-3">
        <div class="col-md-6 text-start">
            <h4 class="fw-bold mb-1"><i class="fas fa-handshake text-primary me-2"></i>Liste des collaborateurs</h4>
            <p class="text-muted small mb-0">{{ count($collaborators) }} Collaborateurs enregistrés</p>
        </div>
        <div class="col-md-6 text-end my-3">
            <button class="btn btn-success " data-bs-toggle="modal" data-bs-target="#addCollaboratorModal">
                <i class="fas fa-plus-circle me-1"></i> Ajouter un collaborateur
            </button>
        </div>
    </div>

    <!-- ✅ TABLE CARD -->
    <div class="card shadow-sm border-0 widget-box-2 wd-listing">
        <div class="card-body wrap-table border-0 p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="example2">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th>code</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($collaborators as $item)
                            <tr>
                                <td>{{ $item->code ?? '' }}</td>
                                <td>{{ $item->lastname ?? '' }} {{ $item->name ?? '' }}</td>
                                <td>{{ $item->email ?? '-' }}</td>
                                <td>{{ $item->phone ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $item->etat == 'actif' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ strtoupper($item->etat ?? 'inconnu') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- <button class="btn btn-sm btn-outline-info">
                                            <a href="{{ route('admin.showPartner', $partner->uuid) }}" title="Voir">
                                                <i class="fas fa-eye text-secondary"></i>
                                            </a>
                                        </button> --}}
                                        <button class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal" data-bs-target="#editModal{{ $item->uuid }}"
                                            title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <a class="deleteConfirmation" data-uuid="{{ $item->uuid }}"
                                                data-token="{{ csrf_token() }}"
                                                data-type="confirmation_redirect"
                                                data-url="{{ route('partner.destroyCollaborator', $item->uuid) }}"
                                                data-title="Vous êtes sur le point de supprimer {{ $item->name ?? ''}} {{ $item->lastname ?? ''}}"
                                                data-id="{{ $item->uuid }}"
                                                data-route="{{ route('partner.destroyCollaborator', $item->uuid) }}">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal modification --}}
                            @include('partners.collaborators.editModal', ['collaborator' => $item])
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Aucun partenaire trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal ajout partenaire --}}
@include('partners.collaborators.addModal')
@endsection
