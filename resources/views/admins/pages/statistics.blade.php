@extends('layouts.app')
@section('content')
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #E85A29;
            --primary-light: #FFE9E0;
            --secondary: #004E89;
            --secondary-dark: #003D6E;
            --secondary-light: #E6F0F9;
            --success: #06D6A0;
            --success-dark: #04A777;
            --success-light: #E6FBF5;
            --warning: #FFB627;
            --warning-dark: #F5A623;
            --warning-light: #FFF5E6;
            --danger: #EF476F;
            --danger-dark: #D43A5E;
            --danger-light: #FDE8ED;
            --info: #667eea;
            --info-dark: #5568d3;
            --info-light: #F0F3FF;
            --bg-main: #FAFBFC;
            --bg-card: #FFFFFF;
            --bg-sidebar: #0F172A;
            --text-primary: #1A202C;
            --text-secondary: #718096;
            --text-light: #CBD5E0;
            --text-white: #FFFFFF;
            --border: #E2E8F0;
            --border-light: #F1F5F9;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.1);
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.04);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --transition: all 0.3s ease;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border);
        }

        .header-left h1 {
            font-family: 'Clash Display', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.375rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-subtitle i {
            color: var(--primary);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .period-selector {
            display: flex;
            gap: 0.25rem;
            background: var(--bg-card);
            padding: 0.375rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .period-btn {
            padding: 0.625rem 1.5rem;
            border: none;
            background: transparent;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            color: var(--text-secondary);
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .period-btn.active {
            background: var(--primary);
            color: var(--text-white);
            box-shadow: var(--shadow-sm);
        }

        .period-btn:hover:not(.active) {
            background: var(--bg-main);
            color: var(--text-primary);
        }

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .kpi-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .kpi-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .kpi-title {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        .kpi-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            font-family: 'Clash Display', sans-serif;
        }

        .kpi-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .trend-up {
            color: var(--success);
        }

        .trend-down {
            color: var(--danger);
        }

        .trend-neutral {
            color: var(--warning);
        }

        /* Chart Section */
        .chart-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .chart-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .chart-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .chart-actions {
            display: flex;
            gap: 0.5rem;
        }

        .chart-btn {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chart-btn:hover {
            background: var(--bg-main);
            color: var(--primary);
            border-color: var(--primary);
        }

        .chart-legend {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        /* Full Width Charts */
        .full-width-chart {
            grid-column: 1 / -1;
        }

        /* Table Section */
        .table-section {
            margin-bottom: 2.5rem;
        }

        .table-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .table-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .search-box {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            width: 300px;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--bg-main);
        }

        .search-box:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
            background: var(--bg-card);
        }

        .export-btn {
            padding: 0.75rem 1.5rem;
            background: var(--secondary);
            color: var(--text-white);
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .export-btn:hover {
            background: var(--secondary-dark);
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: var(--bg-main);
            border-bottom: 2px solid var(--border);
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: var(--bg-main);
            transform: translateX(5px);
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.875rem;
        }

        .rank-1 {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: white;
        }

        .rank-2 {
            background: linear-gradient(135deg, #C0C0C0 0%, #808080 100%);
            color: white;
        }

        .rank-3 {
            background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%);
            color: white;
        }

        .rank-other {
            background: var(--bg-main);
            color: var(--text-secondary);
        }

        /* Map Section */
        .map-section {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .map-placeholder {
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            position: relative;
            overflow: hidden;
        }

        .map-overlay {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow);
        }

        .country-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .country-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: var(--bg-main);
            border-radius: var(--radius-md);
            transition: var(--transition);
            cursor: pointer;
        }

        .country-item:hover {
            background: var(--primary);
            color: white;
            transform: translateX(5px);
        }

        .country-name {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
        }

        .country-flag {
            font-size: 1.5rem;
        }

        .country-value {
            font-weight: 700;
            font-size: 1.1rem;
        }




        /* Responsive */
        @media (max-width: 1200px) {

            .chart-section,
            .map-section {
                grid-template-columns: 1fr;
            }

            .kpi-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 992px) {

            .header {
                flex-direction: column;
                gap: 1.5rem;
                align-items: flex-start;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .period-selector {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .table-actions {
                width: 100%;
                flex-direction: column;
            }

            .search-box {
                width: 100%;
            }

            .export-btn {
                width: 100%;
                justify-content: center;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            .footer {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .kpi-card,
        .chart-card,
        .table-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .kpi-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .kpi-card:nth-child(3) {
            animation-delay: 0.2s;
        }

        .kpi-card:nth-child(4) {
            animation-delay: 0.3s;
        }

        .kpi-card:nth-child(5) {
            animation-delay: 0.4s;
        }

        .kpi-card:nth-child(6) {
            animation-delay: 0.5s;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-main);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        /* Tooltip */
        [data-tooltip] {
            position: relative;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-primary);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 1000;
        }

        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 5px);
        }

        .tooltip {
            position: absolute;
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
            z-index: 1000;
            white-space: nowrap;
            pointer-events: none;
        }

        .tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: var(--bg-secondary) transparent transparent transparent;
        }
    </style>

    <div class="container-fluid py-4">

        <!-- Main Content -->
        <header class="header py-5">
            <div class="header-left">
                <h1>Tableau de Bord Statistiques</h1>
                <p class="header-subtitle">
                    <i class="fas fa-chart-line"></i>
                    Vue d'ensemble des performances de la plateforme MOKAZ
                </p>
            </div>
            <div class="header-right">
                <div class="period-selector">
                    <button class="period-btn" data-period="today">Aujourd'hui</button>
                    <button class="period-btn" data-period="week">Semaine</button>
                    <button class="period-btn active" data-period="month">Mois</button>
                    <button class="period-btn" data-period="quarter">Trimestre</button>
                    <button class="period-btn" data-period="year">Année</button>
                </div>
            </div>
        </header>

        <!-- KPI Cards -->
        <section class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-header">
                    <div>
                        <div class="kpi-title">Visiteurs Uniques</div>
                    </div>
                    <div class="kpi-icon" style="background: var(--primary-light); color: var(--primary);">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="kpi-value loading">Chargement...</div>
                <div class="kpi-trend">
                    <div class="trend-content">
                        <i class="fas fa-minus"></i>
                        <span>--%</span>
                        <span class="comparison"></span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%;"></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div>
                        <div class="kpi-title">Sessions Totales</div>
                    </div>
                    <div class="kpi-icon" style="background: var(--secondary-light); color: var(--secondary);">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                </div>
                <div class="kpi-value loading">Chargement...</div>
                <div class="kpi-trend">
                    <div class="trend-content">
                        <i class="fas fa-minus"></i>
                        <span>--%</span>
                        <span class="comparison"></span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%;"></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div>
                        <div class="kpi-title">Durée Moyenne Session</div>
                    </div>
                    <div class="kpi-icon" style="background: var(--success-light); color: var(--success);">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="kpi-value loading">Chargement...</div>
                <div class="kpi-trend">
                    <div class="trend-content">
                        <i class="fas fa-minus"></i>
                        <span>--%</span>
                        <span class="comparison"></span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%;"></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div>
                        <div class="kpi-title">Taux de Rebond</div>
                    </div>
                    <div class="kpi-icon" style="background: var(--warning-light); color: var(--warning);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="kpi-value loading">Chargement...</div>
                <div class="kpi-trend">
                    <div class="trend-content">
                        <i class="fas fa-minus"></i>
                        <span>--%</span>
                        <span class="comparison"></span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%; background: var(--success);"></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div>
                        <div class="kpi-title">Nouveaux Visiteurs</div>
                    </div>
                    <div class="kpi-icon" style="background: var(--info-light); color: var(--info);">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="kpi-value loading">Chargement...</div>
                <div class="kpi-trend">
                    <div class="trend-content">
                        <i class="fas fa-minus"></i>
                        <span>--%</span>
                        <span class="comparison">0% du total</span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%;"></div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div>
                        <div class="kpi-title">Visiteurs Récurrents</div>
                    </div>
                    <div class="kpi-icon" style="background: rgba(118, 75, 162, 0.15); color: #764ba2;">
                        <i class="fas fa-redo"></i>
                    </div>
                </div>
                <div class="kpi-value loading">Chargement...</div>
                <div class="kpi-trend">
                    <div class="trend-content">
                        <i class="fas fa-minus"></i>
                        <span>--%</span>
                        <span class="comparison">0% du total</span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%;"></div>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="chart-section table-responsive">
            <div class="chart-card full-width-chart">
                <div class="chart-header">
                    <h3 class="chart-title">Évolution du Trafic - 12 Derniers Mois</h3>
                    <div class="chart-actions">
                        <button class="chart-btn" data-tooltip="Télécharger">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="chart-btn" data-tooltip="Plein écran">
                            <i class="fas fa-expand"></i>
                        </button>
                        <button class="chart-btn" id="refreshChart" data-tooltip="Actualiser">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: var(--primary);"></div>
                        <span>Visiteurs uniques</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: var(--secondary);"></div>
                        <span>Sessions</span>
                    </div>
                </div>
                <div class="chart-container">
                    <div>
                        <canvas id="trafficChart" style="min-height: 300px;"></canvas>
                    </div>
                    <div class="chart-loading">
                        <div class="spinner"></div>
                        <p>Chargement des données...</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sources Chart Section (si vous l'avez) -->
        <section class="chart-section table-responsive w-100">
            <!-- Graphique des sources -->
            <div class="chart-card border border-1 border-danger">
                <div class="chart-header">
                    <h3 class="chart-title">Sources de Trafic</h3>
                    <div class="chart-actions">
                        <button class="chart-btn" data-tooltip="Actualiser" id="refreshSourceChart">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="sourceChart" height="250"></canvas>
                    <div class="chart-loading" style="display: none;">
                        <div class="spinner"></div>
                        <p>Chargement des sources...</p>
                    </div>
                </div>
            </div>

            <!-- Répartition par canal -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Répartition par Canal</h3>
                    <div class="chart-actions">
                        <button class="chart-btn" data-tooltip="Actualiser" id="refreshCanalData">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div style="margin-top: 2rem;" id="canalData">
                    <!-- Les données seront chargées dynamiquement ici -->
                    <div class="loading-placeholder">
                        <div class="spinner small" style="margin: 0 auto 1rem;"></div>
                        <p style="text-align: center; color: var(--text-secondary);">Chargement des données...</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- <section class="map-section table-responsive">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Origine Géographique des Visiteurs</h3>
                    <div class="chart-actions">
                        <button class="chart-btn" data-tooltip="Explorer">
                            <i class="fas fa-globe-africa"></i>
                        </button>
                    </div>
                </div>
                <div class="map-placeholder">
                    <div style="text-align: center;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div>Carte interactive des visiteurs</div>
                        <div style="font-size: 0.9rem; opacity: 0.8; margin-top: 0.5rem;">Intégration Google Maps / Leaflet
                        </div>
                    </div>
                    <div class="map-overlay">
                        <div style="font-weight: 700; margin-bottom: 0.5rem;">Afrique de l'Ouest</div>
                        <div style="font-size: 0.875rem;">85% des visiteurs</div>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Top 10 Pays</h3>
                </div>
                <div class="country-list">
                    <div class="country-item">
                        <div class="country-name">
                            <span class="country-flag">🇨🇮</span>
                            <span>Côte d'Ivoire</span>
                        </div>
                        <div class="country-value">34,582</div>
                    </div>
                    <div class="country-item">
                        <div class="country-name">
                            <span class="country-flag">🇫🇷</span>
                            <span>France</span>
                        </div>
                        <div class="country-value">8,932</div>
                    </div>
                    <div class="country-item">
                        <div class="country-name">
                            <span class="country-flag">🇸🇳</span>
                            <span>Sénégal</span>
                        </div>
                        <div class="country-value">5,241</div>
                    </div>
                    <div class="country-item">
                        <div class="country-name">
                            <span class="country-flag">🇲🇱</span>
                            <span>Mali</span>
                        </div>
                        <div class="country-value">3,876</div>
                    </div>
                    <div class="country-item">
                        <div class="country-name">
                            <span class="country-flag">🇧🇫</span>
                            <span>Burkina Faso</span>
                        </div>
                        <div class="country-value">2,654</div>
                    </div>
                </div>
            </div>
        </section> --}}
        <section class="map-section table-responsive">
            <!-- Carte Leaflet -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title" id="mapTitle">Origine Géographique des Visiteurs</h3>
                    <div class="chart-actions">
                        <button class="chart-btn" id="refreshMap" data-tooltip="Actualiser la carte">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button class="chart-btn" id="toggleLegend" data-tooltip="Afficher la légende">
                            <i class="fas fa-layer-group"></i>
                        </button>
                        <button class="chart-btn" data-tooltip="Plein écran"
                            onclick="dashboard.toggleFullscreen(this.closest('.chart-card'))">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="map-container">
                    <div id="visitorMap" style="height: 400px; border-radius: 8px;"></div>
                    <div class="map-loading">
                        <div class="spinner"></div>
                        <p>Chargement de la carte...</p>
                    </div>
                    <!-- Overlay avec statistiques mondiales -->
                    <div class="map-overlay" id="globalStatsOverlay">
                        <div class="global-stats">
                            <div class="global-stat-item">
                                <div class="global-stat-label">🌍 Monde</div>
                                <div class="global-stat-value">100%</div>
                            </div>
                            <div class="global-stat-item" id="topCountryStat">
                                <div class="global-stat-label">🏆 Top Pays</div>
                                <div class="global-stat-value">--%</div>
                            </div>
                            <div class="global-stat-item" id="africaStat">
                                <div class="global-stat-label">🌍 Afrique</div>
                                <div class="global-stat-value">--%</div>
                            </div>
                        </div>
                    </div>
                    <!-- Légende de la carte -->
                    <div class="map-legend" id="mapLegend" style="display: none;">
                        <div class="legend-title">Légende</div>
                        <div class="legend-scale">
                            <div class="legend-color" style="background: #34bf49;"></div>
                            <span class="legend-label">Faible activité</span>
                        </div>
                        <div class="legend-scale">
                            <div class="legend-color" style="background: #ffd700;"></div>
                            <span class="legend-label">Activité moyenne</span>
                        </div>
                        <div class="legend-scale">
                            <div class="legend-color" style="background: #ff6b35;"></div>
                            <span class="legend-label">Forte activité</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des pays et continents -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title" id="countryTitle">Top 10 Pays Mondial</h3>
                    <div class="chart-actions">
                        <button class="chart-btn" id="refreshCountries" data-tooltip="Actualiser">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="geographic-data-container">
                    <!-- Liste des pays -->
                    <div class="country-list" id="countryList">
                        <div class="loading-placeholder">
                            <div class="spinner small" style="margin: 0 auto 1rem;"></div>
                            <p style="text-align: center; color: var(--text-secondary);">Chargement des pays...</p>
                        </div>
                    </div>

                    <!-- Répartition par continent -->
                    <div class="continents-distribution" style="margin-top: 2rem;">
                        <h4 style="font-size: 1rem; margin-bottom: 1rem; color: var(--text-primary);">
                            <i class="fas fa-globe-americas" style="margin-right: 0.5rem;"></i>
                            Répartition par Continent
                        </h4>
                        <div id="continentsList">
                            <div class="loading-placeholder">
                                <div class="spinner small" style="margin: 0 auto 0.5rem;"></div>
                                <p style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                                    Chargement...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="chart-section">
            <div class="chart-card full-width-chart">
                <div class="chart-header">
                    <h3 class="chart-title"></h3>
                    <div class="chart-actions">
                        <button class="chart-btn" data-tooltip="Filtrer">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
                <div>

                    <canvas id="destinationsChart" height="250"></canvas>
                </div>
            </div>


        </section>

        {{-- <!-- Top Pages & Hébergements -->
        <section class="table-section">
            <div class="table-card">
                <div class="table-header">
                    <h3 class="chart-title">Top 10 Pages les Plus Visitées</h3>
                    <div class="table-actions">
                        <input type="text" class="search-box" placeholder="🔍 Rechercher une page...">
                        <button class="export-btn">
                            <i class="fas fa-download"></i> Exporter
                        </button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Page</th>
                            <th>Vues</th>
                            <th>Visiteurs Uniques</th>
                            <th>Durée Moy.</th>
                            <th>Taux Rebond</th>
                            <th>Tendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="rank-badge rank-1">1</span></td>
                            <td><strong>/</strong> (Page d'accueil)</td>
                            <td><strong>32,487</strong></td>
                            <td>28,932</td>
                            <td>2m 45s</td>
                            <td>28.5%</td>
                            <td><span class="status-badge status-success"><i class="fas fa-arrow-up"></i> +12%</span></td>
                        </tr>
                        <tr>
                            <td><span class="rank-badge rank-2">2</span></td>
                            <td><strong>/recherche</strong> (Recherche hébergements)</td>
                            <td><strong>24,391</strong></td>
                            <td>21,043</td>
                            <td>5m 12s</td>
                            <td>18.2%</td>
                            <td><span class="status-badge status-success"><i class="fas fa-arrow-up"></i> +8%</span></td>
                        </tr>
                        <tr>
                            <td><span class="rank-badge rank-3">3</span></td>
                            <td><strong>/hebergement/villa-cocody</strong></td>
                            <td><strong>18,254</strong></td>
                            <td>16,892</td>
                            <td>4m 38s</td>
                            <td>22.1%</td>
                            <td><span class="status-badge status-success"><i class="fas fa-arrow-up"></i> +15%</span></td>
                        </tr>
                        <tr>
                            <td><span class="rank-badge rank-other">4</span></td>
                            <td><strong>/connexion</strong></td>
                            <td><strong>15,672</strong></td>
                            <td>14,239</td>
                            <td>1m 23s</td>
                            <td>42.7%</td>
                            <td><span class="status-badge status-warning"><i class="fas fa-arrow-down"></i> -3%</span>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="rank-badge rank-other">5</span></td>
                            <td><strong>/hebergement/appartement-plateau</strong></td>
                            <td><strong>12,834</strong></td>
                            <td>11,542</td>
                            <td>3m 56s</td>
                            <td>25.8%</td>
                            <td><span class="status-badge status-success"><i class="fas fa-arrow-up"></i> +6%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="table-section">
            <div class="table-card">
                <div class="table-header">
                    <h3 class="chart-title">Top 10 Hébergements les Plus Consultés</h3>
                    <div class="table-actions">
                        <input type="text" class="search-box" placeholder="🔍 Rechercher un hébergement...">
                        <button class="export-btn">
                            <i class="fas fa-download"></i> Exporter
                        </button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Hébergement</th>
                            <th>Localisation</th>
                            <th>Consultations</th>
                            <th>Réservations</th>
                            <th>Tx Conversion</th>
                            <th>Tendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="rank-badge rank-1">1</span></td>
                            <td><strong>Villa Luxe Cocody</strong></td>
                            <td>Cocody, Abidjan</td>
                            <td><strong>18,254</strong></td>
                            <td>847</td>
                            <td>4.6%</td>
                            <td><span class="status-badge status-success"><i class="fas fa-arrow-up"></i> +15%</span></td>
                        </tr>
                        <tr>
                            <td><span class="rank-badge rank-2">2</span></td>
                            <td><strong>Appartement Moderne Plateau</strong></td>
                            <td>Plateau, Abidjan</td>
                            <td><strong>12,834</strong></td>
                            <td>623</td>
                            <td>4.9%</td>
                            <td><span class="status-badge status-success"><i class="fas fa-arrow-up"></i> +6%</span></td>
                        </tr>
                        <tr>
                            <td><span class="rank-badge rank-3">3</span></td>
                            <td><strong>Résidence Standing Marcory</strong></td>
                            <td>Marcory, Abidjan</td>
                            <td><strong>6,932</strong></td>
                            <td>341</td>
                            <td>4.9%</td>
                            <td><span class="status-badge status-success"><i class="fas fa-arrow-up"></i> +9%</span></td>
                        </tr>
                        <tr>
                            <td><span class="rank-badge rank-other">4</span></td>
                            <td><strong>Studio Cosy Yopougon</strong></td>
                            <td>Yopougon, Abidjan</td>
                            <td><strong>5,421</strong></td>
                            <td>298</td>
                            <td>5.5%</td>
                            <td><span class="status-badge status-success"><i class="fas fa-arrow-up"></i> +12%</span></td>
                        </tr>
                        <tr>
                            <td><span class="rank-badge rank-other">5</span></td>
                            <td><strong>Villa Plage Assinie</strong></td>
                            <td>Assinie</td>
                            <td><strong>4,892</strong></td>
                            <td>287</td>
                            <td>5.9%</td>
                            <td><span class="status-badge status-success"><i class="fas fa-arrow-up"></i> +18%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section> --}}

        {{-- <!-- Destinations Populaires -->
        <section class="chart-section table-responsive">
            <div class="chart-card full-width-chart">
                <div class="chart-header">
                    <h3 class="chart-title">Comparaison Mensuelle</h3>
                </div>
                <div style="padding: 1rem 0;">
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2rem; padding: 1.5rem; background: var(--bg-main); border-radius: var(--radius-md);">
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Mois
                                Actuel</div>
                            <div style="font-size: 2rem; font-weight: 800; color: var(--primary);">47,532</div>
                            <div
                                style="font-size: 0.875rem; color: var(--success); font-weight: 600; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-arrow-up"></i> +12.5%
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Mois
                                Dernier</div>
                            <div style="font-size: 2rem; font-weight: 800; color: var(--secondary);">42,241</div>
                            <div
                                style="font-size: 0.875rem; color: var(--text-secondary); font-weight: 600; margin-top: 0.25rem;">
                                Référence</div>
                        </div>
                    </div>

                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2rem; padding: 1.5rem; background: var(--bg-main); border-radius: var(--radius-md);">
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Année en
                                Cours</div>
                            <div style="font-size: 2rem; font-weight: 800; color: var(--primary);">524,382</div>
                            <div
                                style="font-size: 0.875rem; color: var(--success); font-weight: 600; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-arrow-up"></i> +24.8%
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Année
                                Dernière</div>
                            <div style="font-size: 2rem; font-weight: 800; color: var(--secondary);">419,876</div>
                            <div
                                style="font-size: 0.875rem; color: var(--text-secondary); font-weight: 600; margin-top: 0.25rem;">
                                Référence</div>
                        </div>
                    </div>

                    <div
                        style="text-align: center; padding: 1rem; background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-light) 100%); border-radius: var(--radius-md);">
                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Performance
                            globale</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">Excellent</div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Toutes les
                            métriques sont en croissance</div>
                    </div>
                </div>
            </div> --}}


        </section>


        <!-- Loading Overlay -->
        <div id="globalLoading" class="loading-overlay" style="display: none;">
            <div class="loading-content">
                <div class="spinner large"></div>
                <p>Chargement des données du dashboard...</p>
            </div>
        </div>

        <!-- Notification Container -->
        <div id="notificationContainer"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentPeriod = 'Mois'; // Par défaut
            let trafficChart = null;
            let sourceChart = null;
            let destinationsChart = null;

            // Initialiser le dashboard
            async function initializeDashboard() {
                await loadKPIData();
                await loadTrafficChart();
                await loadSourcesChart();
                await loadDestinationsChart();
            }

            // Charger les données KPI
            async function loadKPIData() {
                try {
                    const response = await fetch(`/api/dashboard/kpis?period=${getPeriodKey(currentPeriod)}`);
                    const data = await response.json();
                    
                    updateKPICards(data.kpis);
                    
                    // Mettre à jour la notification dans le bouton actif
                    document.querySelector('.period-btn.active').innerHTML = `
                        ${currentPeriod} 
                        <small style="font-size: 0.7em; margin-left: 5px;">(${data.period_dates.start} à ${data.period_dates.end})</small>
                    `;
                } catch (error) {
                    console.error('Error loading KPI data:', error);
                }
            }

            // Mettre à jour les cartes KPI avec les données
            function updateKPICards(kpis) {
                // Visiteurs Uniques
                document.querySelector('.kpi-card:nth-child(1) .kpi-value').textContent = 
                    formatNumber(kpis.unique_visitors.value);
                updateTrendElement(
                    document.querySelector('.kpi-card:nth-child(1) .kpi-trend'),
                    kpis.unique_visitors.trend,
                    kpis.unique_visitors.trend_direction
                );

                // Sessions Totales
                document.querySelector('.kpi-card:nth-child(2) .kpi-value').textContent = 
                    formatNumber(kpis.total_sessions.value);
                updateTrendElement(
                    document.querySelector('.kpi-card:nth-child(2) .kpi-trend'),
                    kpis.total_sessions.trend,
                    kpis.total_sessions.trend_direction
                );

                // Durée Moyenne Session
                document.querySelector('.kpi-card:nth-child(3) .kpi-value').textContent = 
                    kpis.avg_session_duration.value;
                updateTrendElement(
                    document.querySelector('.kpi-card:nth-child(3) .kpi-trend'),
                    kpis.avg_session_duration.trend,
                    kpis.avg_session_duration.trend_direction
                );

                // Taux de Rebond
                document.querySelector('.kpi-card:nth-child(4) .kpi-value').textContent = 
                    kpis.bounce_rate.value + '%';
                updateTrendElement(
                    document.querySelector('.kpi-card:nth-child(4) .kpi-trend'),
                    kpis.bounce_rate.trend,
                    kpis.bounce_rate.trend_direction
                );

                // Nouveaux Visiteurs
                document.querySelector('.kpi-card:nth-child(5) .kpi-value').textContent = 
                    formatNumber(kpis.new_visitors.value);
                const newVisitorsTrend = document.querySelector('.kpi-card:nth-child(5) .kpi-trend');
                newVisitorsTrend.innerHTML = `
                    <i class="fas fa-arrow-${kpis.new_visitors.trend_direction}"></i>
                    <span>${kpis.new_visitors.trend >= 0 ? '+' : ''}${kpis.new_visitors.trend}%</span>
                    <span style="color: var(--text-secondary); font-weight: 500;">${kpis.new_visitors.percentage}% du total</span>
                `;
                newVisitorsTrend.className = `kpi-trend trend-${kpis.new_visitors.trend_direction}`;

                // Visiteurs Récurrents
                document.querySelector('.kpi-card:nth-child(6) .kpi-value').textContent = 
                    formatNumber(kpis.returning_visitors.value);
                const returningVisitorsTrend = document.querySelector('.kpi-card:nth-child(6) .kpi-trend');
                returningVisitorsTrend.innerHTML = `
                    <i class="fas fa-arrow-${kpis.returning_visitors.trend_direction}"></i>
                    <span>${kpis.returning_visitors.trend >= 0 ? '+' : ''}${kpis.returning_visitors.trend}%</span>
                    <span style="color: var(--text-secondary); font-weight: 500;">${kpis.returning_visitors.percentage}% du total</span>
                `;
                returningVisitorsTrend.className = `kpi-trend trend-${kpis.returning_visitors.trend_direction}`;
            }

            // Charger le graphique de trafic
            async function loadTrafficChart() {
                try {
                    const response = await fetch('/api/dashboard/traffic-chart');
                    const data = await response.json();
                    
                    const trafficCtx = document.getElementById("trafficChart").getContext("2d");
                    
                    if (trafficChart) {
                        trafficChart.destroy();
                    }
                    
                    trafficChart = new Chart(trafficCtx, {
                        type: "line",
                        data: {
                            labels: data.labels,
                            datasets: [
                                {
                                    label: "Visiteurs uniques",
                                    data: data.unique_visitors_data,
                                    borderColor: "#FF6B35",
                                    backgroundColor: "rgba(255, 107, 53, 0.1)",
                                    tension: 0.4,
                                    fill: true,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointBackgroundColor: "#FF6B35",
                                    pointBorderColor: "#FFFFFF",
                                    pointBorderWidth: 2,
                                },
                                {
                                    label: "Sessions",
                                    data: data.total_sessions_data,
                                    borderColor: "#004E89",
                                    backgroundColor: "rgba(0, 78, 137, 0.1)",
                                    tension: 0.4,
                                    fill: true,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointBackgroundColor: "#004E89",
                                    pointBorderColor: "#FFFFFF",
                                    pointBorderWidth: 2,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    backgroundColor: "rgba(0, 0, 0, 0.8)",
                                    padding: 12,
                                    titleFont: {
                                        size: 14,
                                        weight: "bold",
                                    },
                                    bodyFont: {
                                        size: 13,
                                    },
                                    cornerRadius: 8,
                                    mode: "index",
                                    intersect: false,
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: "rgba(0, 0, 0, 0.05)",
                                    },
                                    ticks: {
                                        callback: function (value) {
                                            if (value >= 1000) {
                                                return value / 1000 + "k";
                                            }
                                            return value;
                                        },
                                    },
                                },
                                x: {
                                    grid: {
                                        display: false,
                                    },
                                },
                            },
                            interaction: {
                                intersect: false,
                                mode: "index",
                            },
                        },
                    });
                } catch (error) {
                    console.error('Error loading traffic chart:', error);
                }
            }

            // Charger le graphique des sources
            async function loadSourcesChart() {
                try {
                    const response = await fetch(`/api/dashboard/sources?period=${getPeriodKey(currentPeriod)}`);
                    const data = await response.json();
                    
                    const sourceCtx = document.getElementById("sourceChart").getContext("2d");
                    
                    if (sourceChart) {
                        sourceChart.destroy();
                    }
                    
                    const colors = ["#FF6B35", "#004E89", "#06D6A0", "#FFB627", "#667eea", "#764ba2"];
                    
                    sourceChart = new Chart(sourceCtx, {
                        type: "doughnut",
                        data: {
                            labels: data.sources.map(s => s.source),
                            datasets: [
                                {
                                    data: data.sources.map(s => s.percentage),
                                    backgroundColor: colors.slice(0, data.sources.length),
                                    borderWidth: 0,
                                    hoverOffset: 15,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: "bottom",
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 12,
                                            weight: "600",
                                        },
                                        usePointStyle: true,
                                        pointStyle: "circle",
                                    },
                                },
                                tooltip: {
                                    backgroundColor: "rgba(0, 0, 0, 0.8)",
                                    padding: 12,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function (context) {
                                            return context.label + ": " + context.parsed + "%";
                                        },
                                    },
                                },
                            },
                            cutout: "70%",
                        },
                    });
                } catch (error) {
                    console.error('Error loading sources chart:', error);
                }
            }

            // Charger le graphique des destinations
            async function loadDestinationsChart() {
                try {
                    const response = await fetch(`/api/dashboard/top-cities?period=${getPeriodKey(currentPeriod)}`);
                    const data = await response.json();
                    
                    const destinationsCtx = document.getElementById("destinationsChart").getContext("2d");
                    
                    if (destinationsChart) {
                        destinationsChart.destroy();
                    }
                    
                    const colors = [
                        "rgba(255, 107, 53, 0.8)",
                        "rgba(0, 78, 137, 0.8)",
                        "rgba(6, 214, 160, 0.8)",
                        "rgba(255, 182, 39, 0.8)",
                        "rgba(102, 126, 234, 0.8)",
                        "rgba(118, 75, 162, 0.8)",
                    ];
                    
                    destinationsChart = new Chart(destinationsCtx, {
                        type: "bar",
                        data: {
                            labels: data.cities.map(c => c.city),
                            datasets: [
                                {
                                    label: "Visites",
                                    data: data.cities.map(c => c.count),
                                    backgroundColor: colors.slice(0, data.cities.length),
                                    borderRadius: 8,
                                    borderWidth: 0,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    backgroundColor: "rgba(0, 0, 0, 0.8)",
                                    padding: 12,
                                    cornerRadius: 8,
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: "rgba(0, 0, 0, 0.05)",
                                    },
                                    ticks: {
                                        callback: function (value) {
                                            if (value >= 1000) {
                                                return value / 1000 + "k";
                                            }
                                            return value;
                                        },
                                    },
                                },
                                x: {
                                    grid: {
                                        display: false,
                                    },
                                },
                            },
                        },
                    });
                } catch (error) {
                    console.error('Error loading destinations chart:', error);
                }
            }

            // Fonctions utilitaires
            function formatNumber(num) {
                return new Intl.NumberFormat('fr-FR').format(num);
            }

            function updateTrendElement(element, trend, direction) {
                element.innerHTML = `
                    <i class="fas fa-arrow-${direction}"></i>
                    <span>${trend >= 0 ? '+' : ''}${trend}%</span>
                    <span style="color: var(--text-secondary); font-weight: 500;">vs mois dernier</span>
                `;
                element.className = `kpi-trend trend-${direction}`;
            }

            function getPeriodKey(periodText) {
                const periodMap = {
                    'Aujourd\'hui': 'today',
                    'Semaine': 'week',
                    'Mois': 'month',
                    'Trimestre': 'quarter',
                    'Année': 'year'
                };
                return periodMap[periodText] || 'month';
            }

            // Gestionnaire des boutons de période
            document.querySelectorAll(".period-btn").forEach((btn) => {
                btn.addEventListener("click", async function () {
                    document.querySelectorAll(".period-btn").forEach((b) => {
                        b.classList.remove("active");
                        b.innerHTML = b.getAttribute('data-original-text') || b.textContent;
                    });
                    
                    this.classList.add("active");
                    currentPeriod = this.textContent;
                    this.setAttribute('data-original-text', currentPeriod);
                    
                    // Mettre à jour les données
                    await Promise.all([
                        loadKPIData(),
                        loadSourcesChart(),
                        loadDestinationsChart()
                    ]);
                    
                    showNotification(`Données mises à jour pour la période: ${currentPeriod}`, "success");
                });
            });

            // Initialiser les tooltips pour les boutons d'action
            document.querySelectorAll('[data-tooltip]').forEach(element => {
                element.addEventListener('mouseenter', function() {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip';
                    tooltip.textContent = this.getAttribute('data-tooltip');
                    document.body.appendChild(tooltip);
                    
                    const rect = this.getBoundingClientRect();
                    tooltip.style.position = 'absolute';
                    tooltip.style.top = (rect.top - 35) + 'px';
                    tooltip.style.left = (rect.left + rect.width/2 - tooltip.offsetWidth/2) + 'px';
                    tooltip.style.background = 'var(--bg-secondary)';
                    tooltip.style.color = 'var(--text-primary)';
                    tooltip.style.padding = '5px 10px';
                    tooltip.style.borderRadius = '4px';
                    tooltip.style.fontSize = '12px';
                    tooltip.style.boxShadow = 'var(--shadow-sm)';
                    tooltip.style.zIndex = '1000';
                    
                    this.setAttribute('data-current-tooltip', tooltip);
                });
                
                element.addEventListener('mouseleave', function() {
                    const tooltip = this.getAttribute('data-current-tooltip');
                    if (tooltip) {
                        document.body.removeChild(tooltip);
                        this.removeAttribute('data-current-tooltip');
                    }
                });
            });

            // Initialiser le dashboard
            initializeDashboard();

            // Rafraîchir automatiquement toutes les 5 minutes
            setInterval(async () => {
                await loadKPIData();
                console.log('Dashboard auto-refresh');
            }, 300000); // 5 minutes
        });
    </script> --}}

    <style>
        /* Styles pour les états de chargement */
        .kpi-value.loading {
            color: #aaa;
            font-style: italic;
        }

        .chart-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 10px;
        }

        .spinner.large {
            width: 60px;
            height: 60px;
            border-width: 5px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-content {
            text-align: center;
        }

        /* Styles pour les tendances */
        .trend-up {
            color: #06D6A0;
        }

        .trend-down {
            color: #FF6B35;
        }

        .kpi-trend i {
            margin-right: 5px;
        }

        .comparison {
            color: var(--text-secondary);
            margin-left: 8px;
            font-weight: 500;
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px 20px;
            min-width: 300px;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 10000;
            border-left: 4px solid;
        }

        .notification.show {
            transform: translateX(0);
            opacity: 1;
        }

        .notification-success {
            border-left-color: #06D6A0;
        }

        .notification-error {
            border-left-color: #FF6B35;
        }

        .notification-info {
            border-left-color: #3498db;
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notification-content i {
            font-size: 1.2em;
        }

        .notification-success .notification-content i {
            color: #06D6A0;
        }

        .notification-error .notification-content i {
            color: #FF6B35;
        }

        .notification-close {
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
            margin-left: auto;
            color: #999;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Tooltips */
        .tooltip {
            position: fixed;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 1000;
        }

        .tooltip:after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: rgba(0, 0, 0, 0.8);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }

            .notification {
                left: 20px;
                right: 20px;
                min-width: auto;
            }
        }

        /* Map */
            .map-container {
            position: relative;
            margin-top: 1rem;
        }

        .map-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 8px;
        }

        .map-overlay {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            z-index: 999;
            min-width: 200px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .global-stats {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .global-stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .global-stat-item:last-child {
            border-bottom: none;
        }

        .global-stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .global-stat-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .map-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            z-index: 999;
            min-width: 180px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .legend-title {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: var(--text-primary);
        }

        .legend-scale {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .legend-scale:last-child {
            margin-bottom: 0;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .legend-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .country-list {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .country-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .country-item:last-child {
            border-bottom: none;
        }

        .country-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .country-rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .country-rank.top-3 {
            background: var(--primary);
            color: white;
        }

        .country-flag {
            font-size: 1.4rem;
            min-width: 24px;
        }

        .country-name {
            font-weight: 600;
            color: var(--text-primary);
            flex: 1;
        }

        .country-stats {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            min-width: 120px;
        }

        .country-count {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .country-percentage {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .continent-item {
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .continent-item:last-child {
            border-bottom: none;
        }

        .continent-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .continent-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .continent-name {
            font-weight: 500;
            color: var(--text-primary);
            flex: 1;
        }

        .continent-stats {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            min-width: 100px;
        }

        .continent-percentage {
            font-size: 1rem;
            font-weight: 700;
        }

        .continent-visits {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Scrollbar personnalisée */
        .country-list::-webkit-scrollbar {
            width: 6px;
        }

        .country-list::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 3px;
        }

        .country-list::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        .country-list::-webkit-scrollbar
    </style>
@endsection
