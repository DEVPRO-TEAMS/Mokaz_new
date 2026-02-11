@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <!-- ✅ BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded px-4 py-2 shadow-sm">
            <li class="breadcrumb-item"><a href="javascript:void(0);"><i class="fas fa-home me-1"></i> Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">FAQ</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-primary mb-2">Centre d'Aide</h1>
        <p class="text-muted fs-5">Vidéos tutoriels pour vous accompagner dans l'utilisation de la plateforme</p>
    </div>

    <!-- Modules Grid -->
    <div class="row g-4">

    {{-- ================= MODULE RÉSERVATION ================= --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm hover-shadow">
            <div class="card-header bg-gradient-primary text-white py-3">
                <h3 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-calendar-check me-3 fs-4"></i>
                    Module Réservation
                </h3>
            </div>

            <div class="card-body p-4">
                @if(!empty($reservationFileUrls))
                    <div class="row g-4">
                        @foreach($reservationFileUrls as $video)
                            <div class="col-md-6 col-lg-4">
                                <div class="video-card">
                                    <div class="video-wrapper rounded overflow-hidden shadow-sm mb-3">
                                        <video class="w-100" controls preload="metadata">
                                            <source src="{{ $video['url'] }}" type="video/mp4">
                                            Votre navigateur ne supporte pas la lecture de vidéos.
                                        </video>
                                    </div>
                                    <h5 class="video-title text-dark mb-2">
                                        <i class="fas fa-play-circle text-primary me-2"></i>
                                        {{ pathinfo($video['name'], PATHINFO_FILENAME) }}
                                    </h5>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-video-slash text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted mb-0">Aucune vidéo disponible pour le module Réservation.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>


    {{-- ================= MODULE PROPERTIES ================= --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm hover-shadow">
            <div class="card-header bg-gradient-success text-white py-3">
                <h3 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-building me-3 fs-4"></i>
                    Module Gestion des Propriétés
                </h3>
            </div>

            <div class="card-body p-4">
                @if(!empty($propertyFileUrls))
                    <div class="row g-4">
                        @foreach($propertyFileUrls as $video)
                            <div class="col-md-6 col-lg-4">
                                <div class="video-card">
                                    <div class="video-wrapper rounded overflow-hidden shadow-sm mb-3">
                                        <video class="w-100" controls preload="metadata">
                                            <source src="{{ $video['url'] }}" type="video/mp4">
                                            Votre navigateur ne supporte pas la lecture de vidéos.
                                        </video>
                                    </div>
                                    <h5 class="video-title text-dark mb-2">
                                        <i class="fas fa-play-circle text-success me-2"></i>
                                        {{ pathinfo($video['name'], PATHINFO_FILENAME) }}
                                    </h5>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-video-slash text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted mb-0">Aucune vidéo disponible pour le module Gestion des propriétés.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>


    {{-- ================= MODULE HÉBERGEMENTS ================= --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm hover-shadow">
            <div class="card-header bg-gradient-info text-white py-3">
                <h3 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-home me-3 fs-4"></i>
                    Module Gestion des Hébergements
                </h3>
            </div>

            <div class="card-body p-4">
                @if(!empty($appartFileUrls))
                    <div class="row g-4">
                        @foreach($appartFileUrls as $video)
                            <div class="col-md-6 col-lg-4">
                                <div class="video-card">
                                    <div class="video-wrapper rounded overflow-hidden shadow-sm mb-3">
                                        <video class="w-100" controls preload="metadata">
                                            <source src="{{ $video['url'] }}" type="video/mp4">
                                            Votre navigateur ne supporte pas la lecture de vidéos.
                                        </video>
                                    </div>
                                    <h5 class="video-title text-dark mb-2">
                                        <i class="fas fa-play-circle text-info me-2"></i>
                                        {{ pathinfo($video['name'], PATHINFO_FILENAME) }}
                                    </h5>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-video-slash text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted mb-0">Aucune vidéo disponible pour le module Gestion des hébergements.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>


    {{-- ================= MODULE PARAMÈTRES ================= --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm hover-shadow">
            <div class="card-header bg-gradient-warning text-white py-3">
                <h3 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-cog me-3 fs-4"></i>
                    Module Gestion des Paramètres
                </h3>
            </div>

            <div class="card-body p-4">
                @if(!empty($paramettreFileUrls))
                    <div class="row g-4">
                        @foreach($paramettreFileUrls as $video)
                            <div class="col-md-6 col-lg-4">
                                <div class="video-card">
                                    <div class="video-wrapper rounded overflow-hidden shadow-sm mb-3">
                                        <video class="w-100" controls preload="metadata">
                                            <source src="{{ $video['url'] }}" type="video/mp4">
                                            Votre navigateur ne supporte pas la lecture de vidéos.
                                        </video>
                                    </div>
                                    <h5 class="video-title text-dark mb-2">
                                        <i class="fas fa-play-circle text-warning me-2"></i>
                                        {{ pathinfo($video['name'], PATHINFO_FILENAME) }}
                                    </h5>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-video-slash text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted mb-0">Aucune vidéo disponible pour le module Gestion des paramètres.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>


    <!-- Help Footer -->
    <div class="text-center mt-5 p-4 bg-light rounded">
        <h5 class="mb-2">Besoin d'aide supplémentaire ?</h5>
        <p class="text-muted mb-3">Notre équipe est là pour vous accompagner</p>
        <a href="mailto:support@jsbeyci.com" class="btn btn-primary px-4">
            <i class="fas fa-envelope me-2"></i>Contactez-nous
        </a>
    </div>
</div>

<style>
    /* Gradient backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    /* Card hover effect */
    .hover-shadow {
        transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
    }

    /* Video card styling */
    .video-card {
        transition: transform 0.3s ease;
    }
    
    .video-card:hover {
        transform: scale(1.02);
    }

    .video-wrapper {
        position: relative;
        background: #000;
        aspect-ratio: 16/9;
    }
    
    .video-wrapper video {
        height: 100%;
        object-fit: cover;
    }

    .video-title {
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.4;
    }

    /* Custom scrollbar for videos */
    video::-webkit-media-controls-panel {
        background: linear-gradient(transparent, rgba(0,0,0,0.5));
    }

    /* Breadcrumb styling */
    .breadcrumb {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
    }
    
    .breadcrumb a {
        color: #667eea;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .breadcrumb a:hover {
        color: #764ba2;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }
        
        .card-header h3 {
            font-size: 1.25rem;
        }
    }
</style>
@endsection