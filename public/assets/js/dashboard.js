// dashboard.js

/**
 * DashboardManager - Gestionnaire principal du tableau de bord
 * Gère les KPIs, graphiques et données analytiques
 */
class DashboardManager {
    constructor() {
        this.currentPeriod = 'month';
        this.isLoading = false;
        this.charts = {
            traffic: null,
            source: null,
            destinations: null
        };
        this.dataCache = new Map();
        this.autoRefreshInterval = null;
        this.geographicManager = null;
        this.initialize();
    }

    initialize() {
        this.setupEventListeners();
        this.loadAllData();
        this.startAutoRefresh();
        
        // Initialiser le gestionnaire géographique après un délai
        setTimeout(() => {
            this.initGeographicManager();
        }, 100);
    }

    initGeographicManager() {
        if (document.getElementById('visitorMap')) {
            this.geographicManager = new GeographicManager(this);
        }
    }

    setupEventListeners() {
        // Gestionnaire des boutons de période
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                if (this.isLoading) return;
                
                const period = e.target.getAttribute('data-period') || this.getPeriodFromText(e.target.textContent);
                await this.changePeriod(period);
            });
        });

        // Boutons d'actualisation
        document.getElementById('refreshChart')?.addEventListener('click', () => this.refreshTrafficChart());
        document.getElementById('refreshSourceChart')?.addEventListener('click', () => this.refreshSourceData());
        document.getElementById('refreshCanalData')?.addEventListener('click', () => this.refreshSourceData());
        document.getElementById('refreshMap')?.addEventListener('click', () => this.refreshGeographicData());
        document.getElementById('refreshCountries')?.addEventListener('click', () => this.refreshGeographicData());
        document.getElementById('refreshAll')?.addEventListener('click', () => this.refreshAllData());

        // Boutons plein écran
        document.querySelectorAll('[data-tooltip="Plein écran"]').forEach(btn => {
            btn.addEventListener('click', (e) => this.toggleFullscreen(e.target.closest('.chart-card')));
        });

        // Boutons de téléchargement
        document.querySelectorAll('[data-tooltip="Télécharger"]').forEach(btn => {
            btn.addEventListener('click', (e) => this.downloadChartData(e.target.closest('.chart-card')));
        });

        // Bouton légende carte
        document.getElementById('toggleLegend')?.addEventListener('click', () => {
            if (this.geographicManager) this.geographicManager.toggleLegend();
        });

        // Observer la visibilité de la page
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.resumeAutoRefresh();
            } else {
                this.pauseAutoRefresh();
            }
        });

        // Gestion des résize pour les graphiques
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => this.handleResize(), 250);
        });

        // Recherche dans les tables
        document.querySelectorAll('.table-search').forEach(input => {
            input.addEventListener('input', (e) => this.filterTable(e.target));
        });
    }

    async changePeriod(period) {
        if (this.currentPeriod === period || this.isLoading) return;
        
        this.currentPeriod = period;
        
        // Mettre à jour le bouton actif
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.classList.remove('active');
            const btnPeriod = btn.getAttribute('data-period') || this.getPeriodFromText(btn.textContent);
            if (btnPeriod === period) {
                btn.classList.add('active');
            }
        });
        
        // Vider le cache pour cette période
        this.dataCache.clear();
        
        // Notifier le changement de période au gestionnaire géographique
        if (this.geographicManager) {
            this.geographicManager.currentPeriod = period;
            this.geographicManager.dataCache.delete(`geographic-${period}`);
            
            // Créer un événement personnalisé
            const event = new CustomEvent('periodChanged', { detail: { period } });
            document.dispatchEvent(event);
        }
        
        await this.loadAllData();
        this.showNotification(`Période changée: ${this.getPeriodName(period)}`, 'success');
    }

    async loadAllData() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showGlobalLoading(true);
        
        try {
            // Charger en parallèle
            await Promise.allSettled([
                this.loadKPIData(),
                this.loadTrafficChart(),
                this.loadSourcesData(),
                this.loadDestinationsChart(),
                this.loadGeographicData()
            ]);
        } catch (error) {
            console.error('Erreur lors du chargement des données:', error);
            this.showNotification('Erreur lors du chargement des données', 'error');
        } finally {
            this.isLoading = false;
            this.showGlobalLoading(false);
        }
    }

    async loadKPIData(forceRefresh = false) {
        const cacheKey = `kpis-${this.currentPeriod}`;
        
        // Vérifier le cache
        if (!forceRefresh && this.dataCache.has(cacheKey)) {
            const data = this.dataCache.get(cacheKey);
            this.updateKPICards(data.kpis, data.comparison_text, data.data_quality);
            return;
        }
        
        try {
            // Afficher le chargement
            this.showKPIloading(true);
            
            const response = await fetch(`/api/dashboard/kpis?period=${this.currentPeriod}`);
            
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            
            // Mettre en cache
            this.dataCache.set(cacheKey, data);
            
            // Mettre à jour l'interface
            this.updateKPICards(data.kpis, data.comparison_text, data.data_quality);
            
        } catch (error) {
            console.error('Erreur KPI:', error);
            this.showKPIError();
            throw error;
        } finally {
            this.showKPIloading(false);
        }
    }

    updateKPICards(kpis, comparisonText, dataQuality) {
        const formatNumber = (num) => {
            if (typeof num !== 'number') return '0';
            if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
            if (num >= 1000) return (num / 1000).toFixed(1) + 'k';
            return new Intl.NumberFormat('fr-FR').format(num);
        };

        const kpiCards = document.querySelectorAll('.kpi-card');
        
        if (!kpiCards.length) return;

        // Calcul des pourcentages pour les barres de progression
        const maxUniqueVisitors = Math.max(kpis.unique_visitors.value, 1);
        const maxSessions = Math.max(kpis.total_sessions.value, 1);
        const maxDuration = Math.max(kpis.avg_session_duration.raw_value || 600, 1); // 10 minutes max
        const maxBounceRate = 100; // Pourcentage

        // Carte 1: Visiteurs Uniques
        this.updateKPICard(kpiCards[0], {
            value: formatNumber(kpis.unique_visitors.value),
            trend: kpis.unique_visitors.trend,
            direction: kpis.unique_visitors.trend_direction,
            progress: Math.min((kpis.unique_visitors.value / maxUniqueVisitors) * 100, 100),
            comparison: comparisonText || 'vs période précédente',
            label: 'Visiteurs Uniques'
        });

        // Carte 2: Sessions Totales
        this.updateKPICard(kpiCards[1], {
            value: formatNumber(kpis.total_sessions.value),
            trend: kpis.total_sessions.trend,
            direction: kpis.total_sessions.trend_direction,
            progress: Math.min((kpis.total_sessions.value / maxSessions) * 100, 100),
            comparison: comparisonText || 'vs période précédente',
            label: 'Sessions Totales'
        });

        // Carte 3: Durée Moyenne Session
        const durationValue = kpis.avg_session_duration.raw_value || 0;
        const durationWarning = kpis.avg_session_duration.warning || null;
        this.updateKPICard(kpiCards[2], {
            value: kpis.avg_session_duration.value,
            trend: kpis.avg_session_duration.trend,
            direction: kpis.avg_session_duration.trend_direction,
            progress: Math.min((durationValue / maxDuration) * 100, 100),
            comparison: comparisonText || 'vs période précédente',
            warning: durationWarning,
            label: 'Durée Moyenne Session'
        });

        // Carte 4: Taux de Rebond
        this.updateKPICard(kpiCards[3], {
            value: `${kpis.bounce_rate.value}%`,
            trend: kpis.bounce_rate.trend,
            direction: kpis.bounce_rate.trend_direction,
            progress: Math.min(kpis.bounce_rate.value, 100),
            isBounce: true,
            comparison: comparisonText || 'vs période précédente',
            label: 'Taux de Rebond'
        });

        // Carte 5: Nouveaux Visiteurs
        this.updateKPICard(kpiCards[4], {
            value: formatNumber(kpis.new_visitors.value),
            trend: kpis.new_visitors.trend,
            direction: kpis.new_visitors.trend_direction,
            progress: kpis.new_visitors.percentage,
            // progress: kpis.new_visitors.percentage,
            comparison: `${kpis.new_visitors.percentage}% du total`,
            label: 'Nouveaux Visiteurs'
        });

        // Carte 6: Visiteurs Récurrents
        this.updateKPICard(kpiCards[5], {
            value: formatNumber(kpis.returning_visitors.value),
            trend: kpis.returning_visitors.trend,
            direction: kpis.returning_visitors.trend_direction,
            progress: kpis.returning_visitors.percentage,
            comparison: `${kpis.returning_visitors.percentage}% du total`,
            label: 'Visiteurs Récurrents'
        });

        // Afficher l'alerte de qualité des données si nécessaire
        this.showDataQualityAlert(dataQuality);
    }

    showDataQualityAlert(dataQuality) {
        const alertContainer = document.getElementById('dataQualityAlert');
        if (!alertContainer) return;

        if (dataQuality && dataQuality.has_issues) {
            let alertHtml = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Problèmes de qualité des données détectés</strong>
                    </div>
                    <ul class="mb-2 mt-2 ps-3">`;

            dataQuality.issues.forEach(issue => {
                alertHtml += `<li class="small">${issue}</li>`;
            });

            alertHtml += `
                    </ul>
                    <small class="text-muted d-block mt-1">Ces problèmes peuvent fausser les statistiques affichées.</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;

            alertContainer.innerHTML = alertHtml;
            alertContainer.style.display = 'block';
            
            // Ajouter l'animation Bootstrap
            const alertElement = alertContainer.querySelector('.alert');
            alertElement.classList.add('show');
        } else {
            alertContainer.style.display = 'none';
            alertContainer.innerHTML = '';
        }
    }

    updateKPICard(card, data) {
        // Valeur principale
        const valueEl = card.querySelector('.kpi-value');
        if (valueEl) {
            valueEl.textContent = data.value;
            valueEl.classList.remove('loading', 'error');
        }

        // Label (titre)
        const labelEl = card.querySelector('.kpi-label');
        if (labelEl && data.label) {
            labelEl.textContent = data.label;
        }

        // Tendance
        const trendEl = card.querySelector('.kpi-trend');
        if (trendEl) {
            trendEl.className = `kpi-trend trend-${data.direction}`;
            const arrowIcon = data.direction === 'up' ? 'fa-arrow-up' : 
                            data.direction === 'down' ? 'fa-arrow-down' : 'fa-minus';
            
            trendEl.innerHTML = `
                <div class="trend-content d-flex align-items-center gap-2">
                    <i class="fas ${arrowIcon}"></i>
                    <span>${data.trend >= 0 ? '+' : ''}${Math.abs(data.trend)}%</span>
                    <span class="comparison text-muted small">${data.comparison}</span>
                </div>
            `;
            
            // Pour le taux de rebond, inverser la couleur
            if (data.isBounce) {
                trendEl.classList.add('bounce-rate');
            }
        }

        // Barre de progression
        const progressFill = card.querySelector('.progress-fill');
        if (progressFill) {
            progressFill.style.width = `${data.progress}%`;
            progressFill.style.transition = 'width 0.8s ease';
            
            // Couleur basée sur le type de KPI
            if (data.isBounce) {
                // Pour le taux de rebond, rouge pour haut, vert pour bas
                progressFill.style.backgroundColor = data.progress > 50 ? '#dc3545' : '#28a745';
            } else if (data.label?.includes('Durée')) {
                progressFill.style.backgroundColor = '#ffc107'; // Jaune pour durée
            } else {
                progressFill.style.backgroundColor = '#007bff'; // Bleu par défaut
            }
        }

        // Avertissement si présent
        const warningEl = card.querySelector('.kpi-warning');
        if (warningEl && data.warning) {
            warningEl.textContent = data.warning;
            warningEl.style.display = 'block';
        } else if (warningEl) {
            warningEl.style.display = 'none';
        }

        // Pourcentage (pour nouveaux/retournants)
        const percentageEl = card.querySelector('.kpi-percentage');
        if (percentageEl && (data.label?.includes('Nouveaux') || data.label?.includes('Récurrents'))) {
            percentageEl.textContent = `${Math.round(data.progress)}%`;
            percentageEl.style.display = 'block';
        } else if (percentageEl) {
            percentageEl.style.display = 'none';
        }
    }

    async loadTrafficChart(forceRefresh = false) {
        const cacheKey = `traffic-${this.currentPeriod}`;
        
        // Vérifier le cache
        if (!forceRefresh && this.dataCache.has(cacheKey)) {
            this.renderTrafficChart(this.dataCache.get(cacheKey));
            return;
        }
        
        const canvas = document.getElementById('trafficChart');
        if (!canvas) return;
        
        const container = canvas.closest('.chart-container');
        const loadingEl = container?.querySelector('.chart-loading');
        
        if (loadingEl) loadingEl.style.display = 'flex';
        
        try {
            const response = await fetch(`/api/dashboard/traffic-chart?period=${this.currentPeriod}`);
            
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            
            // Mettre en cache
            this.dataCache.set(cacheKey, data);
            
            // Rendre le graphique
            this.renderTrafficChart(data);
            
        } catch (error) {
            console.error('Erreur graphique trafic:', error);
            this.showChartError(canvas, 'Erreur de chargement du graphique');
        } finally {
            if (loadingEl) loadingEl.style.display = 'none';
        }
    }

    renderTrafficChart(data) {
        const canvas = document.getElementById('trafficChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        // Détruire le graphique existant
        if (this.charts.traffic) {
            this.charts.traffic.destroy();
        }
        
        // Mettre à jour le titre selon la période
        const titleMap = {
            'today': 'Évolution du Trafic - Aujourd\'hui',
            'week': 'Évolution du Trafic - Cette Semaine',
            'month': 'Évolution du Trafic - Ce Mois',
            'quarter': 'Évolution du Trafic - Ce Trimestre',
            'year': 'Évolution du Trafic - Cette Année'
        };
        
        const chartTitle = document.querySelector('.full-width-chart .chart-title');
        if (chartTitle) {
            chartTitle.textContent = titleMap[this.currentPeriod] || 'Évolution du Trafic';
        }
        
        // Mettre à jour les statistiques du trafic
        this.updateTrafficStats(data.summary);
        
        this.charts.traffic = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: [
                    {
                        label: 'Visiteurs uniques',
                        data: data.unique_visitors_data || [],
                        borderColor: '#FF6B35',
                        backgroundColor: 'rgba(255, 107, 53, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#FF6B35',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#FF6B35',
                        pointHoverBorderColor: '#FFFFFF',
                        pointHoverBorderWidth: 3
                    },
                    {
                        label: 'Sessions totales',
                        data: data.total_sessions_data || [],
                        borderColor: '#004E89',
                        backgroundColor: 'rgba(0, 78, 137, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#004E89',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#004E89',
                        pointHoverBorderColor: '#FFFFFF',
                        pointHoverBorderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 12,
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            color: '#333'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold',
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        bodyFont: {
                            size: 13,
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        footerFont: {
                            size: 12,
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        cornerRadius: 8,
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            },
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('fr-FR').format(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            callback: function(value) {
                                if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'k';
                                }
                                return value;
                            }
                        },
                        title: {
                            display: true,
                            text: 'Nombre de visites',
                            color: '#666',
                            font: {
                                size: 12,
                                weight: 'normal',
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            maxRotation: 45
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 750,
                    easing: 'easeOutQuart'
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                },
                hover: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    }

    updateTrafficStats(summary) {
        const statsContainer = document.getElementById('trafficStats');
        if (!statsContainer || !summary) return;

        const formatNumber = (num) => {
            if (typeof num !== 'number') return '0';
            return new Intl.NumberFormat('fr-FR').format(Math.round(num));
        };

        statsContainer.innerHTML = `
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="stat-card p-3 bg-light rounded">
                        <div class="stat-label text-muted small mb-1">Total visiteurs</div>
                        <div class="stat-value fw-bold fs-5 text-primary">${formatNumber(summary.total_unique_visitors)}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card p-3 bg-light rounded">
                        <div class="stat-label text-muted small mb-1">Total sessions</div>
                        <div class="stat-value fw-bold fs-5 text-primary">${formatNumber(summary.total_sessions)}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card p-3 bg-light rounded">
                        <div class="stat-label text-muted small mb-1">Moyenne visiteurs/jour</div>
                        <div class="stat-value fw-bold fs-5 text-primary">${formatNumber(summary.avg_unique_visitors)}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card p-3 bg-light rounded">
                        <div class="stat-label text-muted small mb-1">Jour de pic</div>
                        <div class="stat-value fw-bold fs-5 text-primary">${summary.peak_day ? formatNumber(summary.peak_day.unique_visitors) : 'N/A'}</div>
                        ${summary.peak_day ? `<div class="stat-subtitle text-muted small mt-1">${summary.peak_day.label}</div>` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    async loadSourcesData(forceRefresh = false) {
        const cacheKey = `sources-${this.currentPeriod}`;
        
        // Vérifier le cache
        if (!forceRefresh && this.dataCache.has(cacheKey)) {
            const data = this.dataCache.get(cacheKey);
            this.renderSourceChart(data);
            this.renderCanalDistribution(data);
            return;
        }
        
        try {
            // Afficher les états de chargement
            this.showSourceLoading(true);
            
            const response = await fetch(`/api/dashboard/sources?period=${this.currentPeriod}`);
            
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            
            // Mettre en cache
            this.dataCache.set(cacheKey, data);
            
            // Rendre les graphiques
            this.renderSourceChart(data);
            this.renderCanalDistribution(data);
            
        } catch (error) {
            console.error('Erreur sources:', error);
            this.showSourceError();
        } finally {
            this.showSourceLoading(false);
        }
    }

    renderSourceChart(data) {
        const canvas = document.getElementById('sourceChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        // Détruire le graphique existant
        if (this.charts.source) {
            this.charts.source.destroy();
        }
        
        const sources = data.sources || [];
        const total = data.global_stats?.total_sessions || sources.reduce((sum, source) => sum + (source.sessions || 0), 0);
        
        // Mettre à jour le titre
        const titleMap = {
            'today': 'Sources du Trafic - Aujourd\'hui',
            'week': 'Sources du Trafic - Cette Semaine',
            'month': 'Sources du Trafic - Ce Mois',
            'quarter': 'Sources du Trafic - Ce Trimestre',
            'year': 'Sources du Trafic - Cette Année'
        };
        
        const chartTitle = canvas.closest('.chart-card').querySelector('.chart-title');
        if (chartTitle) {
            chartTitle.textContent = titleMap[this.currentPeriod] || 'Sources de Trafic';
        }
        
        // Mettre à jour les statistiques globales
        this.updateSourceGlobalStats(data.global_stats);
        
        this.charts.source = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: sources.map(s => s.source),
                datasets: [{
                    data: sources.map(s => s.percentage),
                    backgroundColor: sources.map(s => s.color || this.getSourceColor(sources.indexOf(s))),
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 15,
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '600',
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: '#333',
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const color = data.datasets[0].backgroundColor[i];
                                        
                                        return {
                                            text: `${label} (${value}%)`,
                                            fillStyle: color,
                                            strokeStyle: color,
                                            lineWidth: 2,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: {
                            size: 14,
                            weight: 'bold',
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        bodyFont: {
                            size: 13,
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        callbacks: {
                            label: (context) => {
                                const source = sources[context.dataIndex];
                                const bounceText = source.bounce_rate ? ` | Rebond: ${source.bounce_rate}%` : '';
                                const durationText = source.avg_duration ? ` | Durée: ${source.avg_duration}` : '';
                                return [
                                    `${source.source}: ${source.percentage}%`,
                                    `Visites: ${new Intl.NumberFormat('fr-FR').format(source.sessions || 0)}`,
                                    `Visiteurs uniques: ${new Intl.NumberFormat('fr-FR').format(source.unique_visits || 0)}${bounceText}${durationText}`
                                ];
                            }
                        }
                    }
                },
                cutout: '65%',
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 750,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    updateSourceGlobalStats(globalStats) {
        const statsContainer = document.getElementById('sourceGlobalStats');
        if (!statsContainer || !globalStats) return;

        statsContainer.innerHTML = `
            <div class="source-stats-grid row g-3">
                <div class="col-6 col-md-3">
                    <div class="source-stat-item p-2">
                        <div class="source-stat-label text-muted small">Total sessions</div>
                        <div class="source-stat-value fw-bold">${globalStats.total_sessions.toLocaleString('fr-FR')}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="source-stat-item p-2">
                        <div class="source-stat-label text-muted small">Visiteurs uniques</div>
                        <div class="source-stat-value fw-bold">${globalStats.total_unique_visits.toLocaleString('fr-FR')}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="source-stat-item p-2">
                        <div class="source-stat-label text-muted small">Taux rebond global</div>
                        <div class="source-stat-value fw-bold ${globalStats.avg_bounce_rate > 50 ? 'text-warning' : 'text-success'}">
                            ${globalStats.avg_bounce_rate}%
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="source-stat-item p-2">
                        <div class="source-stat-label text-muted small">Source principale</div>
                        <div class="source-stat-value fw-bold">${globalStats.top_source}</div>
                    </div>
                </div>
            </div>
        `;
    }

    renderCanalDistribution(data) {
        const container = document.getElementById('canalData');
        if (!container) return;
        
        const sources = data.sources || [];
        
        // Trier par pourcentage décroissant
        const sortedSources = [...sources].sort((a, b) => b.percentage - a.percentage);
        
        // Générer le HTML
        let html = '';
        
        sortedSources.forEach((source, index) => {
            if (index < 5) { // Limiter à 5 canaux principaux
                const color = source.color || this.getSourceColor(index);
                const icon = source.icon || this.getSourceIcon(source.source);
                const bounceClass = source.bounce_rate > 50 ? 'text-warning' : 'text-success';
                const durationClass = this.getDurationClass(source.avg_duration);
                
                html += `
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas ${icon} me-2" style="color: ${color};"></i>
                                <span class="fw-bold">${source.source}</span>
                            </div>
                            <span class="fw-bold" style="color: ${color};">${source.percentage}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar" style="width: ${source.percentage}%; background-color: ${color};"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                ${new Intl.NumberFormat('fr-FR').format(source.sessions || 0)} visites
                                ${source.unique_visits ? `(${new Intl.NumberFormat('fr-FR').format(source.unique_visits)} uniques)` : ''}
                            </small>
                            <div class="d-flex gap-3">
                                ${source.bounce_rate ? `
                                    <small class="${bounceClass}">
                                        <i class="fas fa-sign-out-alt me-1"></i>${source.bounce_rate}%
                                    </small>
                                ` : ''}
                                ${source.avg_duration ? `
                                    <small class="${durationClass}">
                                        <i class="fas fa-clock me-1"></i>${source.avg_duration}
                                    </small>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }
        });
        
        // Ajouter le résumé avec la période
        const periodName = this.getPeriodName(this.currentPeriod);
        const total = data.global_stats?.total_sessions || sortedSources.reduce((sum, source) => sum + (source.sessions || 0), 0);
        
        html += `
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                    <span class="text-muted">
                        <i class="fas fa-chart-pie me-2"></i>
                        Total des sessions (${periodName.toLowerCase()})
                    </span>
                    <span class="fw-bold fs-5">${new Intl.NumberFormat('fr-FR').format(total)}</span>
                </div>
                <div class="text-muted small">
                    ${sortedSources.length} sources de trafic analysées
                </div>
            </div>
        `;
        
        container.innerHTML = html;
    }

    getDurationClass(durationStr) {
        if (!durationStr) return 'text-muted';
        
        // Extraire les minutes de la durée formatée (ex: "5m 30s")
        const minutesMatch = durationStr.match(/(\d+)\s*m/);
        if (minutesMatch) {
            const minutes = parseInt(minutesMatch[1]);
            if (minutes < 1) return 'text-danger';
            if (minutes < 3) return 'text-warning';
            if (minutes >= 3) return 'text-success';
        }
        return 'text-muted';
    }

    async loadDestinationsChart(forceRefresh = false) {
        const cacheKey = `cities-${this.currentPeriod}`;
        
        // Vérifier le cache
        if (!forceRefresh && this.dataCache.has(cacheKey)) {
            this.renderDestinationsChart(this.dataCache.get(cacheKey));
            return;
        }
        
        try {
            const canvas = document.getElementById('destinationsChart');
            if (!canvas) return;
            
            const container = canvas.closest('.chart-container');
            const loadingEl = container?.querySelector('.chart-loading');
            
            if (loadingEl) loadingEl.style.display = 'flex';
            
            const response = await fetch(`/api/dashboard/top-cities?period=${this.currentPeriod}`);
            
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            
            // Mettre en cache
            this.dataCache.set(cacheKey, data);
            
            // Rendre le graphique
            this.renderDestinationsChart(data);
            
        } catch (error) {
            console.error('Erreur destinations:', error);
        } finally {
            const loadingEl = document.querySelector('#destinationsChart')?.closest('.chart-container')?.querySelector('.chart-loading');
            if (loadingEl) loadingEl.style.display = 'none';
        }
    }

    renderDestinationsChart(data) {
        const canvas = document.getElementById('destinationsChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        // Détruire le graphique existant
        if (this.charts.destinations) {
            this.charts.destinations.destroy();
        }
        
        const cities = data.cities || [];
        const maxVisits = Math.max(...cities.map(c => c.count || c.sessions || 0), 1);
        
        // Mettre à jour le titre selon la période
        const titleMap = {
            'today': 'Top Villes - Aujourd\'hui',
            'week': 'Top Villes - Cette Semaine',
            'month': 'Top Villes - Ce Mois',
            'quarter': 'Top Villes - Ce Trimestre',
            'year': 'Top Villes - Cette Année'
        };
        
        const chartTitle = canvas.closest('.chart-card')?.querySelector('.chart-title');
        if (chartTitle) {
            chartTitle.textContent = titleMap[this.currentPeriod] || 'Top Villes';
        }
        
        // Mettre à jour les statistiques géographiques
        this.updateGeoStats(data.geo_stats);
        
        // Mettre à jour la liste des villes
        this.updateCitiesTable(cities);
        
        this.charts.destinations = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: cities.map(c => c.city),
                datasets: [{
                    label: 'Sessions',
                    data: cities.map(c => c.count || c.sessions || 0),
                    backgroundColor: cities.map((_, i) => 
                        `hsla(${i * 60}, 70%, 60%, 0.8)`
                    ),
                    borderRadius: 8,
                    borderWidth: 0,
                    hoverBackgroundColor: cities.map((_, i) => 
                        `hsla(${i * 60}, 70%, 50%, 1)`
                    ),
                    borderSkipped: false
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
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: {
                            size: 14,
                            weight: 'bold',
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        bodyFont: {
                            size: 13,
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        callbacks: {
                            title: function(tooltipItems) {
                                const city = cities[tooltipItems[0].dataIndex];
                                return `${city.city}, ${city.country}`;
                            },
                            label: function(context) {
                                const city = cities[context.dataIndex];
                                const percentage = ((context.parsed.y / maxVisits) * 100).toFixed(1);
                                return [
                                    `Sessions: ${new Intl.NumberFormat('fr-FR').format(context.parsed.y)} (${percentage}%)`,
                                    `Part mondiale: ${city.percentage || '0'}%`,
                                    `Visiteurs uniques: ${city.unique_visitors ? new Intl.NumberFormat('fr-FR').format(city.unique_visitors) : 'N/A'}`,
                                    `Durée moyenne: ${city.avg_duration || 'N/A'}`,
                                    `Dernière visite: ${city.last_visit || 'N/A'}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            callback: function(value) {
                                if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'k';
                                }
                                return value;
                            }
                        },
                        title: {
                            display: true,
                            text: 'Nombre de sessions',
                            color: '#666',
                            font: {
                                size: 12,
                                weight: 'normal',
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11,
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            maxRotation: 45
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    updateGeoStats(geoStats) {
        const statsContainer = document.getElementById('geoStats');
        if (!statsContainer || !geoStats) return;

        let topCountryInfo = '';
        if (geoStats.top_country) {
            const flag = geoStats.top_country.country_code ? 
                this.getCountryFlagFromCode(geoStats.top_country.country_code) : '🏳️';
            
            topCountryInfo = `
                <div class="col-6 col-md-3">
                    <div class="geo-stat-item p-2">
                        <div class="geo-stat-label text-muted small">Pays principal</div>
                        <div class="geo-stat-value fw-bold d-flex align-items-center gap-1">
                            ${flag} ${geoStats.top_country.country}
                        </div>
                        <div class="geo-stat-subtitle text-muted small">${geoStats.top_country.count.toLocaleString('fr-FR')} visites</div>
                    </div>
                </div>
            `;
        }

        statsContainer.innerHTML = `
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="geo-stat-item p-2">
                        <div class="geo-stat-label text-muted small">Villes distinctes</div>
                        <div class="geo-stat-value fw-bold">${geoStats.total_cities}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="geo-stat-item p-2">
                        <div class="geo-stat-label text-muted small">Pays distincts</div>
                        <div class="geo-stat-value fw-bold">${geoStats.total_countries}</div>
                    </div>
                </div>
                ${topCountryInfo}
                <div class="col-6 col-md-3">
                    <div class="geo-stat-item p-2">
                        <div class="geo-stat-label text-muted small">Localisations inconnues</div>
                        <div class="geo-stat-value fw-bold">${geoStats.unknown_locations}</div>
                    </div>
                </div>
            </div>
        `;
    }

    updateCitiesTable(cities) {
        const tableContainer = document.getElementById('citiesTable');
        if (!tableContainer) return;

        let tableHtml = `
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th class="text-muted">#</th>
                            <th class="text-muted">Ville</th>
                            <th class="text-muted">Pays</th>
                            <th class="text-muted text-end">Sessions</th>
                            <th class="text-muted text-end">Part</th>
                            <th class="text-muted text-end">Visiteurs</th>
                            <th class="text-muted text-end">Durée</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        cities.forEach((city, index) => {
            const flag = city.flag || (city.country_code ? this.getCountryFlagFromCode(city.country_code) : '🏳️');
            
            tableHtml += `
                <tr class="align-middle">
                    <td class="text-muted">${index + 1}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            <span class="fw-medium">${city.city}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            ${flag}
                            <span>${city.country}</span>
                        </div>
                    </td>
                    <td class="text-end fw-bold">${(city.count || city.sessions || 0).toLocaleString('fr-FR')}</td>
                    <td class="text-end text-muted">${city.percentage || '0'}%</td>
                    <td class="text-end">${city.unique_visitors ? city.unique_visitors.toLocaleString('fr-FR') : '-'}</td>
                    <td class="text-end text-muted small">${city.avg_duration || '-'}</td>
                </tr>
            `;
        });

        tableHtml += `
                    </tbody>
                </table>
            </div>
        `;

        tableContainer.innerHTML = tableHtml;
    }

    getCountryFlagFromCode(code) {
        if (!code || code.length !== 2) return '🏳️';
        const codePoints = code.toUpperCase().split('').map(char => 127397 + char.charCodeAt(0));
        return String.fromCodePoint(...codePoints);
    }

    async loadGeographicData(forceRefresh = false) {
        if (this.geographicManager) {
            await this.geographicManager.loadGeographicData(forceRefresh);
        }
    }

    // Méthodes d'actualisation
    async refreshTrafficChart() {
        await this.loadTrafficChart(true);
        this.showNotification('Graphique de trafic actualisé', 'success');
    }

    async refreshSourceData() {
        await this.loadSourcesData(true);
        this.showNotification('Données des sources actualisées', 'success');
    }

    async refreshGeographicData() {
        if (this.geographicManager) {
            await this.geographicManager.refreshGeographicData();
        }
    }

    async refreshAllData() {
        this.dataCache.clear();
        await this.loadAllData();
        this.showNotification('Toutes les données actualisées', 'success');
    }

    // Gestion du plein écran
    toggleFullscreen(chartCard) {
        if (!chartCard) return;
        
        const isFullscreen = chartCard.classList.contains('fullscreen');
        const fullscreenBtn = chartCard.querySelector('[data-tooltip="Plein écran"]');
        
        if (!isFullscreen) {
            chartCard.classList.add('fullscreen');
            chartCard.style.position = 'fixed';
            chartCard.style.top = '0';
            chartCard.style.left = '0';
            chartCard.style.width = '100vw';
            chartCard.style.height = '100vh';
            chartCard.style.zIndex = '9999';
            chartCard.style.borderRadius = '0';
            chartCard.style.padding = '20px';
            chartCard.style.background = 'white';
            chartCard.style.overflow = 'auto';
            chartCard.style.boxShadow = '0 0 0 100vmax rgba(0,0,0,0.5)';
            
            if (fullscreenBtn) {
                fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                fullscreenBtn.setAttribute('data-tooltip', 'Quitter plein écran');
            }
            
            // Redessiner les graphiques
            setTimeout(() => {
                Object.values(this.charts).forEach(chart => {
                    if (chart) chart.resize();
                });
                if (this.geographicManager && this.geographicManager.map) {
                    this.geographicManager.map.invalidateSize();
                }
            }, 100);
            
            // Écouter la touche Échap
            document.addEventListener('keydown', this.handleEscapeKey);
        } else {
            chartCard.classList.remove('fullscreen');
            chartCard.style.cssText = '';
            
            if (fullscreenBtn) {
                fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                fullscreenBtn.setAttribute('data-tooltip', 'Plein écran');
            }
            
            document.removeEventListener('keydown', this.handleEscapeKey);
            
            // Redessiner les graphiques
            setTimeout(() => {
                Object.values(this.charts).forEach(chart => {
                    if (chart) chart.resize();
                });
                if (this.geographicManager && this.geographicManager.map) {
                    this.geographicManager.map.invalidateSize();
                }
            }, 100);
        }
    }

    handleEscapeKey = (e) => {
        if (e.key === 'Escape') {
            const fullscreenCard = document.querySelector('.chart-card.fullscreen');
            if (fullscreenCard) {
                this.toggleFullscreen(fullscreenCard);
            }
        }
    }

    // Téléchargement des données
    async downloadChartData(chartCard) {
        const chartTitle = chartCard.querySelector('.chart-title')?.textContent || 'donnees';
        const canvas = chartCard.querySelector('canvas');
        
        if (canvas) {
            // Télécharger l'image
            const link = document.createElement('a');
            link.download = `${chartTitle.toLowerCase().replace(/\s+/g, '-')}-${new Date().toISOString().split('T')[0]}.png`;
            link.href = canvas.toDataURL('image/png', 1.0);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            this.showNotification(`Graphique "${chartTitle}" téléchargé`, 'success');
        } else {
            // Pour les cartes sans canvas, essayer de télécharger les données
            try {
                let dataUrl;
                const chartId = chartCard.querySelector('canvas')?.id;
                
                switch(chartId) {
                    case 'trafficChart':
                        dataUrl = await this.exportTrafficData();
                        break;
                    case 'sourceChart':
                        dataUrl = await this.exportSourceData();
                        break;
                    case 'destinationsChart':
                        dataUrl = await this.exportCitiesData();
                        break;
                    default:
                        throw new Error('Données non disponibles');
                }
                
                const link = document.createElement('a');
                link.download = `${chartTitle.toLowerCase().replace(/\s+/g, '-')}-${new Date().toISOString().split('T')[0]}.json`;
                link.href = dataUrl;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                this.showNotification(`Données "${chartTitle}" téléchargées`, 'success');
            } catch (error) {
                this.showNotification('Impossible de télécharger les données', 'error');
            }
        }
    }

    async exportTrafficData() {
        const response = await fetch(`/api/dashboard/traffic-chart?period=${this.currentPeriod}`);
        const data = await response.json();
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        return URL.createObjectURL(blob);
    }

    async exportSourceData() {
        const response = await fetch(`/api/dashboard/sources?period=${this.currentPeriod}`);
        const data = await response.json();
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        return URL.createObjectURL(blob);
    }

    async exportCitiesData() {
        const response = await fetch(`/api/dashboard/top-cities?period=${this.currentPeriod}`);
        const data = await response.json();
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        return URL.createObjectURL(blob);
    }

    // Gestion des tables
    filterTable(input) {
        const searchTerm = input.value.toLowerCase().trim();
        const table = input.closest('.table-card').querySelector('tbody');
        const rows = table.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });
        
        // Mettre à jour le compteur
        const counter = input.parentNode.querySelector('.search-counter');
        if (counter) {
            counter.textContent = `${visibleCount}/${rows.length}`;
            counter.style.display = 'inline-block';
        }
    }

    // Gestion du rafraîchissement automatique
    startAutoRefresh(interval = 300000) { // 5 minutes par défaut
        this.autoRefreshInterval = setInterval(() => {
            if (document.visibilityState === 'visible') {
                this.refreshAllData();
            }
        }, interval);
    }

    pauseAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    }

    resumeAutoRefresh() {
        if (!this.autoRefreshInterval) {
            this.startAutoRefresh();
        }
    }

    handleResize() {
        // Redimensionner tous les graphiques
        Object.values(this.charts).forEach(chart => {
            if (chart) chart.resize();
        });
        
        // Redimensionner la carte si elle existe
        if (this.geographicManager && this.geographicManager.map) {
            setTimeout(() => this.geographicManager.map.invalidateSize(), 100);
        }
    }

    // Méthodes utilitaires
    getPeriodFromText(text) {
        const periodMap = {
            'Aujourd\'hui': 'today',
            'Semaine': 'week',
            'Mois': 'month',
            'Trimestre': 'quarter',
            'Année': 'year'
        };
        return periodMap[text.trim()] || 'month';
    }

    getPeriodName(periodKey) {
        const periods = {
            'today': "Aujourd'hui",
            'week': 'Semaine',
            'month': 'Mois',
            'quarter': 'Trimestre',
            'year': 'Année'
        };
        return periods[periodKey] || 'Mois';
    }

    getSourceColors(count) {
        const baseColors = [
            '#FF6B35', // Organique
            '#004E89', // Direct
            '#06D6A0', // Réseaux sociaux
            '#FFB627', // Payant
            '#667eea',  // Email
            '#764ba2',  // Référence
            '#FF4081',  // Autre 1
            '#9C27B0',  // Autre 2
            '#3F51B5',  // Autre 3
            '#009688'   // Autre 4
        ];
        
        if (count <= baseColors.length) {
            return baseColors.slice(0, count);
        }
        
        const colors = [...baseColors];
        for (let i = baseColors.length; i < count; i++) {
            const hue = (i * 137.508) % 360; // Angle d'or
            colors.push(`hsl(${hue}, 70%, 60%)`);
        }
        
        return colors;
    }

    getSourceColor(index) {
        const colors = this.getSourceColors(10);
        return colors[index % colors.length];
    }

    getSourceIcon(sourceName) {
        const iconMap = {
            'Organique': 'fa-search',
            'SEO': 'fa-search',
            'Direct': 'fa-link',
            'Réseaux Sociaux': 'fa-share-alt',
            'Social': 'fa-share-alt',
            'Payant (CPC)': 'fa-ad',
            'Ads': 'fa-ad',
            'Email': 'fa-envelope',
            'Referral': 'fa-external-link-alt',
            'Autre': 'fa-circle',
            'Référence': 'fa-external-link-alt',
            'Referral': 'fa-external-link-alt'
        };
        
        return iconMap[sourceName] || 'fa-circle';
    }

    // Méthodes d'affichage d'état
    showGlobalLoading(show) {
        const overlay = document.getElementById('globalLoading');
        if (overlay) {
            overlay.style.display = show ? 'flex' : 'none';
        }
        
        // Désactiver les boutons pendant le chargement
        document.querySelectorAll('.period-btn, .chart-btn, .refresh-btn').forEach(btn => {
            btn.disabled = show;
            if (show) {
                btn.classList.add('disabled');
            } else {
                btn.classList.remove('disabled');
            }
        });
    }

    showKPIloading(show) {
        const kpiValues = document.querySelectorAll('.kpi-value');
        kpiValues.forEach(el => {
            if (show) {
                el.classList.add('loading');
                el.textContent = 'Chargement...';
            } else {
                el.classList.remove('loading');
            }
        });
    }

    showKPIError() {
        const kpiValues = document.querySelectorAll('.kpi-value');
        kpiValues.forEach(el => {
            el.classList.remove('loading');
            el.classList.add('error');
            el.textContent = 'Erreur';
        });
    }

    showSourceLoading(show) {
        const sourceLoading = document.querySelector('#sourceChart')?.closest('.chart-container')?.querySelector('.chart-loading');
        const canalData = document.getElementById('canalData');
        
        if (sourceLoading) {
            sourceLoading.style.display = show ? 'flex' : 'none';
        }
        
        if (canalData && show) {
            canalData.innerHTML = `
                <div class="loading-placeholder text-center py-5">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="text-muted mt-2">Chargement des données...</p>
                </div>
            `;
        }
    }

    showSourceError() {
        const canalData = document.getElementById('canalData');
        if (canalData) {
            canalData.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                    <p class="text-warning">Erreur lors du chargement des données</p>
                    <button class="btn btn-primary btn-sm mt-2" onclick="dashboard.refreshSourceData()">
                        <i class="fas fa-redo me-1"></i> Réessayer
                    </button>
                </div>
            `;
        }
    }

    showChartError(canvas, message) {
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        ctx.clearRect(0, 0, width, height);
        ctx.fillStyle = '#f8f9fa';
        ctx.fillRect(0, 0, width, height);
        
        ctx.fillStyle = '#6c757d';
        ctx.font = '16px "Segoe UI", Tahoma, Geneva, Verdana, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(message, width / 2, height / 2);
    }

    showNotification(message, type = 'info') {
        // Créer le conteneur s'il n'existe pas
        let container = document.getElementById('notificationContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notificationContainer';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 99999;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
        
        // Déterminer les classes Bootstrap
        const alertClasses = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        const iconClasses = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };
        
        // Créer la notification
        const notificationId = 'notification-' + Date.now();
        const notification = document.createElement('div');
        notification.id = notificationId;
        notification.className = `alert ${alertClasses[type] || 'alert-info'} alert-dismissible fade show shadow-sm`;
        notification.role = 'alert';
        notification.style.cssText = `
            margin-bottom: 10px;
            animation: slideInRight 0.3s ease;
            max-width: 100%;
        `;
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${iconClasses[type] || 'fa-info-circle'} me-2"></i>
                <span class="flex-grow-1">${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="document.getElementById('${notificationId}').remove()"></button>
            </div>
        `;
        
        container.appendChild(notification);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            if (document.getElementById(notificationId)) {
                const alert = document.getElementById(notificationId);
                alert.classList.remove('show');
                setTimeout(() => {
                    if (document.getElementById(notificationId)) {
                        document.getElementById(notificationId).remove();
                    }
                }, 150);
            }
        }, 5000);
    }

    // Méthodes de débogage
    logPerformance() {
        console.log('📊 Performance du Dashboard:');
        console.log(`- Cache size: ${this.dataCache.size}`);
        console.log(`- Charts loaded: ${Object.values(this.charts).filter(c => c).length}`);
        console.log(`- Current period: ${this.currentPeriod}`);
        console.log(`- Is loading: ${this.isLoading}`);
        console.log(`- Geographic manager: ${this.geographicManager ? 'Loaded' : 'Not loaded'}`);
    }

    // Nettoyage
    destroy() {
        this.pauseAutoRefresh();
        
        // Détruire tous les graphiques
        Object.values(this.charts).forEach(chart => {
            if (chart) chart.destroy();
        });
        
        // Détruire le gestionnaire géographique
        if (this.geographicManager) {
            this.geographicManager.destroy();
            this.geographicManager = null;
        }
        
        // Nettoyer les écouteurs d'événements
        document.removeEventListener('keydown', this.handleEscapeKey);
        document.removeEventListener('visibilitychange', () => {});
        window.removeEventListener('resize', () => {});
        
        // Vider le cache
        this.dataCache.clear();
        
        console.log('🧹 DashboardManager nettoyé');
    }
}

/**
 * GeographicManager - Gestionnaire des données géographiques
 * Gère la carte interactive et les données géographiques
 */
class GeographicManager {
    constructor(dashboard) {
        this.dashboard = dashboard;
        this.map = null;
        this.markersLayer = null;
        this.heatLayer = null;
        this.legend = null;
        this.currentPeriod = 'month';
        this.dataCache = new Map();
        this.isLoading = false;
        this.initialize();
    }

    initialize() {
        this.setupEventListeners();
        this.initMap();
        this.loadGeographicData();
    }

    setupEventListeners() {
        document.addEventListener('periodChanged', (e) => {
            this.currentPeriod = e.detail.period;
            this.loadGeographicData();
        });
    }

    initMap() {
        const mapElement = document.getElementById('visitorMap');
        if (!mapElement) return;
        
        this.map = L.map('visitorMap').setView([8, -5], 4);
        
        // Ajouter plusieurs tuiles de carte pour la redondance
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributeurs',
            maxZoom: 19,
            minZoom: 2
        }).addTo(this.map);
        
        // Optionnel: Ajouter une couche satellite
        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles © Esri',
            maxZoom: 19
        });
        
        // Contrôle des couches
        const baseLayers = {
            "Carte Standard": osmLayer,
            "Vue Satellite": satelliteLayer
        };
        
        L.control.layers(baseLayers).addTo(this.map);
        
        // Créer les couches de marqueurs
        this.markersLayer = L.layerGroup().addTo(this.map);
        this.heatLayer = L.layerGroup().addTo(this.map);
        
        // Ajouter les contrôles
        L.control.scale({ imperial: false }).addTo(this.map);
        L.control.zoom({ position: 'topright' }).addTo(this.map);
        
        // Créer la légende
        this.createLegend();
        
        // Gérer le redimensionnement
        window.addEventListener('resize', () => {
            setTimeout(() => {
                if (this.map) this.map.invalidateSize();
            }, 100);
        });
    }

    createLegend() {
        this.legend = L.control({ position: 'bottomright' });
        
        this.legend.onAdd = () => {
            const div = L.DomUtil.create('div', 'leaflet-control leaflet-control-legend bg-white rounded shadow-sm p-3');
            div.style.cssText = `
                max-width: 200px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            `;
            
            div.innerHTML = `
                <div class="legend-title fw-bold mb-2 text-dark">Intensité des visites</div>
                <div class="legend-scale d-flex align-items-center mb-2">
                    <div class="legend-color rounded-circle me-2" style="width: 16px; height: 16px; background: #34bf49;"></div>
                    <span class="legend-label small">Faible (0-33%)</span>
                </div>
                <div class="legend-scale d-flex align-items-center mb-2">
                    <div class="legend-color rounded-circle me-2" style="width: 16px; height: 16px; background: #ffd700;"></div>
                    <span class="legend-label small">Moyenne (34-66%)</span>
                </div>
                <div class="legend-scale d-flex align-items-center">
                    <div class="legend-color rounded-circle me-2" style="width: 16px; height: 16px; background: #ff6b35;"></div>
                    <span class="legend-label small">Forte (67-100%)</span>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <small class="text-muted">Taille = Nombre de visites</small>
                </div>
            `;
            
            // Empêcher les événements de la carte
            L.DomEvent.disableClickPropagation(div);
            L.DomEvent.disableScrollPropagation(div);
            
            return div;
        };
        
        this.legend.addTo(this.map);
    }

    toggleLegend() {
        const legend = document.querySelector('.leaflet-control-legend');
        if (legend) {
            legend.style.display = legend.style.display === 'none' ? 'block' : 'none';
        }
    }

    async loadGeographicData(forceRefresh = false) {
        if (this.isLoading) return;
        
        const cacheKey = `geographic-${this.currentPeriod}`;
        
        // Vérifier le cache
        if (!forceRefresh && this.dataCache.has(cacheKey)) {
            const data = this.dataCache.get(cacheKey);
            this.renderGeographicData(data);
            return;
        }
        
        this.isLoading = true;
        this.showLoading(true);
        
        try {
            const response = await fetch(`/api/dashboard/geographic?period=${this.currentPeriod}`);
            
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            
            this.dataCache.set(cacheKey, data);
            this.renderGeographicData(data);
            
        } catch (error) {
            console.error('Erreur données géographiques:', error);
            this.showError();
            this.dashboard.showNotification('Erreur lors du chargement des données géographiques', 'error');
        } finally {
            this.isLoading = false;
            this.showLoading(false);
        }
    }

    refreshGeographicData() {
        this.dataCache.delete(`geographic-${this.currentPeriod}`);
        this.loadGeographicData(true);
        this.dashboard.showNotification('Données géographiques actualisées', 'success');
    }

    renderGeographicData(data) {
        // Mettre à jour les titres
        this.updateTitles(data.period);
        
        // Rendre la carte
        this.renderMap(data);
        
        // Rendre la liste des pays
        this.renderCountriesList(data);
        
        // Rendre la répartition par continent
        this.renderContinentsList(data);
        
        // Mettre à jour les statistiques globales
        this.updateGlobalStats(data);
        
        // Mettre à jour les statistiques Afrique
        this.updateAfricaStats(data);
    }

    updateTitles(period) {
        const periodNames = {
            'today': "Aujourd'hui",
            'week': 'Cette Semaine',
            'month': 'Ce Mois',
            'quarter': 'Ce Trimestre',
            'year': 'Cette Année'
        };
        
        const periodName = periodNames[period] || period;
        
        // Titre de la carte
        const mapTitle = document.getElementById('mapTitle');
        if (mapTitle) {
            mapTitle.textContent = `Carte Géographique - ${periodName}`;
        }
        
        // Titre des pays
        const countryTitle = document.getElementById('countryTitle');
        if (countryTitle) {
            countryTitle.textContent = `Top 10 Pays - ${periodName}`;
        }
    }

    renderMap(data) {
        // Nettoyer les anciennes données
        if (this.markersLayer) this.markersLayer.clearLayers();
        if (this.heatLayer) this.heatLayer.clearLayers();
        
        const cities = data.cities || [];
        if (cities.length === 0) {
            // Afficher un message si aucune donnée
            this.showNoDataMessage();
            return;
        }
        
        // Calculer les valeurs max/min pour l'échelle
        const counts = cities.map(c => c.count).filter(count => count > 0);
        const maxVisits = counts.length > 0 ? Math.max(...counts) : 1;
        const minVisits = counts.length > 0 ? Math.min(...counts) : 1;
        
        // Créer un cluster pour les marqueurs
        const markers = [];
        
        // Ajouter les marqueurs pour chaque ville
        cities.forEach((city) => {
            if (!city.latitude || !city.longitude) return;
            
            // Calculer la taille et la couleur basée sur le nombre de visites
            const normalizedValue = maxVisits > minVisits ? 
                (city.count - minVisits) / (maxVisits - minVisits) : 0.5;
            
            const radius = 8 + (normalizedValue * 24); // Entre 8 et 32 pixels
            const color = this.getHeatColor(normalizedValue);
            const intensity = Math.round(normalizedValue * 100);
            
            // Créer le marqueur circulaire
            const marker = L.circleMarker([city.latitude, city.longitude], {
                radius: radius,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 0.8,
                fillOpacity: 0.7,
                className: 'city-marker'
            });
            
            // Info-bulle détaillée
            const popupContent = `
                <div class="map-popup p-3 w-100">
                    <div class="popup-header border-bottom pb-2 mb-2">
                        <div class="popup-city fw-bold fs-6 text-dark">${city.city}</div>
                        <div class="popup-country text-muted small">${city.country}</div>
                    </div>
                    <div class="popup-stats">
                        <div class="popup-stat d-flex justify-content-between mb-1">
                            <span class="popup-label text-muted">Visites</span>
                            <span class="popup-value fw-bold">${city.count.toLocaleString('fr-FR')}</span>
                        </div>
                        <div class="popup-stat d-flex justify-content-between mb-1">
                            <span class="popup-label text-muted">Part mondiale</span>
                            <span class="popup-value fw-bold ${intensity > 66 ? 'text-danger' : intensity > 33 ? 'text-warning' : 'text-success'}">
                                ${city.percentage}%
                            </span>
                        </div>
                        <div class="popup-stat d-flex justify-content-between mb-1">
                            <span class="popup-label text-muted">Intensité</span>
                            <span class="popup-value">
                                <div class="progress" style="width: 60px; height: 6px;">
                                    <div class="progress-bar ${intensity > 66 ? 'bg-danger' : intensity > 33 ? 'bg-warning' : 'bg-success'}" 
                                         style="width: ${intensity}%"></div>
                                </div>
                                <small class="text-muted ms-1">${intensity}%</small>
                            </span>
                        </div>
                    </div>
                    <div class="popup-coords text-center mt-2 pt-2 border-top small text-muted">
                        ${city.latitude.toFixed(4)}°, ${city.longitude.toFixed(4)}°
                    </div>
                </div>
            `;
            
            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            });
            
            // Effets au survol
            marker.on('mouseover', function(e) {
                this.openPopup();
                this.setStyle({
                    fillOpacity: 0.9,
                    weight: 3,
                    color: '#fff'
                });
            });
            
            marker.on('mouseout', function(e) {
                this.closePopup();
                this.setStyle({
                    fillOpacity: 0.7,
                    weight: 2,
                    color: '#fff'
                });
            });
            
            marker.on('click', function(e) {
                // Zoom sur la ville au clic
                if (this._map) {
                    this._map.setView([city.latitude, city.longitude], 8);
                }
            });
            
            markers.push(marker);
        });
        
        // Ajouter tous les marqueurs à la couche
        markers.forEach(marker => marker.addTo(this.markersLayer));
        
        // Ajuster la vue de la carte pour inclure tous les marqueurs
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            this.map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    getHeatColor(intensity) {
        // Dégradé de couleur du vert au rouge via jaune
        if (intensity < 0.33) {
            // Vert clair à vert moyen
            return `rgb(${Math.round(52 + intensity * 100)}, ${Math.round(191 - intensity * 80)}, ${Math.round(73 - intensity * 30)})`;
        } else if (intensity < 0.66) {
            // Jaune à orange
            return `rgb(${Math.round(255 - (intensity - 0.33) * 100)}, ${Math.round(215 - (intensity - 0.33) * 50)}, ${Math.round(0 + (intensity - 0.33) * 50)})`;
        } else {
            // Orange à rouge
            return `rgb(${Math.round(255 - (intensity - 0.66) * 35)}, ${Math.round(107 + (intensity - 0.66) * 50)}, ${Math.round(53 - (intensity - 0.66) * 20)})`;
        }
    }

    showNoDataMessage() {
        if (this.markersLayer) {
            const center = this.map.getCenter();
            const message = L.marker(center, {
                icon: L.divIcon({
                    className: 'no-data-marker',
                    html: `
                        <div class="text-center p-3 bg-white rounded shadow-sm border">
                            <i class="fas fa-globe text-muted fs-1 mb-2"></i>
                            <p class="text-muted mb-0">Aucune donnée géographique disponible</p>
                            <small class="text-muted">Essayez une autre période</small>
                        </div>
                    `,
                    iconSize: [200, 100],
                    iconAnchor: [100, 50]
                })
            }).addTo(this.markersLayer);
        }
    }

    renderCountriesList(data) {
        const container = document.getElementById('countryList');
        if (!container) return;
        
        const countries = data.countries || [];
        
        if (countries.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-globe-africa text-muted fs-1 mb-3"></i>
                    <p class="text-muted">Aucune donnée de pays disponible</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        countries.forEach((country, index) => {
            const rankClass = index < 3 ? 'top-3' : '';
            const medalIcon = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : '';
            
            html += `
                <div class="country-item border rounded p-3 mb-2 bg-light-hover">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="country-info d-flex align-items-center">
                            <span class="country-rank ${rankClass} fw-bold me-2 ${index < 3 ? 'fs-5' : 'text-muted'}">
                                ${index < 3 ? medalIcon : index + 1}
                            </span>
                            <span class="country-flag fs-5 me-2">${country.flag}</span>
                            <div>
                                <div class="country-name fw-medium">${country.country}</div>
                                <small class="text-muted">${country.country_code || ''}</small>
                            </div>
                        </div>
                        <div class="country-stats text-end">
                            <div class="country-count fw-bold">${country.count.toLocaleString('fr-FR')}</div>
                            <div class="country-percentage small ${country.percentage > 10 ? 'text-primary fw-bold' : 'text-muted'}">
                                ${country.percentage}% mondial
                            </div>
                        </div>
                    </div>
                    ${index < countries.length - 1 ? '<hr class="my-2">' : ''}
                </div>
            `;
        });
        
        container.innerHTML = html;
    }

    renderContinentsList(data) {
        const container = document.getElementById('continentsList');
        if (!container) return;
        
        const continents = data.continents || {};
        const totalVisits = data.statistics?.total_visits || 1;
        
        if (Object.keys(continents).length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-globe-americas text-muted mb-2"></i>
                    <p class="text-muted small">Aucune donnée de continent</p>
                </div>
            `;
            return;
        }
        
        // Trier les continents par pourcentage décroissant
        const sortedContinents = Object.entries(continents)
            .map(([key, continent]) => ({ key, ...continent }))
            .sort((a, b) => b.percentage - a.percentage);
        
        let html = '';
        
        sortedContinents.forEach(continent => {
            const percentage = continent.percentage || ((continent.visits / totalVisits) * 100).toFixed(1);
            const progressWidth = Math.min(percentage, 100);
            
            html += `
                <div class="continent-item mb-3">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <div class="continent-info d-flex align-items-center ">
                            <div class="continent-icon rounded-circle d-flex align-items-center justify-content-center me-2" 
                                 style="width: 32px; height: 32px; background: ${continent.color}; color: white;">
                                <i class="fas ${continent.icon}"></i>
                            </div>
                            <span class="continent-name fw-medium">${continent.name}</span>
                        </div>
                        <div class="continent-stats text-end">
                            <div class="continent-percentage fw-bold" style="color: ${continent.color}">
                                ${percentage}%
                            </div>
                            <div class="continent-visits small text-muted">
                                ${continent.visits.toLocaleString('fr-FR')} visites
                            </div>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" 
                             style="width: ${progressWidth}%; background: ${continent.color};"></div>
                    </div>
                </div>
            `;
        });
        
        // Ajouter un résumé
        const totalPercentage = sortedContinents.reduce((sum, c) => sum + (c.percentage || 0), 0);
        
        html += `
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Total couvert</span>
                    <span class="fw-bold ${totalPercentage >= 95 ? 'text-success' : totalPercentage >= 80 ? 'text-warning' : 'text-danger'}">
                        ${totalPercentage.toFixed(1)}%
                    </span>
                </div>
                <div class="progress mt-1" style="height: 4px;">
                    ${sortedContinents.map(continent => `
                        <div class="progress-bar" 
                             style="width: ${continent.percentage || 0}%; background: ${continent.color};">
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        container.innerHTML = html;
    }

    updateGlobalStats(data) {
        const overlay = document.getElementById('globalStatsOverlay');
        if (!overlay) return;
        
        const topCountry = data.statistics?.global_distribution?.top_country;
        const topCity = data.statistics?.global_distribution?.top_city;
        const africaPercentage = data.continents?.africa?.percentage || 0;
        const topCountriesPercentage = data.statistics?.global_distribution?.top_10_countries_percentage || 0;
        const totalVisits = data.statistics?.total_visits || 0;
        
        overlay.innerHTML = `
            <div class="global-stats p-3 bg-white rounded shadow-sm border">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="global-stat-item text-center p-2">
                            <div class="global-stat-label text-muted small">
                                <i class="fas fa-globe me-1"></i> Couverture
                            </div>
                            <div class="global-stat-value fw-bold fs-5 ${topCountriesPercentage > 80 ? 'text-success' : topCountriesPercentage > 50 ? 'text-warning' : 'text-danger'}">
                                ${topCountriesPercentage}%
                            </div>
                        </div>
                    </div>
                    ${topCountry ? `
                    <div class="col-6">
                        <div class="global-stat-item text-center p-2">
                            <div class="global-stat-label text-muted small">
                                ${topCountry.flag} ${topCountry.name}
                            </div>
                            <div class="global-stat-value fw-bold fs-5">
                                ${topCountry.percentage}%
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    <div class="col-6">
                        <div class="global-stat-item text-center p-2">
                            <div class="global-stat-label text-muted small">
                                <i class="fas fa-globe-africa me-1"></i> Afrique
                            </div>
                            <div class="global-stat-value fw-bold fs-5 ${africaPercentage > 50 ? 'text-success' : africaPercentage > 20 ? 'text-warning' : 'text-danger'}">
                                ${africaPercentage}%
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="global-stat-item text-center p-2">
                            <div class="global-stat-label text-muted small">
                                <i class="fas fa-users me-1"></i> Total
                            </div>
                            <div class="global-stat-value fw-bold fs-5">
                                ${totalVisits.toLocaleString('fr-FR')}
                            </div>
                        </div>
                    </div>
                </div>
                ${topCity ? `
                <div class="mt-2 pt-2 border-top text-center">
                    <small class="text-muted">
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                        Ville principale: ${topCity.name} (${topCity.percentage}%)
                    </small>
                </div>
                ` : ''}
            </div>
        `;
    }

    updateAfricaStats(data) {
        const container = document.getElementById('africaStats');
        if (!container || !data.statistics?.africa_distribution) return;
        
        const africaData = data.statistics.africa_distribution;
        
        let html = `
            <div class="africa-stats">
                <div class="row g-3">
        `;
        
        // Régions d'Afrique
        const regions = ['west', 'north', 'central', 'east', 'south'];
        const regionNames = {
            'west': 'Afrique de l\'Ouest',
            'north': 'Afrique du Nord',
            'central': 'Afrique Centrale',
            'east': 'Afrique de l\'Est',
            'south': 'Afrique Australe'
        };
        
        regions.forEach(region => {
            if (africaData[`${region}_africa`]) {
                const regionData = africaData[`${region}_africa`];
                const percentage = regionData.percentage || 0;
                
                html += `
                    <div class="col-6 col-md-4">
                        <div class="region-card p-2 border rounded bg-light">
                            <div class="region-name fw-medium small mb-1">${regionNames[region]}</div>
                            <div class="region-percentage fw-bold ${percentage > 20 ? 'text-success' : percentage > 5 ? 'text-warning' : 'text-danger'}">
                                ${percentage}%
                            </div>
                            ${regionData.countries && regionData.countries.length > 0 ? `
                                <div class="region-countries mt-1">
                                    ${regionData.countries.map(country => `
                                        <span class="badge bg-secondary bg-opacity-25 text-dark me-1 mb-1">${country.country}</span>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }
        });
        
        html += `
                </div>
                <div class="mt-3 pt-2 border-top text-center">
                    <small class="text-muted">
                        Total Afrique: ${africaData.total_percentage || 0}%
                    </small>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
    }

    showLoading(show) {
        const mapLoading = document.querySelector('.map-loading');
        const countryList = document.getElementById('countryList');
        const continentsList = document.getElementById('continentsList');
        
        if (mapLoading) {
            mapLoading.style.display = show ? 'flex' : 'none';
        }
        
        if (countryList && show) {
            countryList.innerHTML = `
                <div class="loading-placeholder text-center py-5">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="text-muted mt-2 small">Chargement des données géographiques...</p>
                </div>
            `;
        }
        
        if (continentsList && show) {
            continentsList.innerHTML = `
                <div class="loading-placeholder text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="text-muted mt-2 small">Chargement...</p>
                </div>
            `;
        }
    }

    showError() {
        const countryList = document.getElementById('countryList');
        const continentsList = document.getElementById('continentsList');
        const mapContainer = document.getElementById('visitorMap');
        
        if (countryList) {
            countryList.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                    <p class="text-warning">Erreur lors du chargement des données</p>
                    <button class="btn btn-primary btn-sm mt-2" onclick="dashboard.geographicManager.refreshGeographicData()">
                        <i class="fas fa-redo me-1"></i> Réessayer
                    </button>
                </div>
            `;
        }
        
        if (continentsList) {
            continentsList.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-circle text-warning me-1"></i>
                    <span class="text-warning small">Données indisponibles</span>
                </div>
            `;
        }
        
        if (mapContainer && this.map) {
            // Nettoyer la carte
            this.markersLayer.clearLayers();
            
            // Afficher un message d'erreur sur la carte
            const center = this.map.getCenter();
            const errorMarker = L.marker(center, {
                icon: L.divIcon({
                    className: 'error-marker',
                    html: `
                        <div class="text-center p-3 bg-white rounded shadow border border-danger">
                            <i class="fas fa-exclamation-triangle text-danger fs-2 mb-2"></i>
                            <p class="text-danger mb-1">Erreur de chargement</p>
                            <button class="btn btn-danger btn-sm mt-1" onclick="dashboard.geographicManager.refreshGeographicData()">
                                Réessayer
                            </button>
                        </div>
                    `,
                    iconSize: [200, 120],
                    iconAnchor: [100, 60]
                })
            }).addTo(this.markersLayer);
        }
    }

    // Méthodes utilitaires
    formatNumber(num) {
        if (typeof num !== 'number') return '0';
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'k';
        return new Intl.NumberFormat('fr-FR').format(num);
    }

    destroy() {
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
        
        this.markersLayer = null;
        this.heatLayer = null;
        this.legend = null;
        
        // Nettoyer les écouteurs d'événements
        document.removeEventListener('periodChanged', () => {});
        window.removeEventListener('resize', () => {});
        
        console.log('🗺️ GeographicManager nettoyé');
    }
}

// Initialisation globale
document.addEventListener('DOMContentLoaded', () => {
    // Vérifier que Chart.js est disponible
    if (typeof Chart === 'undefined') {
        console.error('Chart.js n\'est pas chargé');
        return;
    }
    
    // Vérifier que Leaflet est disponible
    if (typeof L === 'undefined') {
        console.error('Leaflet n\'est pas chargé');
    }
    
    // Initialiser le dashboard
    window.dashboard = new DashboardManager();
    
    // Exposer globalement pour le débogage
    window.DashboardManager = DashboardManager;
    window.GeographicManager = GeographicManager;
    
    // Initialiser les tooltips Bootstrap
    initBootstrapTooltips();
    
    // Ajouter des styles CSS supplémentaires
    addCustomStyles();
});

// Fonction pour initialiser les tooltips Bootstrap
function initBootstrapTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover'
        });
    });
}

// Fonction pour ajouter des styles CSS personnalisés
function addCustomStyles() {
    const styles = `
        /* Styles pour les états de chargement */
        .kpi-value.loading {
            color: transparent;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loadingShimmer 1.5s infinite;
            border-radius: 4px;
            min-height: 2.5rem;
            display: inline-block;
            min-width: 80px;
        }*/
        
        .kpi-value.error {
            color: #dc3545 !important;
        }
        
        @keyframes loadingShimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        /*.chart-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: inherit;
        }*/
        
        /* Styles pour les tendances 
        .trend-up {
            color: #28a745;
        }
        
        .trend-down {
            color: #dc3545;
        }
        
        .trend-neutral {
            color: #6c757d;
        }
        
        .kpi-trend.bounce-rate.trend-up {
            color: #dc3545;
        }
        
        .kpi-trend.bounce-rate.trend-down {
            color: #28a745;
        }*/
        
        /* Styles pour les cartes */
        .kpi-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Styles pour les graphiques en plein écran */
        .chart-card.fullscreen {
            z-index: 9999 !important;
        }
        
        /* Styles pour les marqueurs Leaflet */
        .city-marker {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .city-marker:hover {
            filter: brightness(1.1);
        }
        
        /* Styles pour les popups Leaflet */
        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .custom-popup .leaflet-popup-content {
            margin: 0;
            padding: 0;
        }
        
        /* Animation pour les notifications */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .kpi-card {
                margin-bottom: 1rem;
            }
            
            .chart-card {
                margin-bottom: 1rem;
            }
            
            #visitorMap {
                height: 300px !important;
            }
        }
        
        /* Hover effects */
        .bg-light-hover:hover {
            background-color: #f8f9fa !important;
        }
        
        /* Country ranks */
        .country-rank.top-3 {
            background: linear-gradient(135deg, #ffd700, #c0c0c0, #cd7f32);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    `;
    
    const styleSheet = document.createElement('style');
    styleSheet.textContent = styles;
    document.head.appendChild(styleSheet);
}

// Fonction utilitaire pour formater les nombres
function formatNumber(num) {
    if (typeof num !== 'number') return '0';
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'k';
    return new Intl.NumberFormat('fr-FR').format(num);
}

// Fonction utilitaire pour formater les pourcentages
function formatPercentage(num, decimals = 1) {
    if (typeof num !== 'number') return '0%';
    return num.toFixed(decimals) + '%';
}

// Exporter pour utilisation dans d'autres fichiers
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { DashboardManager, GeographicManager, formatNumber, formatPercentage };
}