<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mokaz - Maintenance en cours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet"type="text/css" href="{{ asset('assets/css/styles.css') }}" />
    <style>
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #fee2e2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .bg-red-700 {
            background-color: #b91c1c !important;
        }

        .bg-red-600 {
            background-color: #dc2626 !important;
        }

        .bg-red-500 {
            background-color: #ef4444 !important;
        }

        .bg-red-400 {
            background-color: #f87171 !important;
        }

        .text-red-600 {
            color: #dc2626 !important;
        }

        .text-red-700 {
            color: #b91c1c !important;
        }

        .text-red-400 {
            color: #f87171 !important;
        }

        .border-red-600 {
            border-color: #dc2626 !important;
        }

        .bg-red-50 {
            background-color: #fef2f2 !important;
        }

        .hover-red-700:hover {
            background-color: #b91c1c !important;
        }

        .hover-red-600:hover {
            background-color: #dc2626 !important;
        }

        .hover-red-500:hover {
            background-color: #ef4444 !important;
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .maintenance-card {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .maintenance-left {
            background-color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            min-height: 300px;
        }

        .border-left-red {
            border-left: 4px solid #dc2626;
        }

        .social-btn {
            transition: all 0.3s ease;
        }

        .footer-link {
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            color: white !important;
        }
    </style>
</head>

<body class="gradient-bg">
    <header class="bg-red-700 text-white py-3 shadow-lg">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-home fs-3 me-2"></i>
                    <h1 class="fs-4 fw-bold mb-0 text-uppercase text-white">Mokaz</h1>
                </div>
                <span class="small fw-light"><a href="#demandPartnariaModal" data-bs-toggle="modal"
                        class="text-white text-decoration-none fs-5 btn btn-outline-warning bg-warning text-white"><strong>
                            <i class="fas fa-handshake me-1"></i> Devenir partenaire</strong></a></span>
                <span class="small fw-light">
                    @if (Auth::check())
                        <a href="#"  class="box-avatar dropdown-toggle d-flex align-items-center gap-2 text-white fs-5"
                            data-bs-toggle="dropdown">
                            <div class="avatar avt-40 round">
                                <img src="{{ asset('assets/images/avatar/user-profile.webp') }}" alt="avt">
                            </div>
                            <p class="name">{{ Auth::user()->name }} {{ Auth::user()->lastname }} <span
                                    class="icon icon-arr-down"></span></p>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                    href="{{ Auth::user()->user_type == 'admin' ? route('admin.index') : (Auth::user()->user_type == 'partner' ? route('partner.index') : route('user.index')) }}">Tableau
                                    de bord</a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">Se
                                    deconnecter</a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </a>
                    @else
                        <a href="#modalLogin" data-bs-toggle="modal" class="text-white text-decoration-none fs-5"> <i
                                class="fas fa-sign-in-alt me-1"></i> Connexion
                        </a>
                    @endif

                </span>
            </div>
        </div>
    </header>

    <main class="main-content py-5">
        <div class="container px-4">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-8">
                    <div class="maintenance-card bg-white">
                        <div class="row g-0">
                            <div class="col-md-4 maintenance-left">
                                <div class="text-white text-center">
                                    <i class="fas fa-tools pulse-animation" style="font-size: 4rem;"></i>
                                    <h2 class="fs-3 fw-bold mt-3 mb-0">Maintenance en cours</h2>
                                </div>
                            </div>
                            <div class="col-md-8 p-4 p-md-5">
                                <div class="text-center d-md-none mb-4">
                                    <i class="fas fa-tools text-red-600 pulse-animation" style="font-size: 2.5rem;"></i>
                                </div>
                                <h2 class="fs-2 fw-bold text-dark mb-4 text-center text-md-start">Nous améliorons votre
                                    expérience</h2>
                                <p class="text-muted mb-4 text-center text-md-start">
                                    Notre plateforme Mokaz est actuellement en maintenance pour vous offrir un service
                                    encore plus performant.
                                    Nous mettons tout en œuvre pour que cette interruption soit la plus courte possible.
                                </p>

                                <div class="bg-red-50 border-left-red p-3 mb-4">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-clock text-red-600"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="small text-red-700 mb-0">
                                                <span class="fw-bold">Temps estimé:</span> Retour prévu avant 18h00
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h3 class="fs-5 fw-semibold text-dark mb-3">Que faire en attendant ?</h3>
                                    <ul class="list-unstyled">
                                        <li class="d-flex align-items-start mb-2">
                                            <i class="fas fa-phone-alt text-red-600 mt-1 me-2"></i>
                                            <span>Contactez-nous au <strong>+225 07 87 24 51 97</strong></span>
                                        </li>
                                        <li class="d-flex align-items-start mb-2">
                                            <i class="fas fa-envelope text-red-600 mt-1 me-2"></i>
                                            <span>Envoyez-nous un email à <strong>info@jsbeyci.com</strong></span>
                                        </li>
                                        <li class="d-flex align-items-start mb-2">
                                            <i class="fas fa-calendar-check text-red-600 mt-1 me-2"></i>
                                            <span>Consultez nos réseaux sociaux pour des mises à jour</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                                    <a href="#"
                                        class="btn bg-red-600 text-white fw-medium social-btn hover-red-700">
                                        <i class="fab fa-facebook-f me-2"></i> Facebook
                                    </a>
                                    <a href="#"
                                        class="btn bg-red-500 text-white fw-medium social-btn hover-red-600">
                                        <i class="fab fa-twitter me-2"></i> Twitter
                                    </a>
                                    <a href="#"
                                        class="btn bg-red-400 text-white fw-medium social-btn hover-red-500">
                                        <i class="fab fa-instagram me-2"></i> Instagram
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small mb-0">
                        &copy; 2025 <span class="text-red-400">JSBey</span> - Tous droits réservés
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-center justify-content-md-end gap-4">
                        <a href="#" class="text-muted footer-link text-decoration-none">
                            <i class="fas fa-globe me-1"></i> jsbeyci.com
                        </a>
                        <a href="{{ asset('/conditions/cgu.pdf') }}" target="_blank"
                            class="text-muted footer-lin text-decoration-none text-white">
                            <i class="fas fa-lock me-1"></i> CGU et Politique de confidentialité
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @include('components.loginModal')
    @include('partners.pages.demandPartnariaModal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Compte à rebours
        function updateCountdown() {
            const now = new Date();
            const target = new Date();
            target.setHours(18, 0, 0, 0); // 18h00

            if (now > target) {
                target.setDate(target.getDate() + 1);
            }

            const diff = target - now;
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

            const countdownElement = document.getElementById('countdown');
            if (countdownElement) {
                countdownElement.textContent = `${hours}h ${minutes}m`;
            }
        }

        setInterval(updateCountdown, 60000);
        updateCountdown();
    </script>


</body>

</html>
