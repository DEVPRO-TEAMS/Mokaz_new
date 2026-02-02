<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NNNZRNVL');</script>
    <!-- End Google Tag Manager -->
    <meta charset="utf-8">
    <title>MOKAZ - Plateforme Immobilier</title>

    <meta name="author" content="creativeagency.web">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- font -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/font-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet"type="text/css" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/logo/favicon.png') }}">
    
    <style>
        /* Conteneur des champs de saisie pour placer l'icône */
        /* Applique le style aux éléments en lecture seule */
        input[readonly],
        textarea[readonly],
        select[readonly] {
            background-color: #f0f0f0;
            /* Couleur de fond gris pour les champs en readonly */
            border: 1px solid #ccc;
            /* Bordure gris clair */
            /* cursor: not-allowed;        Curseur indiquant que l'action est interdite */
            cursor: no-drop;
            pointer-events: none;
            /* Empêche toute interaction avec ces éléments */
        }

        /* Remplacer le curseur par l'emoji 🚫 lors du survol des champs readonly */
        input[readonly]:hover,
        textarea[readonly]:hover,
        select[readonly]:hover {
            cursor: no-drop;
            /* cursor: wait; */
        }
        .more-content.collapse:not(.show) {
            display: block !important;
            height: 0;
            overflow: hidden;
            position: relative;
        }

        .read-more-toggle {
            color: var(--primary-color);
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .read-more-toggle:hover {
            opacity: 0.8;
        }

        .read-more-toggle i {
            transition: transform 0.2s ease;
        }

        [aria-expanded="true"] .read-more-toggle i {
            transform: rotate(180deg);
        }

        .rating {
            direction: rtl;
            /* Permet de remplir les étoiles de droite à gauche */
            unicode-bidi: bidi-override;
            display: inline-flex;
        }

        .rating input {
            display: none;
        }

        .rating label {
            font-size: 3rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating input:checked~label,
        .rating label:hover,
        .rating label:hover~label {
            color: #ffc107;
            /* Jaune bootstrap */
        }

        .list-star-note {
            display: flex;
        }

        .list-star-note .icon-star {
            color: #ddd;
            font-size: 16px;
        }

        .pagination .page-item .page-link {
            color: #dc3545;
            /* Rouge Bootstrap */
            border-radius: 8px;
            margin: 0 4px;
            border: 1px solid #dc3545;
            transition: all 0.3s ease;
        }

        .pagination .page-item .page-link:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .pagination .page-item.active .page-link {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
            font-weight: bold;
        }
        .select2-container--default .select2-selection--single {
            /* border-radius: 8px; */
            border: none !important;
        }
        /* .select2-container--default .select2-selection--single {
            height: 38px !important;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        } */
    </style>
</head>

<body class="body counter-scroll">
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NNNZRNVL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    @php
        $reservations = App\Models\Reservation::all();
    @endphp

    {{-- @dd(Auth::user()->user_type) --}}

    <div class="preload preload-container">
        <div class="boxes ">
            <div class="box">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="box">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="box">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="box">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>

    <!-- /preload -->

    <div id="wrapper">
        <div id="pagee" class="clearfix">



            <!-- Main Header -->
            @include('layouts.header')
            <!-- End Main Header -->

            <div class="container-fluid" style="margin-top: 5%">
                @yield('content')
            </div>
            <!-- footer -->
            @include('layouts.footer')
            <!-- end footer -->
        </div>
        <!-- /#page -->

    </div>

    @include('components.wishlist')

    {{-- Gestion du menu static inteligent  --}}

    <script>
        let lastScrollTop = 0;
        const header = document.querySelector('.fixed-header');

        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > lastScrollTop) {
                // Scroll vers le bas → cacher le header
                header.style.top = "-100px";
            } else {
                // Scroll vers le haut → afficher le header
                header.style.top = "0";
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // pour éviter valeurs négatives
        });
    </script>


    <script>
        function toggleWishlistCart() {
            const cart = document.getElementById('wishlistCart');
            cart.classList.toggle('d-none');
        }
    </script>




    <!-- go top -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 286.138;">
            </path>
        </svg>
    </div>



    @include('components.loginModal')
    @include('components.getReservationModal')

    @include('partners.pages.demandPartnariaModal')



    <!-- JS -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}






    <!-- Javascript -->
    <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>
     <!-- JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('assets/js/owl.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/carousel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/plugin.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/rangle-slider.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/countto.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/shortcodes.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/animation_heading.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCFC3m2n0jBRFTMvUNZc0-6Y0Rzlcadzcw"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/map.js') }}"></script>
    <script src="{{ asset('assets/js/map-contact.js') }}"></script>
    <script src="{{ asset('assets/js/marker.js') }}"></script>
    <script src="{{ asset('assets/js/infobox.min.js') }}"></script>

    {{-- <script src=https://touchpay.gutouch.net/touchpayv2/script/touchpaynr/prod_touchpay-0.0.1.js  type="text/javascript"></script> --}}

    {{-- <script src=https://touchpay.gutouch.net/touchpayv2/script/touchpaynr/prod_touchpay-0.0.1.js type="text/javascript">
    </script> --}}
    <script src="{{ asset('assets/js/map-single.js') }}"></script>

    
    <script>
        $(document).ready(function() {
            $('.selection').select2({
                // placeholder: "Choisir",
                allowClear: true,
                width: '100%',
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr(".datetime", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today"
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let isJobRunning = false;

            function executeCronJob() {
                if (!isJobRunning) {
                    isJobRunning = true;
                    axios.post("/api/cron/autoRemiseReservation", {}, {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => {
                            console.log('Cron job executed successfully:', response.data.message);
                            console.log('Response:', response.data.details);
                        })
                        .catch(error => {
                            console.error("Erreur lors de l'exécution du cron :", error);
                        })
                        .finally(() => {
                            isJobRunning = false;
                        });
                } else {
                    console.log("La tâche cron est déjà en cours.");
                }
            }

            // Exécuter toutes les 60 secondes (1 minute)
            setInterval(executeCronJob, 60000);
        });
    </script>

    {{-- <script>
        const pageViewId = "{{ session('current_page_view_id') }}";

        window.addEventListener('beforeunload', function () {
            if (!pageViewId) return;

            const data = JSON.stringify({ page_view_id: pageViewId });

            navigator.sendBeacon(
                '/track/page-duration',
                new Blob([data], { type: 'application/json' })
            );
        });

    </script> --}}

    {{-- <script>
        const pageViewHistoriqueUuid = "{{ session('current_page_view_historique_uuid') }}";
        window.addEventListener('beforeunload', function () {
            if (!pageViewHistoriqueUuid) return;

            const data = new Blob(
                [JSON.stringify({ historique_uuid: pageViewHistoriqueUuid })],
                { type: 'application/json' }
            );

            navigator.sendBeacon('/track/page-duration', data);
        });
    </script> --}}

    {{-- <script>
    const visitUuid = "{{ session('visit_uuid') }}";

        window.addEventListener('beforeunload', function () {
            if (!visitUuid) return;

            navigator.sendBeacon(
                '/track/visit-end',
                new Blob(
                    [JSON.stringify({ visit_uuid: visitUuid })],
                    { type: 'application/json' }
                )
            );
        });
    </script> --}}
    
    <script>
        
        // class VisitTracker {
        //     constructor() {
        //         this.visitHistoriqueUuid = window.visitHistoriqueUuid;
        //         this.beaconSent = false;
        //         this.init();
        //     }
            
        //     init() {
        //         if (!this.visitHistoriqueUuid) return;
                
        //         // Page Visibility API
        //         document.addEventListener('visibilitychange', () => {
        //             if (document.visibilityState === 'hidden') {
        //                 this.sendBeacon();
        //             }
        //         });
                
        //         // Before unload
        //         window.addEventListener('beforeunload', () => {
        //             this.sendBeacon();
        //         });
                
        //         // Heartbeat toutes les 5 minutes pour maintenir la session
        //         setInterval(() => this.sendHeartbeat(), 5 * 60 * 1000);
        //     }
            
        //     sendBeacon() {
        //         console.log('📡 sendBeacon déclenché', this.visitHistoriqueUuid);
        //         if (this.beaconSent || !this.visitHistoriqueUuid) return;
                
        //         const data = {
        //             visit_historique_uuid: this.visitHistoriqueUuid,
        //             page: window.location.pathname,
        //             timestamp: Date.now()
        //         };
                
        //         const success = navigator.sendBeacon(
        //             '/track/visit-end',
        //             new Blob([JSON.stringify(data)], { type: 'application/json' })
        //         );
                
        //         if (success) this.beaconSent = true;
        //     }
            
        //     sendHeartbeat() {
        //         if (!this.visitHistoriqueUuid) return;
                
        //         fetch('/track/heartbeat', {
        //             method: 'POST',
        //             headers: { 'Content-Type': 'application/json' },
        //             body: JSON.stringify({ 
        //                 visit_historique_uuid: this.visitHistoriqueUuid 
        //             }),
        //             keepalive: true
        //         }).catch(console.error);
        //     }
        // }

        // // Initialisation
        // if (window.visitHistoriqueUuid) {
        //     window.visitTracker = new VisitTracker();
        // }
    </script>
    {{-- <script>
        window.visitHistoriqueUuid = @json(session('visit_historique_uuid'));
        class VisitTracker {
            constructor() {
                this.visitHistoriqueUuid = window.visitHistoriqueUuid;
                this.beaconSent = false;

                if (!this.visitHistoriqueUuid) return;

                this.init();
            }

            init() {
                const close = () => this.sendBeacon();

                // Quand l’onglet est caché (changement d’onglet, app background)
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'hidden') {
                        close();
                    }
                });

                // Plus fiable que beforeunload (mobile / Safari / iOS)
                window.addEventListener('pagehide', close);

                // Heartbeat toutes les 5 minutes
                this.heartbeatInterval = setInterval(() => {
                    this.sendHeartbeat();
                }, 5 * 60 * 1000);
            }

            sendBeacon() {
                if (this.beaconSent || !this.visitHistoriqueUuid) return;

                console.log('📡 sendBeacon déclenché', this.visitHistoriqueUuid);

                const data = {
                    visit_historique_uuid: this.visitHistoriqueUuid,
                    // page: window.location.pathname,
                    // timestamp: Date.now()
                };

                navigator.sendBeacon(
                    '/track/visit-end',
                    new Blob([JSON.stringify(data)], { type: 'application/json' })
                );

                this.beaconSent = true;
                clearInterval(this.heartbeatInterval);
            }

            sendHeartbeat() {
                if (!this.visitHistoriqueUuid) return;

                fetch('/track/heartbeat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        visit_historique_uuid: this.visitHistoriqueUuid
                    }),
                    keepalive: true
                }).catch(() => {});
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.visitHistoriqueUuid) {
                window.visitTracker = new VisitTracker();
            }
        });
    </script> --}}

    <script>
        // Configuration globale
        window.analyticsConfig = {
            visitHistoriqueUuid: @json(session('visit_historique_uuid')),
            pageViewHistoriqueUuid: @json(session('current_page_view_historique_uuid')),
            heartbeatInterval: 60000, // 1 minute
            inactivityTimeout: 300000, // 5 minutes
            debug: false
        };

        class EnhancedVisitTracker {
            constructor() {
                this.visitHistoriqueUuid = window.analyticsConfig.visitHistoriqueUuid;
                this.beaconSent = false;
                this.lastActivity = Date.now();
                this.inactivityCheckInterval = null;
                this.heartbeatInterval = null;
                
                if (!this.visitHistoriqueUuid) {
                    this.log('Aucun UUID de session trouvé');
                    return;
                }

                this.init();
            }

            init() {
                this.log('Initialisation du tracker de visite');
                
                // Détecteur d'activité utilisateur
                this.bindActivityEvents();
                
                // Vérification d'inactivité
                this.startInactivityCheck();
                
                // Heartbeat régulier
                this.startHeartbeat();
                
                // Événements de fermeture
                this.bindCloseEvents();
            }

            bindActivityEvents() {
                const events = ['click', 'mousemove', 'keypress', 'scroll', 'touchstart', 'touchmove'];
                
                events.forEach(event => {
                    document.addEventListener(event, () => {
                        this.lastActivity = Date.now();
                        this.log('Activité détectée');
                    }, { passive: true });
                });
            }

            startInactivityCheck() {
                this.inactivityCheckInterval = setInterval(() => {
                    const inactiveTime = Date.now() - this.lastActivity;
                    
                    if (inactiveTime > window.analyticsConfig.inactivityTimeout) {
                        this.log(`Inactivité détectée (${Math.round(inactiveTime/1000)}s), fermeture de session`);
                        this.sendBeacon();
                        clearInterval(this.inactivityCheckInterval);
                    }
                }, 30000); // Vérifier toutes les 30 secondes
            }

            startHeartbeat() {
                this.heartbeatInterval = setInterval(() => {
                    this.sendHeartbeat();
                }, window.analyticsConfig.heartbeatInterval);
                
                // Premier heartbeat immédiat
                setTimeout(() => this.sendHeartbeat(), 5000);
            }

            bindCloseEvents() {
                // Événements de visibilité
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'hidden') {
                        this.log('Page cachée, envoi beacon');
                        this.sendBeacon();
                    }
                });

                // Événements de navigation
                window.addEventListener('pagehide', () => this.sendBeacon());
                window.addEventListener('beforeunload', () => this.sendBeacon());
                
                // Gestion des onglets du navigateur
                window.addEventListener('unload', () => this.sendBeacon());
            }

            sendBeacon() {
                if (this.beaconSent || !this.visitHistoriqueUuid) return;

                this.log('📡 Envoi du beacon de fermeture');
                this.beaconSent = true;

                const data = {
                    visit_historique_uuid: this.visitHistoriqueUuid,
                    page: window.location.pathname,
                    timestamp: Date.now(),
                    inactive_time: Date.now() - this.lastActivity
                };

                const blob = new Blob([JSON.stringify(data)], { type: 'application/json' });
                
                // Essayer sendBeacon d'abord
                if (navigator.sendBeacon && navigator.sendBeacon('/track/visit-end', blob)) {
                    this.log('Beacon envoyé avec succès');
                } else {
                    // Fallback fetch
                    fetch('/track/visit-end', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data),
                        keepalive: true,
                        priority: 'low'
                    }).then(() => {
                        this.log('Fallback fetch réussi');
                    }).catch(err => {
                        this.log('Erreur fallback fetch:', err);
                    });
                }

                // Nettoyer les intervalles
                clearInterval(this.heartbeatInterval);
                clearInterval(this.inactivityCheckInterval);
            }

            sendHeartbeat() {
                if (!this.visitHistoriqueUuid || this.beaconSent) return;

                fetch('/track/heartbeat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        visit_historique_uuid: this.visitHistoriqueUuid,
                        last_activity: this.lastActivity
                    }),
                    keepalive: true,
                    priority: 'low'
                }).then(() => {
                    this.log('Heartbeat envoyé');
                }).catch(err => {
                    this.log('Erreur heartbeat:', err);
                });
            }

            log(...args) {
                if (window.analyticsConfig.debug) {
                    console.log('[Analytics]', ...args);
                }
            }
        }

        // Tracker de durée de page
        class PageViewTracker {
            constructor() {
                this.pageViewHistoriqueUuid = window.analyticsConfig.pageViewHistoriqueUuid;
                this.pageBeaconSent = false;
                
                if (!this.pageViewHistoriqueUuid) return;

                this.initPageTracking();
            }

            initPageTracking() {
                window.addEventListener('beforeunload', () => {
                    this.sendPageDurationBeacon();
                });

                window.addEventListener('pagehide', () => {
                    this.sendPageDurationBeacon();
                });

                // Auto-fermeture si inactif sur la page
                setTimeout(() => {
                    if (!this.pageBeaconSent) {
                        this.sendPageDurationBeacon();
                    }
                }, 1800000); // 30 minutes max par page
            }

            sendPageDurationBeacon() {
                if (this.pageBeaconSent || !this.pageViewHistoriqueUuid) return;

                this.pageBeaconSent = true;

                const data = {
                    historique_uuid: this.pageViewHistoriqueUuid,
                    page: window.location.pathname
                };

                const blob = new Blob([JSON.stringify(data)], { type: 'application/json' });
                
                if (navigator.sendBeacon) {
                    navigator.sendBeacon('/track/page-duration', blob);
                } else {
                    fetch('/track/page-duration', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data),
                        keepalive: true
                    });
                }
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            // Activer le debug si besoin
            window.analyticsConfig.debug = window.location.search.includes('debug=analytics');
            
            // Initialiser les trackers
            window.visitTracker = new EnhancedVisitTracker();
            window.pageViewTracker = new PageViewTracker();
            
            // Pour le débogage
            if (window.analyticsConfig.debug) {
                window.debugAnalytics = {
                    config: window.analyticsConfig,
                    tracker: window.visitTracker,
                    pageTracker: window.pageViewTracker
                };
                console.log('Analytics debug activé', window.debugAnalytics);
            }
        });
    </script>


</body>

</html>
