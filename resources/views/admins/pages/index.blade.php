@extends('layouts.app')
@section('content')
    <div class="container-fluid py-4">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-handshake me-2 text-danger"></i>
                    Tableau de Bord - Demandes de Partenariat
                </h1>
                <p class="text-muted">Gestion et suivi des demandes de partenariat</p>
            </div>
        </div>

        <!-- Statistiques principales -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="dashboard-card stat-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Demandes</div>
                                <div class="h5 mb-0 font-weight-bold" id="totalRequests">156</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="dashboard-card stat-card pending">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">En Attente</div>
                                <div class="h5 mb-0 font-weight-bold" id="pendingRequests">42</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="dashboard-card stat-card actif">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Approuvées</div>
                                <div class="h5 mb-0 font-weight-bold" id="activeRequests">89</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="dashboard-card stat-card inactif">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Rejetées</div>
                                <div class="h5 mb-0 font-weight-bold" id="inactiveRequests">25</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="row mb-4" style="min-height: 40vh;">
            <div class="col-xl-8 col-lg-7 h-100">
                <div class="card dashboard-card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Évolution des Demandes</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="min-height: 100%; ">
                            <canvas id="requestsChart" style="min-height: 300px; "></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="card dashboard-card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Répartition par État</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Récupération des données passées depuis le contrôleur
        const partnerships = {!! json_encode($partnerships) !!};
        const monthlyData = {!! json_encode($monthlyData) !!};

        // Mise à jour des statistiques principales
        document.getElementById('totalRequests').textContent = {!! $totalRequests !!};
        document.getElementById('pendingRequests').textContent = {!! $pendingRequests !!};
        document.getElementById('activeRequests').textContent = {!! $activeRequests !!};
        document.getElementById('inactiveRequests').textContent = {!! $inactiveRequests !!};

        // Initialisation des graphiques
        function initCharts() {
            // Graphique d'évolution des demandes
            const requestsCtx = document.getElementById('requestsChart').getContext('2d');
            new Chart(requestsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Demandes',
                        data: monthlyData,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.parsed.y} demandes`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Graphique de répartition par état
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['En attente', 'Approuvé', 'Rejeté'],
                    datasets: [{
                        data: [
                            {!! $pendingRequests !!},
                            {!! $activeRequests !!},
                            {!! $inactiveRequests !!}
                        ],
                        backgroundColor: [
                            '#ffc107',
                            '#28a745',
                            '#dc3545'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Le reste de votre code JavaScript peut rester inchangé
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
        });
    </script>
@endsection
