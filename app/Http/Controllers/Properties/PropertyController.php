<?php

namespace App\Http\Controllers\Properties;

use App\Models\city;
use App\Models\country;
use App\Models\Property;
use App\Models\Variable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 8);
        $query = Property::query();

        if($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('partner_uuid', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%');
        }

        // Filtre par date
        if($request->filled('date_debut') && $request->filled('date_fin')) {
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
        if($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }

        $properties = $query->where('partner_uuid', Auth::user()->partner_uuid)->orderBy('created_at', 'desc')->get();
        $propertiesForSmallDevice = $query->where('partner_uuid', Auth::user()->partner_uuid)->orderBy('created_at', 'desc')->paginate($perPage);
        // $properties = Property::where('partner_code', Auth::user()->email)->get();
        return view('properties.index', compact('properties', 'propertiesForSmallDevice'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = country::all();
        $variables = Variable::where(['type'=> 'type_of_property','etat' => 'actif'])->get();
        return view('properties.create', compact('countries', 'variables'));
    }

    // api pour recuperer la liste des ville par pays
    public function getCities(Request $request)
    {
        $countryCode = $request->input('country');
        $cities = city::where('country_code', $countryCode)->get();
        return response()->json($cities);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            $externalUploadDir = base_path(env('STORAGE_FILES'));
                
            if (!is_dir($externalUploadDir)) {
                mkdir($externalUploadDir, 0777, true);
            }

            $code = RefgenerateCode(Property::class, 'PROP', 'code');
            $uuid = Str::uuid();
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = $code. now()->format('YmdHis').$uuid.'.'.$file->extension();

                $fileDirectory = 'properties_'.$code.'/';

                $file->move($externalUploadDir.$fileDirectory, $imageName);
            }
            $fileUrl = "storage/files/" . $fileDirectory. $imageName;
            if($request->city == 'autre'){
                $cityLabel = $request->cityAutre;
                $cityCode = Str::slug($cityLabel) . '-' . Str::random(3) . '-' . $request->country;
                $cityModel = city::firstOrCreate([
                    'label' => $cityLabel,
                    'country_code' => $request->country
                ],
                [
                    'code' => $cityCode
                ]);
                $city = $cityModel->code;
            }else{
                $city = $request->city;
            }
            $property = Property::create(
                [ 
                    'uuid' => $uuid,
                    'code' => $code,
                    'partner_uuid' => $request->partner_uuid,
                    'image' => $fileUrl,
                    'title' => $request->title,
                    'type_uuid' => $request->type_uuid,
                    'address' => $request->address,
                    'country' => $request->country,
                    'city' => $city,
                    'longitude' => $request->longitude,
                    'latitude' => $request->latitude,
                    'description' => $request->description,
                    'created_by' => $request->user_uuid,
                    'updated_by' => $request->user_uuid,
                ]
            );
            DB::commit();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Propriété créee avec succès.',
                    'property' => $property
                ],
                201
            );

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Une erreur s’est produite lors de la création de la propriété.' . $e], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $property = Property::where('uuid', $uuid)
        ->with(['apartements.tarifications']) // eager load
        ->first();
        $categories = Variable::where(['type'=> 'category_of_property','etat' => 'actif'])->get();
        return view('properties.show', compact('property', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $countries = country::all();
        $variables = Variable::where(['type'=> 'type_of_property','etat' => 'actif'])->get();
        $property = Property::where('uuid', $uuid)->first();
        return view('properties.edit', compact('property', 'countries', 'variables'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $uuid)
    {
        DB::beginTransaction();
        try {
            $externalUploadDir = base_path(env('STORAGE_FILES'));
                
            if (!is_dir($externalUploadDir)) {
                mkdir($externalUploadDir, 0777, true);
            }
            $property = Property::where('uuid', $uuid)->first();
            $code = $property->code;
            $mainFileUrl = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = $code . now()->format('YmdHis') .$uuid . '.' . $file->extension();
                $mainFileDirectory = 'properties_' . $code . '/';
                $file->move($externalUploadDir . $mainFileDirectory, $imageName);
                // $file->move(public_path('media/properties_' . $property_code . '/apparts_' . $code), $imageName);
                $mainFileUrl = "storage/files/" . $mainFileDirectory. $imageName;
            }
            if($request->city == 'autre'){
                $cityLabel = $request->cityAutre;
                $cityCode = Str::slug($cityLabel) . '-' . Str::random(3) . '-' . $request->country;
                $cityModel = city::firstOrCreate([
                    'label' => $cityLabel,
                    'country_code' => $request->country
                ],
                [
                    'code' => $cityCode
                ]);
                $city = $cityModel->code;
            }else{
                $city = $request->city;
            }
            $isUpdated = $property->update([
                'title' => $request->title,
                'type_uuid' => $request->type_uuid,
                'address' => $request->address,
                'country' => $request->country,
                'city' => $city,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'description' => $request->description,
                'image' => ($mainFileUrl != null) ? $mainFileUrl : $property->image,
                'updated_by' => $request->user_uuid,
            ]);
            
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Propriété mise à jour avec succès.',
                'property' => $property->fresh() // Recharger les données fraîches
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => "Une erreur s’est produite lors de la création de l'appartement." . $e
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $property = Property::where('uuid', $uuid)->first();

        foreach($property->apartements->where('etat', '!=', 'inactif') as $appart){

            foreach($appart->tarifications->where('etat', '!=', 'inactif') as $tarif){
                // $tarif->etat = 'inactif';
                $tarif->delete();
            }

            foreach($appart->images->where('etat', '!=', 'inactif') as $appartDoc){
                // $appartDoc->etat = 'inactif';
                $appartDoc->delete();
            }
            // $appart->etat = 'inactif';
            // $appart->deleted_at = now();
            $appart->delete();  
        }
        // $property->etat = 'inactif';
        // $property->deleted_at = now();
        $isDeleted = $property->delete();

        if (!$isDeleted) {
            return response()->json([
                'status' => false,
                'message' => 'Une erreur s’est produite lors de la suppression de la propriété.'
            ], 500);
        }else {
            return response()->json([
                'status' => true,
                'message' => 'propriété supprimée avec succès.'
            ], 200);
        }
    }
}
