<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\city;
use App\Models\Search;
use App\Models\Partner;
use App\Models\receipt;
use App\Models\Variable;
use App\Models\Appartement;
use App\Models\Reservation;
use Illuminate\Support\Str;
use App\Models\Tarification;
use Illuminate\Http\Request;
use  Barryvdh\DomPDF\Facade\PDF;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ReconductedReservation;

class ReconductionController extends Controller
{

    public function reconduiReservation(Request $request, $reservation_uuid)
    {
        // ⚙️ Paramètres de pagination et de requête
        $perPage = $request->get('perPage', 6);
        $reservation = Reservation::where('uuid', $reservation_uuid)->firstOrFail();

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
//  $apparts = Appartement::->paginate($perPage);
        // Pagination des appartements
        $apparts = $query->whereHas('tarifications', function ($query) use ($reservation) {
                $query->where('etat', 'actif')
                    ->where('price', '>=', $reservation->total_price);
            })
            ->with(['tarifications' => function ($query) use ($reservation) {
                $query->where('etat', 'actif')
                    ->where('price', '>=', $reservation->total_price);
            }])->paginate($perPage);
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
                    $appart->property->setAttribute('commune_name',  $location['commune']);
                }
            }
        }

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
        $typeAppart = Variable::where(['type' => 'type_of_appart', 'etat' => 'actif'])->get();

        return view(
            'reservations.reconduction.index',
            compact('reservation', 'apparts', 'minPrice', 'maxPrice', 'categories', 'cities', 'commodities', 'typeAppart')
        );
    }
    // public function reconduiReservation(Request $request, $reservation_uuid)
    // {
    //     // ⚙️ Paramètres de pagination et de requête
    //     $perPage = $request->get('perPage', 6);
    //     $reservation = Reservation::where('uuid', $reservation_uuid)->firstOrFail();

    //     $apparts = Appartement::where('etat', 'actif')
    //         ->whereHas('tarifications', function ($query) use ($reservation) {
    //             $query->where('etat', 'actif')
    //                 ->where('price', '>=', $reservation->total_price);
    //         })
    //         ->with(['tarifications' => function ($query) use ($reservation) {
    //             $query->where('etat', 'actif')
    //                 ->where('price', '>=', $reservation->total_price);
    //         }])->paginate($perPage);;

    //     return view(
    //         'reservations.reconduction.index',
    //         compact('reservation', 'apparts')
    //     );
    // }

    public function show($uuid, $reservation_uuid)
    {

        $appart = Appartement::where('uuid', $uuid)->firstOrFail();
        $reservationOld = Reservation::where('uuid', $reservation_uuid)->firstOrFail();
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
                    $appart->property->setAttribute('commune_name',  $location['commune']);
                }
            }
        return view('reservations.reconduction.show', compact('appart', 'reservationOld'));
    }

    public function store(Request $request)
    {

        Log::info('request', ['request' => $request->all()]);
        DB::beginTransaction();
        try {
            // Génération du code unique
            $code = 'RES-' . strtoupper(Str::random(6));
            $start_time = Carbon::parse($request->start_time)->format('Y-m-d H:i:s');
            $end_time = $request->end_time
                ? Carbon::parse($request->end_time)->format('Y-m-d H:i:s')
                : null;
            
            $visitUuid = session('visit_uuid');

            $reservationOld = Reservation::where('uuid', $request->reservation_old_uuid)->first();
            $reservationOld->update([
                'status' => 'reconducted',
                // 'statut_paiement' => 'cancelled',
            ]);

            $still_to_pay = (float) $request->totalPrice - (float) $request->paymentAmount;
            // Création de la réservation
            $reservation = Reservation::create([
                'uuid' => Str::uuid(),
                'code' => $code,
                'visit_uuid' => $visitUuid,
                'nom' => $request->nom,
                'prenoms' => $request->prenoms,
                'email' => $request->email,
                'phone' => $request->phone,
                'appart_uuid' => $request->appart_uuid,
                'property_uuid' => $request->property_uuid,
                'partner_uuid' => $request->partner_uuid,
                'sejour' => $request->isHourly ? 'Heure' : 'Jour',
                'start_time' => $start_time,
                'end_time' => $end_time,
                'nbr_of_sejour' => $request->isHourly ? $request->hours : $request->days,
                'total_price' => $request->totalPrice,
                'unit_price' => $request->unitPrice,
                'still_to_pay' => $still_to_pay,
                'statut_paiement' => 'pending',
                'status' => 'pending',
                'notes' => $request->notes,
                'payment_method' => $request->payment_method,
                'payment_amount' => $request->paymentAmount
            ]);
            if ($reservation) {   
                // Création de la reconduction
                ReconductedReservation::create([
                    'uuid' => Str::uuid(),
                    'original_reservation_uuid' => $reservationOld->uuid,
                    'old_appart_uuid' => $reservationOld->appart_uuid,
                    'old_total_price' => $reservationOld->total_price,
                    'already_paid' => $reservationOld->payment_amount,
                    'new_reservation_uuid' => $reservation->uuid,
                    'new_appart_uuid' => $reservation->appart_uuid,
                    'new_total_price' => $reservation->total_price,
                    'remaining_to_pay' => $still_to_pay,
                    'amount_to_pay_now' => $request->paymentAmount,
                ]);

                // Génération du PDF après enregistrement
                $pdfUrl = $this->generateReceiptPDF($reservation);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Réservation enregistrée avec succès',
                'reservation' => $reservation,
                'pdf_url' => $pdfUrl
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement',
                'error' => $e->getMessage()
            ], 500);
        }


    }

    private function generateReceiptPDF($reservation)
    {
        $directory = 'receipts';
        $externalUploadDir = base_path(env('STORAGE_FILES', '../uploads/'));

        // Créer le dossier s'il n'existe pas
        if (!is_dir($externalUploadDir . $directory)) {
            mkdir($externalUploadDir . $directory, 0755, true);
        }

        $data = [
            'reservation' => $reservation,
            'date' => now()->format('d/m/Y H:i')
        ];

        $pdf = PDF::loadView('reservations.receipt', $data);

        $filename = 'Recu_' . $reservation->code . '_' . $reservation->uuid . '.pdf';
        $filePath = $externalUploadDir . $directory . '/' . $filename;

        $pdf->save($filePath);

        // Enregistrer dans la table receipts
        receipt::create([
            'uuid' => Str::uuid(),
            'code' => 'REC-' . strtoupper(Str::random(6)),
            'reservation_uuid' => $reservation->uuid,
            'filename' => $filename,
            'filepath' => "storage/files/{$directory}/{$filename}"
        ]);

        return "storage/files/{$directory}/{$filename}";
    }
  
}