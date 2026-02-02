@extends('layouts.main')
@section('content')
    <section class="container-fluid position-relative p-0 m-0" style="background: url('https://i.pinimg.com/736x/99/0f/c7/990fc7a568ad1fde0dcd8ea5a087eac8.jpg') no-repeat center center / cover; height: 350px;">
    <!-- Overlay en dégradé -->
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)); z-index: 1;">
    </div>

    <!-- Contenu centré -->
    <div class="row h-100 align-items-center justify-content-center text-center position-relative" style="z-index: 2; animation: fadeIn 1.5s ease;">
        <div class="col-11 col-md-8">
            <h2 class="text-white display-4 fw-bold mb-3">Liste des hébergements</h2>
            <p class="text-white lead mb-4">Trouvez l’hébergement idéal selon vos préférences et votre budget.</p>
            <a href="#liste-appartements" class="btn btn-outline-light px-4 py-2 rounded-pill shadow-sm">Voir les offres</a>
        </div>
    </div>
</section>

<!-- Animation fade-in -->
<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>


    <section class="pt-5">
        <div class="row" id="liste-appartements">
            <div class="col-sm-12 col-xl-4 col-lg-4 col-md-6">
                <div class="homeya-box">
                    <div class="archive-top">
                        <a href="{{ route('property.show') }}" class="images-group">
                            <div class="images-style">
                                <img src="https://i.pinimg.com/736x/99/0f/c7/990fc7a568ad1fde0dcd8ea5a087eac8.jpg"
                                    alt="img">
                            </div>
                            <div class="top">
                                <ul class="d-flex gap-8">
                                    <li class="flag-tag success">en vedette</li>
                                    <li class="flag-tag style-1">à vendre</li>
                                </ul>
                                <ul class="d-flex gap-4">
                                    <li class="box-icon w-32">
                                        <span class="icon icon-arrLeftRight"></span>
                                    </li>
                                    <li class="box-icon w-32">
                                        <span class="icon icon-heart"></span>
                                    </li>
                                    <li class="box-icon w-32">
                                        <span class="icon icon-eye"></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="bottom">
                                <span class="flag-tag style-2">Studio</span>
                            </div>
                        </a>
                        <div class="content">
                            <div class="h7 text-capitalize fw-7"><a href="{{ route('property.show') }}"
                                    class="link"> Villa moderne avec piscine</a></div>
                            <div class="desc"><i class="fs-16 icon icon-mapPin"></i>
                                <p>33 rue commerce, Abidjan, Cote d'ivoire</p>
                            </div>
                            <ul class="meta-list">
                                <li class="item">
                                    <i class="icon icon-bed"></i>
                                    <span>3</span>
                                </li>
                                <li class="item">
                                    <i class="icon icon-bathtub"></i>
                                    <span>2</span>
                                </li>
                                <li class="item">
                                    <i class="icon icon-ruler"></i>
                                    <span>600 SqFT</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="archive-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-8 align-items-center">
                            <div class="avatar avt-40 round">
                                <img src="https://i.pinimg.com/736x/66/2b/be/662bbef42e07620cbea41e3ac63a74eb.jpg"
                                    alt="avt">
                            </div>
                            <span>Arlene McCoy</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6>7250,00 Fcfa</h6>
                            <span class="text-variant-1">/SqFT</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-xl-4 col-lg-4 col-md-6">
                <div class="homeya-box">
                    <div class="archive-top">
                        <a href="{{ route('property.show') }}" class="images-group">
                            <div class="images-style">
                                <img src="https://i.pinimg.com/736x/9d/26/96/9d26968c849f6d53a7a4605791bef6ed.jpg"
                                    alt="img">
                            </div>
                            <div class="top">
                                <ul class="d-flex gap-8">
                                    <li class="flag-tag success">En vedette</li>
                                    <li class="flag-tag style-1">En Vente</li>
                                </ul>
                                <ul class="d-flex gap-4">
                                    <li class="box-icon w-32">
                                        <span class="icon icon-arrLeftRight"></span>
                                    </li>
                                    <li class="box-icon w-32">
                                        <span class="icon icon-heart"></span>
                                    </li>
                                    <li class="box-icon w-32">
                                        <span class="icon icon-eye"></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="bottom">
                                <span class="flag-tag style-2">Apartment</span>
                            </div>
                        </a>
                        <div class="content">
                            <div class="h7 text-capitalize fw-7"><a href="{{ route('property.show') }}"
                                    class="link">Villa del Mar Retreat, Malibu</a></div>
                            <div class="desc"><i class="fs-16 icon icon-mapPin"></i>
                                <p>72 avenue Noguess, Abidjan, Plateau</p>
                            </div>
                            <ul class="meta-list">
                                <li class="item">
                                    <i class="icon icon-bed"></i>
                                    <span>2</span>
                                </li>
                                <li class="item">
                                    <i class="icon icon-bathtub"></i>
                                    <span>2</span>
                                </li>
                                <li class="item">
                                    <i class="icon icon-ruler"></i>
                                    <span>600 SqFT</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="archive-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-8 align-items-center">
                            <div class="avatar avt-40 round">
                                <img src="https://i.pinimg.com/736x/33/70/7c/33707cd80ed6702a5d86ab2d66413f36.jpg"
                                    alt="avt">
                            </div>
                            <span>Annette Black</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6>250,00 Fcfa</h6>
                            <span class="text-variant-1">/month</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-xl-4 col-lg-4 col-md-6">
                <div class="homeya-box">
                    <div class="archive-top">
                        <a href="{{ route('property.show') }}" class="images-group">
                            <div class="images-style">
                                <img src="https://i.pinimg.com/736x/ba/6a/3a/ba6a3a8b779509bdd667f19adbb659e0.jpg"
                                    alt="img">
                            </div>
                            <div class="top">
                                <ul class="d-flex gap-8">
                                    <li class="flag-tag success">en vedette</li>
                                    <li class="flag-tag style-1">en vente</li>
                                </ul>
                                <ul class="d-flex gap-4">
                                    <li class="box-icon w-32">
                                        <span class="icon icon-arrLeftRight"></span>
                                    </li>
                                    <li class="box-icon w-32">
                                        <span class="icon icon-heart"></span>
                                    </li>
                                    <li class="box-icon w-32">
                                        <span class="icon icon-eye"></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="bottom">
                                <span class="flag-tag style-2">Villa</span>
                            </div>
                        </a>
                        <div class="content">
                            <div class="h7 text-capitalize fw-7"><a href="{{ route('property.show') }}"
                                    class="link">Rancho Villa Barbara, Santa Barbara</a></div>
                            <div class="desc"><i class="fs-16 icon icon-mapPin"></i>
                                <p>33 Maple Street, Rue du jardin ,Plateau</p>
                            </div>
                            <ul class="meta-list">
                                <li class="item">
                                    <i class="icon icon-bed"></i>
                                    <span>4</span>
                                </li>
                                <li class="item">
                                    <i class="icon icon-bathtub"></i>
                                    <span>2</span>
                                </li>
                                <li class="item">
                                    <i class="icon icon-ruler"></i>
                                    <span>600 SqFT</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="archive-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-8 align-items-center">
                            <div class="avatar avt-40 round">
                                <img src="https://i.pinimg.com/736x/33/70/7c/33707cd80ed6702a5d86ab2d66413f36.jpg"
                                    alt="avt">
                            </div>
                            <span>Ralph Edwards</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6>5050,00 Fcfa</h6>
                            <span class="text-variant-1">/Jour</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pt-5" style="height: 560px">
            <div id="map" style="height: 100%" class="top-map col-12" data-map-zoom="16" data-map-scroll="true"></div>
        </div>
    </section>
    
@endsection
