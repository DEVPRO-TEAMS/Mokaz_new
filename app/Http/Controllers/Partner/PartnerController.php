<?php

namespace App\Http\Controllers\Partner;

use App\Models\User;
use App\Models\Partner;
use App\Models\Property;
use App\Models\Appartement;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PartnershipRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\NotificationPartenaire;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partner = auth()->user()->partner;

        $partnerUsers = User::where('partner_uuid', $partner->uuid)->get();

        // Récupération des propriétés actives du partenaire
        $partnerProperties = Property::where('partner_uuid', $partner->uuid)
            ->get();

        // Récupération des UUID des propriétés
        $propertyUuids = $partnerProperties->pluck('uuid');

        // Récupération des appartements actifs associés à ces propriétés
        $partnerPropertyApartments = Appartement::whereIn('property_uuid', $propertyUuids)
            ->get();

        // Récupération des reservations actives associées aux appartements
        $reservations = Reservation::whereIn('appart_uuid', $partnerPropertyApartments->pluck('uuid'))
            ->get();
        return view('partners.dashboard', compact(
            'partnerUsers',
            'partnerProperties',
            'partnerPropertyApartments',
            'reservations'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function partners(Request $request)
    {
        
        $query = Partner::query();

        if ($request->filled('search')) {
            $query->where('raison_social', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('adresse', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        }
        $partners = $query->get();
        return view('partners.pages.index', compact('partners'));
    }
    public function partnersCollaborator()
    {
        $partner_uuid = auth()->user()->partner_uuid;
        $collaborators = User::where('partner_uuid', $partner_uuid)->get();
        return view('partners.collaborators.index', compact('collaborators'));
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {


            $partnership = PartnershipRequest::create(
                [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'company' => $request->company,
                    'property_type' => $request->property_type,
                    'activity_zone' => $request->activity_zone,
                    'experience' => $request->experience,
                    'portfolio_size' => $request->portfolio_size,
                    'website' => $request->website,
                    'message' => $request->message,
                    'accepts_newsletter' => $request->accepts_newsletter,
                    'etat' => 'pending'
                ]
            );

            DB::commit();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Partnership request created successfully.',
                    'partnership' => $partnership
                ],
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Une erreur s’est produite lors de la création de la partenariat.' . $e
            ], 500);
        }
    }

    public function storePartner(Request $request)
    {
        DB::beginTransaction();
        try {
            $partner_uuid = Str::uuid();

            $partner = Partner::create([
                'uuid' => $partner_uuid,
                'code' => Refgenerate(Partner::class, 'P', 'code'),
                'raison_social' => $request->raison_social,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'adresse' => $request->activity_zone,
                'etat' => 'actif',
            ]);

            if ($partner) {
                $user = User::create([
                    'uuid' => Str::uuid(),
                    'code' => Refgenerate(User::class, 'U', 'code'),
                    'name' => $request->contact_name,
                    'lastname' => $request->contact_lastname,
                    'user_type' => 'partner',
                    'phone' => $request->phone,
                    'partner_uuid' => $partner_uuid,
                    'email' => $request->contact_email,
                    'password' => Hash::make('12345678'),
                    'etat' => 'actif',
                ]);
            }

            if ($partner) {
                $nom = $request->contact_name . ' ' . $request->contact_lastname;

                $emailSubject = "Bienvenue ";
                $message = 'Nous vous recommandons de modifier ce mot de passe après votre première connexion.';

                $emailData = [
                    'nom' => $nom,
                    'email' => $request->contact_email,
                    'password' => '12345678',
                    'message' => $message,
                    'url' => env('APP_URL') . '/login',
                    'buttonText' => 'Finaliser la création du compte',
                ];

                Mail::to($request->contact_email)->send(new NotificationPartenaire($emailData, $emailSubject));
            }

            DB::commit();

            if ($partner) {

                $dataResponse = [
                    'type' => 'success',
                    'urlback' => "back",
                    'message' => "Enregistré avec succes!",
                    'data' => $partner,
                    'code' => 200,
                ];
                DB::commit();
            } else {
                DB::rollback();
                $dataResponse = [
                    'type' => 'error',
                    'urlback' => '',
                    'message' => "Erreur lors de l'enregistrement!",
                    'code' => 500,
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse = [
                'type' => 'error',
                'urlback' => '',
                'message' => "Erreur systeme! $th",
                'code' => 500,
            ];
        }
        return response()->json($dataResponse);
    }


    public function addCollaborator(Request $request)
    {
        DB::beginTransaction();
        try {
            $partner_uuid = auth()->user()->partner_uuid;

            $isExist = User::where('email', $request->contact_email)->first();
            if ($isExist) {
                $dataResponse = [
                    'type' => 'error',
                    'urlback' => '',
                    'message' => "Utilisateur avec cet email existe deja !",
                    'code' => 500,
                ];
            }else {
                $user = User::create([
                    'uuid' => Str::uuid(),
                    'code' => Refgenerate(User::class, 'U', 'code'),
                    'name' => $request->contact_name,
                    'lastname' => $request->contact_lastname,
                    'user_type' => 'partner',
                    'phone' => $request->phone,
                    'partner_uuid' => $partner_uuid,
                    'email' => $request->contact_email,
                    'password' => Hash::make('12345678'),
                    'etat' => 'actif',
                ]);
    
                if ($user) {
                    $nom = $request->contact_name . ' ' . $request->contact_lastname;
    
                    $emailSubject = "Bienvenue ";
                    $message = 'Nous vous recommandons de modifier ce mot de passe après votre première connexion.';
    
                    $emailData = [
                        'nom' => $nom,
                        'email' => $request->contact_email,
                        'password' => '12345678',
                        'message' => $message,
                        'url' => env('APP_URL') . '/login',
                        'buttonText' => 'Finaliser la création du compte',
                    ];
    
                    Mail::to($request->contact_email)->send(new NotificationPartenaire($emailData, $emailSubject));
                }
    
                DB::commit();
    
                if ($user) {
    
                    $dataResponse = [
                        'type' => 'success',
                        'urlback' => "back",
                        'message' => "Enregistré avec succes!",
                        'data' => $user,
                        'code' => 200,
                    ];
                    DB::commit();
                } else {
                    DB::rollback();
                    $dataResponse = [
                        'type' => 'error',
                        'urlback' => '',
                        'message' => "Erreur lors de l'enregistrement!",
                        'code' => 500,
                    ];
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse = [
                'type' => 'error',
                'urlback' => '',
                'message' => "Erreur systeme! $th",
                'code' => 500,
            ];
        }
        return response()->json($dataResponse);
    }

    /**
     * Display the specified resource.
     */
    public function showPartner(string $uuid)
    {
        $partner = Partner::where('uuid', $uuid)->first();

        $uuidPluck = $partner->properties()->pluck('uuid')->toArray();

        $appart = Appartement::whereIn('property_uuid', $uuidPluck)->get();

        $appartUuid = $appart->pluck('uuid')->toArray();

        $reservations = Reservation::where('partner_uuid', $uuid)->get();
        // dd($reservations);
        // $reservations = Reservation::whereIn('appart_uuid', $appartUuid)->get();

        return view('partners.pages.show', compact('partner', 'appart', 'reservations'));
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
    public function updatePartner(Request $request, string $uuid)
    {

        DB::beginTransaction();
        try {

            $partner = Partner::where('uuid', $uuid)->update([
                'raison_social' => $request->raison_social,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'adresse' => $request->adresse,
            ]);

            DB::commit();

            if ($partner) {

                $dataResponse = [
                    'type' => 'success',
                    'urlback' => "back",
                    'message' => "Mise a jour avec succes!",
                    'data' => $partner,
                    'code' => 200,
                ];
                DB::commit();
            } else {
                DB::rollback();
                $dataResponse = [
                    'type' => 'error',
                    'urlback' => '',
                    'message' => "Erreur lors de la mise a jour!",
                    'code' => 500,
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse = [
                'type' => 'error',
                'urlback' => '',
                'message' => "Erreur systeme! $th",
                'code' => 500,
            ];
        }
        return response()->json($dataResponse);
    }

    public function updateCollaborator(Request $request, string $uuid)
    {

        DB::beginTransaction();
        try {

            $collaborator = User::where('uuid', $uuid)->update([
                'name' => $request->name,
                'lastname' => $request->lastname,
                'phone' => $request->phone,
            ]);

            DB::commit();

            if ($collaborator) {

                $dataResponse = [
                    'type' => 'success',
                    'urlback' => "back",
                    'message' => "Mise a jour avec succes!",
                    'data' => $collaborator,
                    'code' => 200,
                ];
                DB::commit();
            } else {
                DB::rollback();
                $dataResponse = [
                    'type' => 'error',
                    'urlback' => '',
                    'message' => "Erreur lors de la mise a jour!",
                    'code' => 500,
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse = [
                'type' => 'error',
                'urlback' => '',
                'message' => "Erreur systeme! $th",
                'code' => 500,
            ];
        }
        return response()->json($dataResponse);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyPartner(string $uuid)
    {
        DB::beginTransaction();
        try {
            $partner = Partner::where('uuid', $uuid)->update([
                'etat' => 'inactif',
            ]);

            DB::commit();

            if ($partner) {

                $dataResponse = [
                    'type' => 'success',
                    'urlback' => "back",
                    'message' => "Supprimé avec succes!",
                    'data' => $partner,
                    'code' => 200,
                ];
                DB::commit();
            } else {
                DB::rollback();
                $dataResponse = [
                    'type' => 'error',
                    'urlback' => '',
                    'message' => "Erreur lors de la suppression!",
                    'code' => 500,
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse = [
                'type' => 'error',
                'urlback' => '',
                'message' => "Erreur systeme! $th",
                'code' => 500,
            ];
        }
        return response()->json($dataResponse);
    }

    public function destroyCollaborator(string $uuid)
    {
        DB::beginTransaction();
        try {
            $collaborators = User::where('uuid', $uuid)->delete();
            DB::commit();

            if ($collaborators) {

                $dataResponse = [
                    'type' => 'success',
                    'urlback' => "back",
                    'message' => "Supprimé avec succes!",
                    'data' => $collaborators,
                    'code' => 200,
                ];
                DB::commit();
            } else {
                DB::rollback();
                $dataResponse = [
                    'type' => 'error',
                    'urlback' => '',
                    'message' => "Erreur lors de la suppression!",
                    'code' => 500,
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse = [
                'type' => 'error',
                'urlback' => '',
                'message' => "Erreur systeme! $th",
                'code' => 500,
            ];
        }
        return response()->json($dataResponse);
    }
}
