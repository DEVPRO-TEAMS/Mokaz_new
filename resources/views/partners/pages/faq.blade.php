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

   

    <div class="row g-4">

        {{-- ================= MODULE RÉSERVATION ================= --}}
        @include('partners.pages.partials.video-module', [
            'title' => 'Module Réservation',
            'icon'  => 'fa-calendar-check',
            'color' => 'primary',
            'videos' => $reservationFileUrls
        ])

        {{-- ================= MODULE PROPERTIES ================= --}}
        @include('partners.pages.partials.video-module', [
            'title' => 'Module Gestion des Propriétés',
            'icon'  => 'fa-building',
            'color' => 'success',
            'videos' => $propertyFileUrls
        ])

        {{-- ================= MODULE HÉBERGEMENTS ================= --}}
        @include('partners.pages.partials.video-module', [
            'title' => 'Module Gestion des Hébergements',
            'icon'  => 'fa-home',
            'color' => 'info',
            'videos' => $appartFileUrls
        ])

        {{-- ================= MODULE PARAMÈTRES ================= --}}
        @include('partners.pages.partials.video-module', [
            'title' => 'Module Gestion des Paramètres',
            'icon'  => 'fa-cog',
            'color' => 'warning',
            'videos' => $paramettreFileUrls
        ])

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