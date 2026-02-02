@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded px-4 py-2 shadow-sm">
            <li class="breadcrumb-item"><a href="javascript:void(0);"><i class="fas fa-home me-1"></i> Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.partner.index') }}">Partenaires</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails du partenaire</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Détails du partenaire</h4>
    </div>

    <!-- Infos du partenaire -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <h5 class="mb-3"><i class="fas fa-building text-primary me-2"></i>{{ $partner->raison_social ?? 'N/A' }}</h5>
            <div class="row g-3">
                <div class="col-md-6"><strong>Email :</strong> {{ $partner->email ?? '-' }}</div>
                <div class="col-md-6"><strong>Téléphone :</strong> {{ $partner->phone ?? '-' }}</div>
                <div class="col-md-6"><strong>Site web :</strong> <a href="{{ $partner->website }}" target="_blank">{{ $partner->website ?? '-' }}</a></div>
                <div class="col-md-6"><strong>Adresse :</strong> {{ $partner->adresse ?? '-' }}</div>
                <div class="col-md-6"><strong>Statut :</strong>
                    <span class="badge {{ $partner->etat == 'actif' ? 'bg-success' : 'bg-secondary' }}">{{ strtoupper($partner->etat) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- NavTabs -->
    <ul class="nav nav-tabs mb-3" id="partnerTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Utilisateurs</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="properties-tab" data-bs-toggle="tab" data-bs-target="#properties" type="button" role="tab">Propriétés</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="apartments-tab" data-bs-toggle="tab" data-bs-target="#apartments" type="button" role="tab">Hébergements</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reservations-tab" data-bs-toggle="tab" data-bs-target="#reservations" type="button" role="tab">Historique des réservations</button>
        </li>
    </ul>

    <div class="tab-content" id="partnerTabsContent">
        <div class="tab-pane fade show active" id="users" role="tabpanel">
            <div class="card card-body border-0 shadow-sm">
                <h6>Utilisateurs liés</h6>
                @include('partners.components.userByPartner', ['users' => $partner->users])
            </div>

        </div>
        <div class="tab-pane fade" id="properties" role="tabpanel">
            <div class="card card-body border-0 shadow-sm">
                <h6>Propriétés enregistrées</h6>
                <p class="text-muted">Liste des propriétés du partenaire</p>
                @include('partners.components.propertiesByPartner', ['properties' => $partner->properties])
            </div>
        </div>
        <div class="tab-pane fade" id="apartments" role="tabpanel">
            <div class="card card-body border-0 shadow-sm">
                <h6>Hébergements liés</h6>
                <p class="text-muted">Liste des hébergements du partenaire</p>
                @include('partners.components.appartByPartner', ['units' => $appart])
            </div>
        </div>
        <div class="tab-pane fade" id="reservations" role="tabpanel">
            <div class="card card-body border-0 shadow-sm">
                <h6>Historique des réservations</h6>
                <p class="text-muted">Toutes les réservations associées au partenaire</p>
                <!-- TODO: ajouter tableau des réservations -->
                @include('partners.components.reservationByPartner', ['partner' => $partner->uuid])
            </div>
        </div>
    </div>
</div>
@endsection
