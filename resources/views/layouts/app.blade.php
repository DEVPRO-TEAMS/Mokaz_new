
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="fr-FR">

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

        <meta name="author" content="themesflat.com">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- font -->
        <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
        <!-- Icons -->
        <link rel="stylesheet" href="{{ asset('assets/fonts/font-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">


        <link rel="stylesheet" href="{{ asset('assets/css/apexcharts.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
        <link rel="stylesheet"type="text/css" href="{{ asset('assets/css/styles.css') }}" />
        <link rel="stylesheet"type="text/css" href="{{ asset('assets/css/custom.css') }}" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <!-- Favicon and Touch Icons  -->
        <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}">
        <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/logo/favicon.png') }}">

    <style>
        /* Conteneur des champs de saisie pour placer l'icône */
        /* Applique le style aux éléments en lecture seule */
        input[readonly], textarea[readonly], select[readonly] {
            background-color: #f0f0f0;  /* Couleur de fond gris pour les champs en readonly */
            border: 1px solid #ccc;     /* Bordure gris clair */
            /* cursor: not-allowed;        Curseur indiquant que l'action est interdite */
            cursor: no-drop;
            pointer-events: none;       /* Empêche toute interaction avec ces éléments */
        }

        /* Remplacer le curseur par l'emoji 🚫 lors du survol des champs readonly */
        input[readonly]:hover, textarea[readonly]:hover, select[readonly]:hover {
            cursor: no-drop;
            /* cursor: wait; */
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
            height: 38px !important;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }
    </style>
    </head>

    <body class="body bg-surface counter-scroll">
        <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NNNZRNVL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
        {{-- @dd($partner); --}}
        {{-- @dd(Auth::user()->user_type) --}}
        {{-- @dd(Auth::user()->partner_uuid); --}}
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

        <div id="wrapper" class="wrapper">
            <div id="page" class="clearfix">
                <div class="layout-wrap">
                    <!-- sidebar dashboard -->
                    @include('layouts.sidbar')
                    <!-- end sidebar dashboard -->
                    <div class="container-fluid" style="margin-top: 6%">
                        <div class="main-content">
                            @yield('content')
                        </div>
                        <div class="footer-dashboard">
                            <p class="text-variant-2">©2025 MOKAZ. Tous droits réservés.</p>
                        </div>
                    </div>
                    <div class="overlay-dashboard"></div>
                </div>
            </div>
        </div>

        <!-- /#page -->
        
        <!-- go top -->
        <div class="progress-wrap">
            <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                    style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 286.138;">
                </path>
            </svg>
        </div>

        <!-- Javascript -->

        <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <script src="https://kit.fontawesome.com/2b6a213e82.js" crossorigin="anonymous"></script>

         <!-- JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

        <script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/carousel.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/plugin.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/chart.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/chart-init.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/rangle-slider.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/countto.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/tinymce/tinymce.min.js')}}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/tinymce/tinymce-custom.js')}}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/shortcodes.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/jqueryui.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/animation_heading.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/main.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/custom.js') }}"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

        <script type="text/javascript" src="{{ asset('assets/js/owl.js') }}"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCFC3m2n0jBRFTMvUNZc0-6Y0Rzlcadzcw"></script>
        <script src="{{ asset('assets/js/map-single.js')}}"></script>
        <script src="{{ asset('assets/js/map.js') }}"></script>
        <script src="{{ asset('assets/js/dashboard.js') }}"></script>
        <script src="{{ asset('assets/js/map-contact.js') }}"></script>
        <script src="{{ asset('assets/js/marker.js') }}"></script>
        <script src="{{ asset('assets/js/infobox.min.js') }}"></script>

        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
        <script>
            $(document).ready(function() {
                $('.selection').select2({
                    placeholder: "Choisir",
                    allowClear: true,
                    width: '100%',
                });
            });
        </script>
        <script>
             $(document).ready(function() {
    

                var table = $('#example2').DataTable({
                    order: [],
                    lengthChange: true,
                    searching: false,
                    buttons: ['copy', 'excel', 'pdf', 'print'],
                    language: {
                                search: "Recherche :",
                                lengthMenu: "Afficher _MENU_ lignes",
                                zeroRecords: "Aucun enregistrement trouvé",
                                info: "Affichage de _START_ à _END_ sur _TOTAL_ enregistrements",
                                infoEmpty: "Aucun enregistrement disponible",
                                infoFiltered: "(filtré à partir de _MAX_ enregistrements)",
                                paginate: {
                                    first: "Premier",
                                    last: "Dernier",
                                    next: "Suivant",
                                    previous: "Précédent",
                                },
                            },
                });

                table.buttons().container()
                    .appendTo('#example2_wrapper .col-md-6:eq(0)');
            });
        </script>

    </body>

</html>
