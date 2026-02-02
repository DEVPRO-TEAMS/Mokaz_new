@extends('layouts.app')
@section('content')
    <div class="container-fluid py-4">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-tachometer-alt me-2 text-danger"></i>
                    Tableau de Bord - {{ Auth::user()->partner->raison_social }}
                </h1>
                <p class="text-muted">Gestion des employés, propriétés, hébergements et reservations</p>
            </div>
        </div>

        <!-- Statistiques principales -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="dashboard-card stat-card">
                    <div class="card-body">
                        <a href="{{ route('partner.collaborator.index') }}">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Collaborateurs</div>
                                    <div class="h5 mb-0 font-weight-bold" id="totalRequests"> {{ count($partnerUsers) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-info"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="dashboard-card stat-card pending">

                    <div class="card-body">
                        <a href="{{ route('partner.properties.index') }}">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Propriétés</div>
                                    <div class="h5 mb-0 font-weight-bold" id="pendingRequests">
                                        {{ count($partnerProperties) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-home fa-2x text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="dashboard-card stat-card actif">
                    <div class="card-body">
                        <a href="{{ route('partner.properties.index') }}">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total hébergements</div>
                                    <div class="h5 mb-0 font-weight-bold" id="activeRequests">
                                        {{ $partnerPropertyApartments->count() }} </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-building fa-2x text-success"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="dashboard-card stat-card inactif">
                    <div class="card-body">
                        <a href="{{ route('partner.reservation.index') }}">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Reservations</div>
                                    <div class="h5 mb-0 font-weight-bold" id="inactiveRequests">
                                        {{ $reservations->where('status', 'confirmed')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-danger"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="row mb-4" style="min-height: 40vh;">
            <div class="col-xl-8 col-lg-7 h-100">
                <nav>
                    <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                        <button class="nav-link active  text-secondary" id="nav-month-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-month" type="button" role="tab" aria-controls="nav-month"
                            aria-selected="true">Mois</button>
                        <button class="nav-link  text-secondary" id="nav-week-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-week" type="button" role="tab" aria-controls="nav-week"
                            aria-selected="false">Semaine</button>
                        <button class="nav-link  text-secondary" id="nav-day-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-day" type="button" role="tab" aria-controls="nav-day"
                            aria-selected="false">Jour</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-month" role="tabpanel" aria-labelledby="nav-month-tab">
                        <div class="card dashboard-card">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Évolution des reservations par mois</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="min-height: 100%; ">
                                    <canvas id="reservationsChartMonth" style="min-height: 300px; "></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-week" role="tabpanel" aria-labelledby="nav-week-tab">
                        <div class="card dashboard-card">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Évolution des reservations par semaine</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="reservationsChartWeek"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-day" role="tabpanel" aria-labelledby="nav-day-tab">
                        <div class="card dashboard-card">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Évolution des reservations par jour</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="reservationsChartDay"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-4 pt-5 col-lg-5">
                <div class="card dashboard-card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Répartition par statut</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="statusReservationsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Récupération des réservations depuis le contrôleur
            const reservations = {!! json_encode($reservations) !!};

            // Fonction pour formater la date en "05 Aoû"
            function formatDay(date) {
                const day = date.getDate().toString().padStart(2, '0');
                const month = date.toLocaleString('fr-FR', {
                    month: 'short'
                });
                return `${day} ${month}`;
            }

            // Fonction pour formater une période de semaine "05 Aoû - 12 Aoû"
            function formatWeek(startDate, endDate) {
                const startDay = startDate.getDate().toString().padStart(2, '0');
                const startMonth = startDate.toLocaleString('fr-FR', {
                    month: 'short'
                });
                const endDay = endDate.getDate().toString().padStart(2, '0');
                const endMonth = endDate.toLocaleString('fr-FR', {
                    month: 'short'
                });
                return `${startDay} ${startMonth} - ${endDay} ${endMonth}`;
            }

            // Fonction pour formater le mois "Aoû"
            function formatMonth(date) {
                return date.toLocaleString('fr-FR', {
                    month: 'short'
                });
            }

            // Fonction pour obtenir le début et la fin d'une semaine à partir d'une date
            function getWeekRange(date) {
                const day = date.getDay();
                const diff = date.getDate() - day + (day === 0 ? -6 : 1); // ajuste pour lundi comme premier jour
                const start = new Date(date.setDate(diff));
                const end = new Date(date.setDate(diff + 6));
                return {
                    start,
                    end
                };
            }

            // Fonction pour grouper les réservations par jour (4 derniers jours)
            function getLast4DaysData(reservations) {
                const result = {};
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                for (let i = 3; i >= 0; i--) {
                    const date = new Date(today);
                    date.setDate(date.getDate() - i);
                    const key = formatDay(date);
                    result[key] = 0;
                }

                reservations.forEach(reservation => {
                    const resDate = new Date(reservation.created_at);
                    resDate.setHours(0, 0, 0, 0);

                    for (let i = 0; i < 4; i++) {
                        const date = new Date(today);
                        date.setDate(date.getDate() - i);

                        if (resDate.getTime() === date.getTime()) {
                            const key = formatDay(date);
                            result[key]++;
                            break;
                        }
                    }
                });

                return result;
            }

            // Fonction pour grouper les réservations par semaine (4 dernières semaines)
            function getLast4WeeksData(reservations) {
                const result = {};
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                for (let i = 3; i >= 0; i--) {
                    const date = new Date(today);
                    date.setDate(date.getDate() - (i * 7));
                    const {
                        start,
                        end
                    } = getWeekRange(date);
                    const key = formatWeek(start, end);
                    result[key] = 0;
                }

                reservations.forEach(reservation => {
                    const resDate = new Date(reservation.created_at);

                    for (let i = 0; i < 4; i++) {
                        const date = new Date(today);
                        date.setDate(date.getDate() - (i * 7));
                        const {
                            start,
                            end
                        } = getWeekRange(date);

                        if (resDate >= start && resDate <= end) {
                            const key = formatWeek(start, end);
                            result[key]++;
                            break;
                        }
                    }
                });

                return result;
            }

            // Fonction pour grouper les réservations par mois (tous les mois de l'année en cours)
            function getCurrentYearMonthsData(reservations) {
                const result = {};
                const currentYear = new Date().getFullYear();

                // Initialiser tous les mois de l'année
                for (let month = 0; month < 12; month++) {
                    const date = new Date(currentYear, month, 1);
                    const key = formatMonth(date);
                    result[key] = 0;
                }

                // Compter les réservations
                reservations.forEach(reservation => {
                    const resDate = new Date(reservation.created_at);
                    if (resDate.getFullYear() === currentYear) {
                        const key = formatMonth(resDate);
                        result[key]++;
                    }
                });

                return result;
            }

            // Préparation des données
            const dailyData = getLast4DaysData(reservations);
            const weeklyData = getLast4WeeksData(reservations);
            const monthlyData = getCurrentYearMonthsData(reservations);

            // Création des graphiques avec superposition ligne/barres
            createCombinedChart('reservationsChartDay', Object.keys(dailyData), Object.values(dailyData), 'Jour');
            createCombinedChart('reservationsChartWeek', Object.keys(weeklyData), Object.values(weeklyData),
                'Semaine');
            createCombinedChart('reservationsChartMonth', Object.keys(monthlyData), Object.values(monthlyData),
                'Mois');

            function createCombinedChart(canvasId, labels, data, period) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: `Réservations (barres)`,
                                data: data,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                order: 1 // Met les barres en arrière-plan
                            },
                            {
                                label: `Évolution (ligne)`,
                                data: data,
                                type: 'line',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                borderWidth: 3,
                                pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                fill: false,
                                tension: 0.3,
                                order: 0 // Met la ligne au premier plan
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.parsed.y} réservations`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    stepSize: 1
                                },
                                grid: {
                                    drawOnChartArea: true
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            const statusCounts = {
                pending: 0,
                confirmed: 0,
                completed: 0,
                cancelled: 0,
                reconducted: 0
            };

            reservations.forEach(reservation => {
                switch (reservation.status) {
                    case 'pending':
                        statusCounts.pending++;
                        break;
                    case 'confirmed':
                        statusCounts.confirmed++;
                        break;
                    case 'completed':
                        statusCounts.completed++;
                        break;
                    case 'cancelled':
                        statusCounts.cancelled++;
                        break;
                    case 'reconducted':
                        statusCounts.reconducted++;
                        break;
                }
            });

            // Graphique de répartition par statut
            const statusCtx = document.getElementById('statusReservationsChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['En attente', 'Confirmée', 'Terminee', 'Annulée', 'Reconduite'],
                    datasets: [{
                        data: [
                            statusCounts.pending,
                            statusCounts.confirmed,
                            statusCounts.completed,
                            statusCounts.cancelled,
                            statusCounts.reconducted
                        ],
                        backgroundColor: [
                            '#ffc107', // Jaune pour en attente
                            '#28a745', // Vert pour confirmée
                            '#6c757d', // gris pour terminee
                            '#dc3545', // Rouge pour annulée
                            '#17a2b8' // Bleu turquoise pour reconduite
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%', // Pour un effet "anneau" plus prononcé
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) :
                                        0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
