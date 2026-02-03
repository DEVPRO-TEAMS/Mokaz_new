<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\city;
use App\Models\User;

use App\Models\Search;
use App\Models\Comment;
use App\Models\Partner;
use App\Models\Property;
use App\Models\Variable;
use App\Models\Appartement;
use App\Models\Testimonial;
use Illuminate\Support\Str;
use App\Models\Tarification;
use Illuminate\Http\Request;
use App\Models\AppartementView;
use Barryvdh\DomPDF\Facade\Pdf;
use function PHPSTORM_META\type;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
// use App\Services\GeocodingService;

class PagesController extends Controller
{

    public function index(Request $request)
    {
        // 📋 Récupération des types d'appartements actifs
        $typeAppart = Variable::where(['type' => 'type_of_appart', 'etat' => 'actif'])->get();

        // dd($typeAppart);

        // ⚙️ Paramètres de pagination et de requête
        $perPage = $request->get('perPage', 6);
        $latitudeUser = $request->get('lat');
        $longitudeUser = $request->get('lng');
        $search = trim($request->input('search'));
        $location = trim($request->input('location'));
        $type = $request->input('type');
        $ville = $request->input('ville');
        $categorie = $request->input('categorie');
        $rooms = $request->input('rooms');
        $bathrooms = $request->input('bathrooms');
        $sejour = $request->input('sejour');
        $commodities = $request->input('commodities');
        $min_price = "";
        $max_price = "";


        $isSearch = ($search || $location || $type || $categorie || $ville || $rooms || $bathrooms || $sejour || $commodities);

        if ($isSearch) {
            $min_price = $request->input('min_price');
            $max_price = $request->input('max_price');
        }


        // créer une session pour stocker les latitudeUser et longitudeUser
        session(['lat' => $latitudeUser, 'lng' => $longitudeUser]);

        //  Si l'utilisateur fait une recherche manuelle, on ignore la géolocalisation
        $useGeolocation = !($search || $location || $type || $categorie || $ville || $rooms || $bathrooms || $sejour || $commodities || $min_price || $max_price);

        // Requête de base : on récupère les appartements actifs via la table Search
        $searchQuery = Search::query();

        if ($search) {
            // $searchQuery->where('query', 'like', "%$search%");
            $fulltextConditions = array_filter([$search]);

            if (!empty($fulltextConditions)) {
                $match = implode(' ', $fulltextConditions);
                $searchQuery->whereRaw("MATCH(query) AGAINST(? IN BOOLEAN MODE)", [$match]);
            }
        }

        // 2️⃣ Filtre par localisation
        if ($location) {
            $searchQuery->where('query', 'like', "%$location%");
        }

        // 3️⃣ Filtre par ville
        if ($ville) {
            $searchQuery->where('query', 'like', "%$ville%");
        }

        // 4️⃣ Filtre par catégorie
        if ($categorie) {
            $searchQuery->where('query', 'like', "%$categorie%");
        }

        // 5️⃣ Filtre par type
        if ($type) {
            $searchQuery->where('query', 'like', "%$type%");
        }
        // 5️⃣ Filtre par rooms
        if ($rooms) {
            $searchQuery->where('query', 'like', "%$rooms%");
        }
        // 5️⃣ Filtre par bathrooms
        if ($bathrooms) {
            $searchQuery->where('query', 'like', "%$bathrooms%");
        }
        // 5️⃣ Filtre par sejour
        if ($sejour) {
            $searchQuery->where('query', 'like', "%$sejour%");
        }

        // 🔹 Récupérer les appartements qui correspondent à TOUS les filtres
        $appartementIds = $searchQuery->pluck('appartement_uuid')->toArray();

        
        // Requête de base : appartements actifs et disponibles
        $query = Appartement::with('property')
            ->where('appartements.etat', 'actif')
            ->where('appartements.nbr_available', '>', 0)
            ->whereIn('appartements.uuid', $appartementIds);
        
        if ($request->filled('commodities')) {
            foreach ($request->commodities as $commodity) {
                $query->where('appartements.commodities', 'LIKE', '%' . $commodity . '%');
            }
        }

        if ($request->filled(['min_price', 'max_price'])) {
            $query->whereHas('tarifications', function ($q) use ($request) {
                $q->whereBetween('price', [
                    $request->min_price,
                    $request->max_price
                ]);
            });
        }

        // Calcul de distance Haversine et tri si coordonnées fournies
        if ($latitudeUser && $longitudeUser) {
            $haversine = "(6371 * acos(cos(radians($latitudeUser)) 
            * cos(radians(properties.latitude)) 
            * cos(radians(properties.longitude) - radians($longitudeUser)) 
            + sin(radians($latitudeUser)) 
            * sin(radians(properties.latitude))))";

