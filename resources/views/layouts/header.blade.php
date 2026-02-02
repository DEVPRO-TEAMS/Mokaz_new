<header class="main-header fixed-header">
    <!-- Header Lower -->
    <div class="header-lower">
        <div class="row">
            <div class="col-lg-12">
                <div class="inner-container d-flex justify-content-between align-items-center">
                    <!-- Logo Box -->
                    <div class="logo-box" style="width: 5rem; margin-top: 0">
                        <div class="logo"><a href="{{ route('welcome') }}"><img
                                    src="{{ asset('assets/images/logo/logo-main.png') }}" alt="logo" width="100%"
                                    height="33"></a>
                        </div>
                    </div>
                    <center class="d-none d-lg-block pt-3 w-100">
                        <div class="row align-items-center justify-content-center" style="width: 35%">
                            <div class="flat-bt-top col-md-6">
                                <a class="tf-btn primary" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#demandPartnariaModal">Devenir hébergeur</a>
                            </div>
                            <div class="flat-bt-top col-md-6">
                                <a href="javascript:void(0);" class="tf-btn btn btn-outline-dark" data-bs-toggle="modal"
                                    data-bs-target="#showReservationModal" class="item">
                                    Ma reservation
                                </a>
                            </div>
                        </div>
                    </center>

                    <div class="header-account">

                        <ul class="icon-box">
                            <li title="Mes Souhaits">
                                {{-- <a href="javascript:void(0);" class="item" onclick="toggleWishlistCart()">
                                    <span class="fs-4 icon icon-heart"></span>
                                </a> --}}
                            </li>
                        </ul>


                        @if (Auth::check())
                            <a href="#" class="box-avatar dropdown-toggle d-flex align-items-center gap-2"
                                data-bs-toggle="dropdown">
                                <div class="avatar avt-40 round">
                                    <img src="{{ asset('assets/images/avatar/user-profile.webp') }}" alt="avt">
                                </div>
                                <p class="name">{{ Auth::user()->name }} {{ Auth::user()->lastname }} <span
                                        class="icon icon-arr-down"></span></p>
                                <div class="dropdown-menu">
                                    @if (Auth::user()->user_type == 'admin')
                                        <a class="dropdown-item" href="{{ route('admin.index') }}">Tableau de bord</a>
                                    @elseif (Auth::user()->user_type == 'partner')
                                        <a class="dropdown-item" href="{{ route('partner.index') }}">Tableau de bord</a>
                                    @elseif (Auth::user()->user_type == 'comManager')
                                        <a class="dropdown-item" href="{{ route('comManager.statistics') }}">Tableau de bord</a>
                                    @endif
                                    
                                    {{-- <a class="dropdown-item"
                                        href="{{ Auth::user()->user_type == 'admin' ? route('admin.index') : (Auth::user()->user_type == 'partner' ? route('partner.index') : route('user.index')) }}">Tableau
                                        de bord</a> --}}
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
                            <div class="register">
                                <ul class="d-flex">
                                    <li><a href="#modalLogin" data-bs-toggle="modal">Connexion</a></li>
                                </ul>
                            </div>
                        @endif

                    </div>

                    <div class="mobile-nav-toggler mobile-button"><span></span></div>

                </div>
            </div>
        </div>
    </div>
    <!-- End Header Lower -->

    <!-- Mobile Menu  -->
    <div class="close-btn"><span class="icon flaticon-cancel-1"></span></div>
    <div class="mobile-menu">
        <div class="menu-backdrop"></div>
        <nav class="menu-box">
            <div class="nav-logo">
                <a href="{{ route('welcome') }}">
                    <img src="{{ asset('assets/images/logo/logo-main.png') }}" alt="nav-logo" width="60%">
                </a>
            </div>
            <div class="bottom-canvas">
                <ul class="icon-box">
                    <li title="Mes Souhaits">
                        {{-- <a href="javascript:void(0);" class="item" onclick="toggleWishlistCart()">
                            <span class="fs-4 icon icon-heart"></span>
                        </a> --}}
                    </li>
                </ul>
                @if (Auth::check())
                    <a href="#" class="box-avatar dropdown-toggle d-flex align-items-center gap-2"
                        data-bs-toggle="dropdown">
                        <div class="avatar avt-40 round">
                            <img src="{{ asset('assets/images/avatar/user-profile.webp') }}" alt="avt">
                        </div>
                        <p class="name">{{ Auth::user()->name }} {{ Auth::user()->lastname }} <span
                                class="icon icon-arr-down"></span></p>
                        <div class="dropdown-menu">
                            @if (Auth::user()->user_type == 'admin')
                                <a class="dropdown-item" href="{{ route('admin.index') }}">Tableau de bord</a>
                            @elseif (Auth::user()->user_type == 'partner')
                                <a class="dropdown-item" href="{{ route('partner.index') }}">Tableau de bord</a>
                            @elseif (Auth::user()->user_type == 'comManager')
                                <a class="dropdown-item" href="{{ route('comManager.statistics') }}">Tableau de bord</a>
                            @endif
                            {{-- <a class="dropdown-item"
                                href="{{ Auth::user()->user_type == 'admin' ? route('admin.index') : (Auth::user()->user_type == 'partner' ? route('partner.index') : route('comManager.statistics')) }}">Tableau
                                de bord</a> --}}
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
                    <div class="login-box flex align-items-center">
                        <a href="#modalLogin" data-bs-toggle="modal">Connexion</a>
                    </div>
                @endif

                <div class="menu-outer"></div>
                <div class="button-mobi-sell">
                    {{-- <a class="tf-btn primary" href="{{ route('appart.all') }}">Tous nos biens</a> --}}
                    <a class="tf-btn primary" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#demandPartnariaModal">Devenir hébergeur</a>
                </div>
                <div class="button-mobi-sell">
                    <a href="javascript:void(0);" class="btn btn-outline-dark" data-bs-toggle="modal"
                        data-bs-target="#showReservationModal" class="item">
                        Ma reservation
                    </a>
                </div>


                <div class="mobi-icon-box">
                    <div class="box d-flex align-items-center">
                        <span class="icon icon-phone2"></span>
                        <div>+225 07 87 24 51 97</div>
                    </div>
                    <div class="box d-flex align-items-center">
                        <span class="icon icon-mail"></span>
                        <div>info@jsbeyci.com</div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- End Mobile Menu -->



</header>
