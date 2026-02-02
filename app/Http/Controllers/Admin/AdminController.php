<?php

namespace App\Http\Controllers\Admin;

use App\Models\city;
use App\Models\User;
use App\Models\Partner;
use App\Models\Property;
use App\Models\Variable;
use App\Models\Appartement;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LocationImage;
use App\Imports\CityCountryImport;
use App\Models\PartnershipRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\NotificationPartenaire;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer toutes les demandes
        $partnerships = PartnershipRequest::all();

        // Statistiques principales
        $totalRequests = $partnerships->count();
        $pendingRequests = $partnerships->where('etat', 'pending')->count();
        $activeRequests = $partnerships->where('etat', 'actif')->count();
        $inactiveRequests = $partnerships->where('etat', 'inactif')->count();

        // Préparer les données pour le graphique d'évolution annuelle
        $currentYear = date('Y');
        $monthlyData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthStart = $currentYear . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01';
            $monthEnd = date('Y-m-t', strtotime($monthStart));

            $count = PartnershipRequest::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $monthlyData[] = $count;
        }

        // Répartition par type de propriété
        $propertyTypes = [
            'residential' => 'Résidentiel',
            'commercial' => 'Commercial',
            'industrial' => 'Industriel',
            'land' => 'Terrains',
            'mixed' => 'Mixte'
        ];

        $propertyTypeData = [];
        foreach ($propertyTypes as $key => $label) {
            $propertyTypeData[$label] = $partnerships->where('property_type', $key)->count();
        }

        // Répartition par niveau d'expérience
        $experienceLevels = [
            '0-2' => '0-2 ans',
            '3-5' => '3-5 ans',
            '6-10' => '6-10 ans',
            '10+' => 'Plus de 10 ans'
        ];

        $experienceData = [];
        foreach ($experienceLevels as $key => $label) {
            $experienceData[$label] = $partnerships->where('experience', $key)->count();
        }

        return view('admins.pages.index', compact(
            'partnerships',
            'totalRequests',
            'pendingRequests',
            'activeRequests',
            'inactiveRequests',
            'monthlyData',
            'propertyTypeData',
            'experienceData'
        ));
    }

    public function indexLocation()
    {
        // $locations = city::with('country')->get();
        $locations = City::with('country')
            ->join('properties', 'properties.city', '=', 'cities.code')
            ->select('cities.*')
            ->distinct()
            ->get();
        $images = LocationImage::all()->keyBy('city_code');

        return view('admins.pages.locations.index', compact('locations', 'images'));
    }

    public function storeLocationImage(Request $request)
    {

        $externalUploadDir = base_path(env('STORAGE_FILES'));

        if (!is_dir($externalUploadDir)) {
            mkdir($externalUploadDir, 0777, true);
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = $request->city_code . now()->format('YmdHis') . '.' . $file->extension();

            $fileDirectory = "locations/$request->city_code/";

            $file->move($externalUploadDir . $fileDirectory, $imageName);
        }
        $fileUrl = "storage/files/" . $fileDirectory . $imageName;

        $ville = city::where('code', $request->city_code)->first();

        $saving = LocationImage::updateOrCreate(
            ['city_code' => $request->city_code],
            [
                'country_code' => $ville->country_code,
                'image' => $fileUrl
            ]
        );

        if (!$saving) {
            return response()->json([
                'type' => 'error',
                'urlback' => '',
                'message' => 'Une erreur s\'est produite lors de l\'ajout de l\'image',
                'code' => 500
            ]);
        }

        return response()->json([
            'type' => 'success',
            'urlback' => 'back',
            'message' => 'Image ajoutée avec successe',
            'code' => 200
        ]);
    }

    public function allProprety(Request $request)
    {
        $query = Property::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                // ->orWhere('partner_code', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%');
        }

        // Filtre par date
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [
                $request->date_debut . ' 00:00:00',
                $request->date_fin . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_debut')) {
            $query->where('created_at', '>=', $request->date_debut . ' 00:00:00');
        } elseif ($request->filled('date_fin')) {
            $query->where('created_at', '<=', $request->date_fin . ' 23:59:59');
        }

        // Filtre par état
        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }

        $properties = $query->orderBy('created_at', 'desc')->get();

        $categories = Variable::where(['type'=> 'category_of_property','etat' => 'actif'])->get();

        return view('admins.pages.propreties.viewProperty', compact('properties', 'categories'));
    }

    public function importCityCountry(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);


        Excel::import(new CityCountryImport, $request->file('file'));

        return back()->with('success', 'Importation réussie.');
    }

    public function viewDemande(Request $request)
    {
        $query = PartnershipRequest::query();

        // Filtre par recherche textuelle
        if ($request->filled('search')) {
            $query->where('company', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        // Filtre par date
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [
                $request->date_debut . ' 00:00:00',
                $request->date_fin . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_debut')) {
            $query->where('created_at', '>=', $request->date_debut . ' 00:00:00');
        } elseif ($request->filled('date_fin')) {
            $query->where('created_at', '<=', $request->date_fin . ' 23:59:59');
        }

        // Filtre par état
        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }

        $demandePartenariats = $query->orderBy('created_at', 'ASC')->get();

        return view('admins.pages.demandesPartenariat.validationDemande', compact('demandePartenariats'));
    }

    public function accepterDemande($id)
    {
        DB::beginTransaction();
        try {
            Log::info('Accepter demande de partenariat');
            // Validation de l'ID
            if (!is_numeric($id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'ID invalide'
                ], 400);
            }

            $demande = PartnershipRequest::find($id);
            Log::info('Demande trouve', ['demande' => $demande]);

            // Vérification si la demande existe
            if (!$demande) {
                Log::info('Demande non trouvee donca 404 error');
                return response()->json([
                    'status' => false,
                    'message' => 'Demande non trouvée'
                ], 404);
            }

            // Vérification si la demande n'est pas déjà approuvée
            if ($demande->etat === 'actif') {
                Log::info('Demande deja approuvee donca actif');
                return response()->json([
                    'status' => false,
                    'message' => 'Cette demande a déjà été approuvée'
                ], 400);
            }

            // Mise à jour de l'état
            $demande->etat = 'actif';
            $demande->save();

            $partner_uuid = Str::uuid();


            $partner = Partner::create([
                'uuid' => $partner_uuid,
                'code' => Refgenerate(Partner::class, 'P', 'code'),
                'raison_social' => $demande->company,
                'email' => $demande->email,
                'phone' => $demande->phone,
                'website' => $demande->website,
                'adresse' => $demande->activity_zone,
                'etat' => 'actif',
            ]);

            // Création du partenariat
            $partner_user = User::create([
                'uuid' => Str::uuid(),
                'code' => Refgenerate(User::class, 'U', 'code'),
                'name' => $demande->first_name,
                'lastname' => $demande->last_name,
                'user_type' => 'partner',
                'phone' => $demande->phone,
                'partner_uuid' => $partner_uuid,
                'email' => $demande->email,
                'password' => Hash::make('12345678'),
                'etat' => 'actif',
            ]);

            if ($partner) {
                $nom = $demande->first_name . ' ' . $demande->last_name;

                $emailSubject = "Bienvenue ";
                $message = 'Nous vous recommandons de modifier ce mot de passe après votre première connexion.';

                $emailData = [
                    'nom' => $nom,
                    'email' => $demande->email,
                    'password' => '12345678',
                    'message' => $message,
                    'url' => route('password.request'),
                    'buttonText' => 'Finaliser la création du compte',
                ];

                Mail::to($demande->email)->send(new NotificationPartenaire($emailData, $emailSubject));
            }


            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Demande acceptée avec succès',
                'data' => $demande,
                'partner' => $partner,
                'partner_user' => $partner_user
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => "Une erreur s'est produite lors de la validation de la demande",
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function rejetDemande($id)
    {

        DB::beginTransaction();
        try {

            $demande = PartnershipRequest::findOrFail($id);

            if ($demande->etat === 'inactif') {
                return response()->json([
                    'status' => false,
                    'message' => 'Cette demande a déjà été rejetée'
                ], 400);
            }

            $demande->etat = 'inactif';
            // $demande->date_rejet = now(); // Ajout d'une date de rejet
            $demande->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Demande rejetée avec succès',
                'data' => $demande
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => "Une erreur s'est produite lors du rejet de la demande",
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function approveProperty(Request $request, $uuid)
    {
        DB::beginTransaction();
        try {

            $property = Property::where('uuid', $uuid)->first();

            // Vérification si la demande existe
            if (!$property) {
                Log::info('Propriete non trouvee donca 404 error');
                return response()->json([
                    'type' => 'error',
                    'status' => false,
                    'urlback' => '',
                    'message' => 'Propriété non trouvée'
                ], 404);
            }

            // Vérification si la demande n'est pas déjà approuvée
            // if ($property->etat == 'actif') {
            //     Log::info('Propriete deja approuvee donc actif');
            //     return response()->json([
            //         'type' => 'error',
            //         'urlback' => '',
            //         'status' => false,
            //         'message' => 'Cette propriété a déjà été approuvée'
            //     ], 400);
            // }

            // Mise à jour de l'état
            // foreach($property->apartements->where('etat', '==', 'inactif') as $appart){
            //     $appart->etat = 'actif';
            //     $appart->save();
            // }
            $property->category_uuid = $request->category_uuid;
            $property->etat = 'actif';
            $property->save();

            DB::commit();

            return response()->json([
                'type' => 'success',
                'status' => true,
                'urlback' => 'back',
                'message' => '',
                // 'message' => 'Propriété approuvée avec succès',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'urlback' => '',
                'status' => false,
                'message' => "Une erreur s'est produite",
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function rejectProperty($uuid)
    {
        DB::beginTransaction();
        try {

            $property = Property::where('uuid', $uuid)->first();

            // Vérification si la demande existe
            if (!$property) {
                Log::info('Propriete non trouvee donca 404 error');
                return response()->json([
                    'type' => 'error',
                    'urlback' => '',
                    'status' => false,
                    'message' => 'Propriété non trouvée'
                ], 404);
            }

            // Vérification si la demande n'est pas déjà approuvée
            if ($property->etat === 'inactif') {
                Log::info('Propriete deja approuvee donc inactif');
                return response()->json([
                    'type' => 'error',
                    'status' => false,
                    'urlback' => '',
                    'message' => 'Cette propriété a déjà été rejetée'
                ], 400);
            }

            // Mise à jour de l'état
            foreach ($property->apartements->where('etat', '==', 'actif') as $appart) {
                $appart->etat = 'inactif';
                $appart->save();
            }
            $property->etat = 'inactif';
            $property->save();

            DB::commit();

            return response()->json([
                'type' => 'success',
                'status' => true,
                'urlback' => 'back',
                'message' => 'Propriété rejetée avec succès',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'status' => false,
                'urlback' => '',
                'message' => "Une erreur s'est produite lors du rejet de la propriété",
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function approveAppart($uuid)
    {
        DB::beginTransaction();
        try {

            $appart = Appartement::where('uuid', $uuid)->first();

            // Vérification si la demande existe
            if (!$appart) {
                Log::info('hébergement non trouvee donca 404 error');
                return response()->json([
                    'type' => 'error',
                    'status' => false,
                    'urlback' => '',
                    'message' => 'Hébergement non trouvée'
                ], 404);
            }

            // Vérification si la demande n'est pas déjà approuvée
            if ($appart->etat === 'actif') {
                Log::info('hébergement deja approuvee donc actif');
                return response()->json([
                    'type' => 'error',
                    'urlback' => '',
                    'status' => false,
                    'message' => 'Cette hébergement a déjà été accepter'
                ], 400);
            }

            // Mise à jour de l'état
            $appart->etat = 'actif';
            $appart->save();

            DB::commit();

            return response()->json([
                'type' => 'success',
                'status' => true,
                'urlback' => 'back',
                'message' => 'Hébergement accepté avec succès',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'urlback' => '',
                'status' => false,
                'message' => "Une erreur s'est produite lors de l'acceptation de l'hébergement",
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function rejectAppart($uuid)
    {
        DB::beginTransaction();
        try {

            $appart = Appartement::where('uuid', $uuid)->first();

            // Vérification si la demande existe
            if (!$appart) {
                Log::info('Hébergement non trouvee donca 404 error');
                return response()->json([
                    'type' => 'error',
                    'urlback' => '',
                    'status' => false,
                    'message' => 'Hébergement non trouvée non trouvée'
                ], 404);
            }

            // Vérification si la demande n'est pas déjà approuvée
            if ($appart->etat === 'inactif') {
                Log::info('Hébergement deja rejete donc inactif');
                return response()->json([
                    'type' => 'error',
                    'status' => false,
                    'urlback' => '',
                    'message' => 'Cet hébergement a déjà été rejeté'
                ], 400);
            }

            // Mise à jour de l'état
            $appart->etat = 'inactif';
            $appart->save();

            DB::commit();

            return response()->json([
                'type' => 'success',
                'status' => true,
                'urlback' => 'back',
                'message' => 'Hébergement rejeté avec succès',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'status' => false,
                'urlback' => '',
                'message' => "Une erreur s'est produite lors du rejet de l'hébergement",
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