            // Filtrer sur la distance ≤ 10 km si géolocalisation active
            if ($useGeolocation) {
                $query->whereHas('property', function ($q) use ($haversine) {
                    $q->whereRaw("$haversine <= 10");
                });

                // Ajouter la distance au SELECT et trier par distance croissante
                $query->with(['property' => function ($q) use ($haversine) {
                    $q->addSelect([
                        'properties.*',
                        DB::raw("$haversine AS distance_km")
                    ]);
                }])
                    ->join('properties', 'appartements.property_uuid', '=', 'properties.uuid')
                    ->select('appartements.*', DB::raw("$haversine AS distance_km"))
                    ->orderBy('distance_km', 'asc')
                    ->orderBy('appartements.created_at', 'desc');
            } else {
                // Ajouter la distance au SELECT de la relation property
                $query->with(['property' => function ($q) use ($haversine) {
                    $q->addSelect([
                        'properties.*',
                        DB::raw("$haversine AS distance_km")
                    ]);
                }]);
            }
        } else {
            // Tri par date de création si pas de géolocalisation
            $query->orderBy('appartements.created_at', 'desc');
        }

        // Pagination des appartements
        $apparts = $query->paginate($perPage);

        // Reverse geocoding POUR CHAQUE APPARTEMENT
    foreach ($apparts as $appart) {

        if (
            $appart->property &&
            $appart->property->latitude &&
            $appart->property->longitude
        ) {
            $location = GeocodingService::reverse(
                $appart->property->latitude,
                $appart->property->longitude
            );

            if ($location) {
                // Injection dynamique
                $appart->property->setAttribute('country_name',  $location['country']);
                $appart->property->setAttribute('city_name',     $location['city']);
                $appart->property->setAttribute('district_name', $location['district']);
                $appart->property->setAttribute('address_name',  $location['address']);
            }
        }
    }
    // dd($apparts);

        $appartements = Appartement::where('etat', 'actif')
            ->where('nbr_available', '>', 0)->get();
        // $priceRange = Tarification::where('etat', 'actif')
        //     ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
        //     ->first();
        $priceRange = Tarification::where('etat', 'actif')
            ->whereHas('appartement', function ($q) {
                $q->where('etat', 'actif')
                ->where('nbr_available', '>', 0);
            })
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();
        // dd($priceRange);
        $minPrice = $priceRange->min_price; // prix minimum
        $maxPrice  = $priceRange->max_price; // prix maximum

        // Meilleurs appartements (ceux ayant le plus de réservations)
        $bestApparts = Appartement::withCount('reservations')
            ->where('appartements.etat', 'actif')
            ->where('appartements.nbr_available', '>', 0)
            ->orderByDesc('reservations_count')
            ->take(3)
            ->with('tarifications')
            ->get();

        // appartemnets par commodité
        foreach ($appartements as $appartement) {
            $commodities = [];

            if (!empty($appartement->commodities)) {
                $commodities = array_map(
                    'trim',
                    explode(',', $appartement->commodities)
                );
            }
        }

        // Liste des localisations groupées (Pays - Ville)
        $locations = Property::with(['ville.locationImage', 'pays'])
            ->where('etat', 'actif')
            ->get()
            ->groupBy(function ($property) {
                return $property->pays?->label . ' - ' . $property->ville?->label;
            });
        $testimonials = Testimonial::all();

        $categories = Variable::where(['type' => 'category_of_property', 'etat' => 'actif'])->get();
        $cities = city::where('country_code', 'CIV')->get();
        return view('welcome', compact('apparts', 'bestApparts', 'typeAppart', 'locations', 'testimonials', 'categories', 'cities', 'commodities', 'minPrice', 'maxPrice'));
    }


    public function getAllProperties()
    {
        $properties = Property::where('etat', 'actif')
            ->whereHas('apartements', function ($query) {
                $query->where('nbr_available', '>', 0)->where('etat', 'actif');
            })
            ->with([
                'type',
                'apartements' => function ($query) {
                    $query->where('nbr_available', '>', 0)
                        ->where('etat', 'actif')
                        ->with('tarifications');
                }
            ])
            ->get()
            ->map(function ($property) {
                $tarifsHeure = [];
                $tarifsJour = [];

                $minHeureTarif = null;
                $minJourTarif = null;

                foreach ($property->apartements as $appartement) {
                    foreach ($appartement->tarifications as $tarif) {
                        if ($tarif->sejour == 'Heure') {
                            $tarifsHeure[] = $tarif;
                        } elseif ($tarif->sejour == 'Jour') {
                            $tarifsJour[] = $tarif;
                        }
                    }
                }

                if (!empty($tarifsHeure)) {
                    $minHeureTarif = collect($tarifsHeure)->sortBy('price')->first();
                }

                if (!empty($tarifsJour)) {
                    $minJourTarif = collect($tarifsJour)->sortBy('price')->first();
                }

                if ($minHeureTarif && (!$minJourTarif || $minHeureTarif->price < $minJourTarif->price)) {
                    $property->min_tarif = $minHeureTarif->price;
                    $property->tarif_type = 'Heure';
                    $property->nbr_sejour = $minHeureTarif->nbr_of_sejour;
                } elseif ($minJourTarif) {
                    $property->min_tarif = $minJourTarif->price;
                    $property->tarif_type = 'Jour';
                    $property->nbr_sejour = $minJourTarif->nbr_of_sejour;
                } else {
                    $property->min_tarif = null;
                    $property->tarif_type = null;
                    $property->nbr_sejour = null;
                }

                return $property;
            });

        return response()->json($properties);
    }
    public function indexReservations()
    {
        return view('pages.reservation');
    }
    public function appartByProperty(Request $request, $uuid)
    {
        $typeAppart = Variable::where(['type' => 'type_of_appart', 'etat' => 'actif'])->get();
        $perPage = $request->get('perPage', 6);
        $categorie = $request->input('categorie');
        // recuperer les coordonnées de l'utilisateur dans la session
        $latitudeUser = session()->get('lat');
        $longitudeUser = session()->get('lng');
        $ville = $request->input('ville');

        $type = $request->input('type');
        $rooms = $request->input('rooms');
        $bathrooms = $request->input('bathrooms');
        $sejour = $request->input('sejour');
        $commodities = $request->input('commodities');
        $min_price = $request->input('min_price');
        $max_price = $request->input('max_price');

       

        $search = $request->input('search');
        $location = $request->input('location');

        //  Si l'utilisateur fait une recherche manuelle, on ignore la géolocalisation
        $useGeolocation = !($search || $location || $type || $categorie || $ville || $rooms || $bathrooms || $sejour || $commodities || $min_price || $max_price);

        // Requête de base : on récupère les appartements actifs via la table Search
        $searchQuery = Search::query();
        // 1️⃣ Recherche mot-clé
        if ($search) {
            // $searchQuery->where('query', 'like', "%$search%");
            $fulltextConditions = array_filter([$search]);

            if (!empty($fulltextConditions)) {
                $match = implode(' ', $fulltextConditions);
                $searchQuery->whereRaw("MATCH(query) AGAINST(? IN BOOLEAN MODE)", [$match]);
            }
        }

        // 2️⃣ Filtre par localisation
        if ($location) {
            $searchQuery->where('query', 'like', "%$location%");
        }

        // 3️⃣ Filtre par ville
        if ($ville) {
            $searchQuery->where('query', 'like', "%$ville%");
        }

        // 4️⃣ Filtre par catégorie
        if ($categorie) {
            $searchQuery->where('query', 'like', "%$categorie%");
        }

        // 5️⃣ Filtre par type
        if ($type) {
            $searchQuery->where('query', 'like', "%$type%");
        }

         // 5️⃣ Filtre par rooms
        if ($rooms) {
            $searchQuery->where('query', 'like', "%$rooms%");
        }
        // 5️⃣ Filtre par bathrooms
        if ($bathrooms) {
            $searchQuery->where('query', 'like', "%$bathrooms%");
        }
        // 5️⃣ Filtre par sejour
        if ($sejour) {
            $searchQuery->where('query', 'like', "%$sejour%");
        }

        // 🔹 Récupérer les appartements qui correspondent à TOUS les filtres
        $appartementIds = $searchQuery->pluck('appartement_uuid')->toArray();

        // Requête de base : appartements actifs et disponibles
        $query = Appartement::with('property')
            ->where('appartements.property_uuid', $uuid)
            ->where('appartements.etat', 'actif')
            ->where('appartements.nbr_available', '>', 0)
            ->whereIn('appartements.uuid', $appartementIds);

        if ($request->filled('commodities')) {
            foreach ($request->commodities as $commodity) {
                $query->where('appartements.commodities', 'LIKE', '%' . $commodity . '%');
            }
        }

        if ($request->filled(['min_price', 'max_price'])) {
            $query->whereHas('tarifications', function ($q) use ($request) {
                $q->whereBetween('price', [
                    $request->min_price,
                    $request->max_price
                ]);
            });
        }


        // Calcul de distance Haversine et tri si coordonnées fournies
        if ($latitudeUser && $longitudeUser) {
            $haversine = "(6371 * acos(cos(radians($latitudeUser)) 
            * cos(radians(properties.latitude)) 
            * cos(radians(properties.longitude) - radians($longitudeUser)) 
            + sin(radians($latitudeUser)) 
            * sin(radians(properties.latitude))))";

            if ($useGeolocation) {
                // $query->whereHas('property', function ($q) use ($haversine) {
                //     $q->whereRaw("$haversine <= 10");
                // });

                // Ajouter la distance au SELECT et trier par distance croissante
                $query->with(['property' => function ($q) use ($haversine) {
                    $q->addSelect([
                        'properties.*',
                        DB::raw("$haversine AS distance_km")
                    ]);
                }])
                    ->join('properties', 'appartements.property_uuid', '=', 'properties.uuid')
                    ->select('appartements.*', DB::raw("$haversine AS distance_km"))
                    ->orderBy('distance_km', 'asc')
                    ->orderBy('appartements.created_at', 'desc');
            } else {
                // Ajouter la distance au SELECT de la relation property
                $query->with(['property' => function ($q) use ($haversine) {
                    $q->addSelect([
                        'properties.*',
                        DB::raw("$haversine AS distance_km")
                    ]);
                }]);
            }
        } else {
            // Tri par date de création si pas de géolocalisation
            $query->orderBy('appartements.created_at', 'desc');
        }

        // Pagination des appartements
        $apparts = $query->paginate($perPage);

        $appartements = Appartement::where('etat', 'actif')
            ->where('appartements.property_uuid', $uuid)
            ->where('nbr_available', '>', 0)->get();

        // $priceRange = Tarification::where('etat', 'actif')
        //     ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
        //     ->first();
        $priceRange = Tarification::where('etat', 'actif')
        ->whereHas('appartement', function ($q) use ($uuid) {
            $q->where('etat', 'actif')
            ->where('nbr_available', '>', 0)
            ->where('property_uuid', $uuid);
        })
        ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
        ->first();
        // dd($priceRange);
        $minPrice = $priceRange->min_price; // prix minimum
        $maxPrice  = $priceRange->max_price; // prix maximum

        // appartemnets par commodité
        foreach ($appartements as $appartement) {
            $commodities = [];

            if (!empty($appartement->commodities)) {
                $commodities = array_map(
                    'trim',
                    explode(',', $appartement->commodities)
                );
            }
        }

        // $apparts = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $categories = Variable::where(['type' => 'category_of_property', 'etat' => 'actif'])->get();
        
        $cities = city::where('country_code', 'CIV')->get();
        return view('pages.apparts', compact('apparts', 'typeAppart', 'uuid', 'categories', 'cities','commodities', 'minPrice', 'maxPrice'));
    }

    public function allApparts(Request $request)
    {
        $typeAppart = Variable::where(['type' => 'type_of_appart', 'etat' => 'actif'])->get();
        $perPage = $request->get('perPage', 6);


        $search = $request->input('search');
        $location = $request->input('location');
        $categorie = $request->input('categorie');
        $type = $request->input('type');
        $ville = $request->input('ville');
        // recuperer les coordonnées de l'utilisateur dans la session
        $latitudeUser = session()->get('lat');
        $longitudeUser = session()->get('lng');

        $rooms = $request->input('rooms');
        $bathrooms = $request->input('bathrooms');
        $sejour = $request->input('sejour');
        $commodities = $request->input('commodities');
        $min_price = $request->input('min_price');
        $max_price = $request->input('max_price');
        //  Si l'utilisateur fait une recherche manuelle, on ignore la géolocalisation
        $useGeolocation = !($search || $location || $type || $categorie || $rooms || $bathrooms || $sejour || $commodities || $min_price || $max_price);


        // Requête de base : on récupère les appartements actifs via la table Search
        $searchQuery = Search::query();
        // 1️⃣ Recherche mot-clé
        if ($search) {
            // $searchQuery->where('query', 'like', "%$search%");
            $fulltextConditions = array_filter([$search]);

            if (!empty($fulltextConditions)) {
                $match = implode(' ', $fulltextConditions);
                $searchQuery->whereRaw("MATCH(query) AGAINST(? IN BOOLEAN MODE)", [$match]);
            }
        }

        // 2️⃣ Filtre par localisation
        if ($location) {
            $searchQuery->where('query', 'like', "%$location%");
        }

        // 3️⃣ Filtre par ville
        if ($ville) {
            $searchQuery->where('query', 'like', "%$ville%");
        }

        // 4️⃣ Filtre par catégorie
        if ($categorie) {
            $searchQuery->where('query', 'like', "%$categorie%");
        }

        // 5️⃣ Filtre par type
        if ($type) {
            $searchQuery->where('query', 'like', "%$type%");
        }

         // 5️⃣ Filtre par rooms
        if ($rooms) {
            $searchQuery->where('query', 'like', "%$rooms%");
        }
        // 5️⃣ Filtre par bathrooms
        if ($bathrooms) {
            $searchQuery->where('query', 'like', "%$bathrooms%");
        }
        // 5️⃣ Filtre par sejour
        if ($sejour) {
            $searchQuery->where('query', 'like', "%$sejour%");
        }

        // 🔹 Récupérer les appartements qui correspondent à TOUS les filtres
        $appartementIds = $searchQuery->pluck('appartement_uuid')->toArray();

        // Requête de base : appartements actifs et disponibles
        $query = Appartement::with('property')
            ->where('appartements.etat', 'actif')
            ->where('appartements.nbr_available', '>', 0)
            ->whereIn('appartements.uuid', $appartementIds);

        if ($request->filled('commodities')) {
            foreach ($request->commodities as $commodity) {
                $query->where('appartements.commodities', 'LIKE', '%' . $commodity . '%');
            }
        }

        if ($request->filled(['min_price', 'max_price'])) {
            $query->whereHas('tarifications', function ($q) use ($request) {
                $q->whereBetween('price', [
                    $request->min_price,
                    $request->max_price
                ]);
            });
        }

        // Calcul de distance Haversine et tri si coordonnées fournies
        if ($latitudeUser && $longitudeUser) {
            $haversine = "(6371 * acos(cos(radians($latitudeUser)) 
            * cos(radians(properties.latitude)) 
            * cos(radians(properties.longitude) - radians($longitudeUser)) 
            + sin(radians($latitudeUser)) 
            * sin(radians(properties.latitude))))";

            if ($useGeolocation) {
                // $query->whereHas('property', function ($q) use ($haversine) {
                //     $q->whereRaw("$haversine <= 10");
                // });

                // Ajouter la distance au SELECT et trier par distance croissante
                $query->with(['property' => function ($q) use ($haversine) {
                    $q->addSelect([
                        'properties.*',
                        DB::raw("$haversine AS distance_km")
                    ]);
                }])
                    ->join('properties', 'appartements.property_uuid', '=', 'properties.uuid')
                    ->select('appartements.*', DB::raw("$haversine AS distance_km"))
                    ->orderBy('distance_km', 'asc')
                    ->orderBy('appartements.created_at', 'desc');
            } else {
                // Ajouter la distance au SELECT de la relation property
                $query->with(['property' => function ($q) use ($haversine) {
                    $q->addSelect([
                        'properties.*',
                        DB::raw("$haversine AS distance_km")
                    ]);
                }]);
            }
        } else {
            // Tri par date de création si pas de géolocalisation
            $query->orderBy('appartements.created_at', 'desc');
        }

        // Pagination des appartements
        $apparts = $query->paginate($perPage);

        $appartements = Appartement::where('etat', 'actif')
            ->where('nbr_available', '>', 0)->get();

        // $priceRange = Tarification::where('etat', 'actif')
        //     ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
        //     ->first();
        $priceRange = Tarification::where('etat', 'actif')
            ->whereHas('appartement', function ($q) {
                $q->where('etat', 'actif')
                ->where('nbr_available', '>', 0);
            })
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();
        // dd($priceRange);
        $minPrice = $priceRange->min_price; // prix minimum
        $maxPrice  = $priceRange->max_price; // prix maximum

        // appartemnets par commodité
        foreach ($appartements as $appartement) {
            $commodities = [];

            if (!empty($appartement->commodities)) {
                $commodities = array_map(
                    'trim',
                    explode(',', $appartement->commodities)
                );
            }
        }

        // $apparts = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $categories = Variable::where(['type' => 'category_of_property', 'etat' => 'actif'])->get();
        $cities = city::where('country_code', 'CIV')->get();

        return view('pages.showAllApparts', compact('apparts', 'typeAppart', 'categories', 'cities','commodities', 'minPrice', 'maxPrice'));
    }

    public function indexApropos()
    {
        return view('pages.apropos');
    }
    public function indexContact()
    {
        return view('pages.contact');
    }
    public function indexFaq()
    {
        return view('pages.faq');
    }
    public function indexPolitiq()
    {
        return view('pages.politiq');
    }
    public function show(string $uuid)
    {
        $appart = Appartement::where('etat', '=', 'actif')->where('uuid', $uuid)->first();

        $visitUuid = session('visit_uuid');

        if ($visitUuid) {
            $todayStart = Carbon::today();
            $todayEnd   = Carbon::today()->endOfDay();

            $view = AppartementView::where('visit_uuid', $visitUuid)
            ->where('appartement_uuid', $uuid)
            ->whereBetween('viewed_at', [$todayStart, $todayEnd])
            ->first();

            if ($view) {
                // déjà visité aujourd’hui → update
                $view->update([
                    'viewed_at' => now(),
                ]);
            } else {
                // pas encore visité aujourd’hui → create
                AppartementView::create([
                    'visit_uuid'       => $visitUuid,
                    'appartement_uuid' => $uuid,
                    'viewed_at'        => now(),
                ]);
            }
        }
        return view('pages.detail', compact('appart'));
    }


    public function addComment(Request $request)
    {

        $comment = Comment::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'appart_uuid' => $request->appart_uuid,
            'property_uuid' => $request->property_uuid,
            'partner_uuid' => $request->partner_uuid,
            'email' => $request->email,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'ajout du commentaire',
                'code' => 500
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté avec succès',
            'data' => $comment,
            'code' => 200
        ]);
    }

    // public function getComments(Request $request)
    // {
    //     $perPage = $request->get('perPage', 2);

    //     $comments = Comment::orderBy('created_at', 'desc')->paginate($perPage);

    //     return response()->json($comments);
    // }

    public function getComments(Request $request)
    {
        $perPage = $request->get('perPage', 2);

        $comments = Comment::where('appart_uuid', $request->appart_uuid)->where('etat', 'actif')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        return response()->json([
            'data' => $comments->items(),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total()
            ]
        ]);
    }

    public function demoSms(Request $request)
    {

        Log::info("envoie sms");

        $to = '2250789078557';
        $message = 'test sms';
        $response = sendSms($to, $message);

        Log::info("envoie sms 2");

        return response()->json([
            'status' => 'success',
            'to' => $to,
            'message' => $message,
            'api_response' => $response
        ]);
    }


    public function contratPrestataire(Request $request, $email)
    {
        $partner = Partner::where('email', $email)->first();
        $user = User::where('email', $email)->first();
        $pdf = Pdf::loadView('mail.contrat', compact('partner', 'user'));
        // return $pdf->stream('contrat-prestataire.pdf');
        return $pdf->download('fiche_prestation.pdf');
    }
}
