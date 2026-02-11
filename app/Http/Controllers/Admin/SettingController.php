<?php

namespace App\Http\Controllers\Admin;

use App\Models\Variable;
use App\Models\Commodity;
use Illuminate\Support\Str;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function indexCommodity(Request $request)
    {
        $variables = Variable::where(['type'=> 'commodity', 'etat' => 'actif'])->get();

        return view('settings.pages.index', compact('variables'));
    }
    public function indexAppart(Request $request)
    {
        $variables = Variable::where(['type'=> 'type_of_appart','etat' => 'actif'])->get();

        return view('settings.pages.index', compact('variables'));
    }
    public function indexProperty(Request $request)
    {
        $variables = Variable::where(['type'=> 'type_of_property','etat' => 'actif'])->get();

        return view('settings.pages.index', compact('variables'));
    }
    public function indexCategory(Request $request)
    {
        $variables = Variable::where(['type'=> 'category_of_property','etat' => 'actif'])->get();

        return view('settings.pages.index', compact('variables'));
    }

    public function storeVariable(Request $request)
    {


        DB::beginTransaction();
        try{

            $validated = $request->validate([
                'libelle' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'etat' => 'nullable|string|in:actif,inactif',
            ]);

            $saving = Variable::create([
                'uuid' => Str::uuid(),
                'code' => Refgenerate(Variable::class, 'V', 'code'),
                'libelle' => $validated['libelle'],
                'description' => $validated['description'],
                'type' => $request->type,
                'category' => $request->category,
                'etat' => "actif",
            ]);

            if ($saving) {

                $dataResponse =[
                    'type'=>'success',
                    'urlback'=>"back",
                    'message'=>"Enregistré avec succes!",
                    'data'=>$saving,
                    'code'=>200,
                ];
                DB::commit();
            } else {
                DB::rollback();
                $dataResponse =[
                    'type'=>'error',
                    'urlback'=>'',
                    'message'=>"Erreur lors de l'enregistrement!",
                    'code'=>500,
                ];
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse =[
                'type'=>'error',
                'urlback'=>'',
                'message'=>"Erreur systeme! $th",
                'code'=>500,
            ];
        }
        return response()->json($dataResponse);
        
    }

     public function updateVariable(Request $request, $uuid)
    {

        try{
            DB::beginTransaction();

             $variable = Variable::where('uuid', $uuid)->first();

            

            $updating = $variable->update([
                'libelle' => $request->libelle,
                'description' => $request->description,
                'type' => $request->type,
                'category' => $request->category,
            ]);

            if ($updating) {

                $dataResponse =[
                    'type'=>'success',
                    'urlback'=>"back",
                    'message'=>"Mis a jour avec succes!",
                    'data'=>$updating,
                    'code'=>200,
                ];
                DB::commit();
            } else {
                DB::rollback();
                $dataResponse =[
                    'type'=>'error',
                    'urlback'=>'',
                    'message'=>"Erreur lors de la mise a jour!",
                    'code'=>500,
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse =[
                'type'=>'error',
                'urlback'=>'',
                'message'=>"Erreur systeme! $th",
                'code'=>500,
            ];
        }
        return response()->json($dataResponse);
       
    }


    public function destroyVariable(string $uuid)
    {
        
        DB::beginTransaction();
        try {

            $saving= Variable::where('uuid', $uuid)->update([
                'etat' => 'inactif',
            ]);

            if ($saving) {

                $dataResponse =[
                    'type'=>'success',
                    'urlback'=>"back",
                    'message'=>"supprimé avec succes!",
                    'code'=>200,
                ];
                DB::commit();
           } else {
                DB::rollback();
                $dataResponse =[
                    'type'=>'error',
                    'urlback'=>'',
                    'message'=>"Erreur lors de l'enregistrement!",
                    'code'=>500,
                ];
           }

        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse =[
                'type'=>'error',
                'urlback'=>'',
                'message'=>"Erreur systeme! $th",
                'code'=>500,
            ];
        }
        return response()->json($dataResponse);
    }

    // public function faq()
    // {
    //     $externalUploadDir = base_path(env('STORAGE_FILES', '../uploads/'));
    //     $externalDir = $externalUploadDir . 'FAQ/';
    //     $reservationFileUrls = [];
    //     $propertyFileUrls = [];
    //     $appartFileUrls = [];
    //     $paramettreFileUrls = [];

    //     // recuperer tous les fichiers mp4 de chaque dossier
    //     $reservationFiles = glob($externalDir . DIRECTORY_SEPARATOR . 'reservation' . DIRECTORY_SEPARATOR . '*.mp4');
    //     $propertyFiles = glob($externalDir . DIRECTORY_SEPARATOR . 'property' . DIRECTORY_SEPARATOR . '*.mp4');
    //     $appartFiles = glob($externalDir . DIRECTORY_SEPARATOR . 'appart' . DIRECTORY_SEPARATOR . '*.mp4');
    //     $paramettresFiles = glob($externalDir . DIRECTORY_SEPARATOR . 'Paramettres' . DIRECTORY_SEPARATOR . '*.mp4');

    //     // recuperer les fichiers mp4 expliquant le module de reservation
    //     if (!empty($reservationFiles)) {
    //         foreach ($reservationFiles as $file) {
    //             $fileName = basename($file);
    //             $fileUrl = url('storage/files/' . $reservationFiles . DIRECTORY_SEPARATOR . $fileName);
    //             $reservationFileUrls[] = $fileUrl;
    //             $reservationFileUrls[] = $fileName;
    //         }
    //     }

    //     // recuperer les fichiers mp4 expliquant le module gestion des property
    //     if (!empty($propertyFiles)) {
    //         foreach ($propertyFiles as $file) {
    //             $fileName = basename($file);
    //             $fileUrl = url('storage/files/' . $propertyFiles . DIRECTORY_SEPARATOR . $fileName);
    //             $propertyFileUrls[] = $fileUrl;
    //             $propertyFileUrls[] = $fileName;
    //         }
    //     }

    //     // recuperer les fichiers mp4 expliquant le module gestion des hebergement
    //     if (!empty($appartFiles)) {
    //         foreach ($appartFiles as $file) {
    //             $fileName = basename($file);
    //             $fileUrl = url('storage/files/' . $appartFiles . DIRECTORY_SEPARATOR . $fileName);
    //             $appartFileUrls[] = $fileUrl;
    //             $appartFileUrls[] = $fileName;
    //         }
    //     }

    //     // recuperer les fichiers mp4 expliquant le module gestion des parametres
    //     if (!empty($paramettresFiles)) {
    //         foreach ($paramettresFiles as $file) {
    //             $fileName = basename($file);
    //             $fileUrl = url('storage/files/' . $paramettresFiles . DIRECTORY_SEPARATOR . $fileName);
    //             $paramettreFileUrls[] = $fileUrl;
    //             $paramettreFileUrls[] = $fileName;
    //         }
    //     }

    //     dd($reservationFileUrls, $propertyFileUrls, $appartFileUrls, $paramettreFileUrls);
    //     return view('partners.pages.faq', compact('reservationFileUrls', 'propertyFileUrls', 'appartFileUrls', 'paramettreFileUrls'));
    // }

    public function faq()
{
    $externalUploadDir = base_path(env('STORAGE_FILES', '../uploads/'));
    $externalDir = $externalUploadDir . 'FAQ/';

    $reservationFileUrls = [];
    $propertyFileUrls = [];
    $appartFileUrls = [];
    $paramettreFileUrls = [];

    // Récupération des fichiers
    $reservationFiles = glob($externalDir . 'reservation/*.mp4');
    $propertyFiles = glob($externalDir . 'property/*.mp4');
    $appartFiles = glob($externalDir . 'appart/*.mp4');
    $paramettresFiles = glob($externalDir . 'Paramettres/*.mp4');

    // Reservation
    if ($reservationFiles) {
        foreach ($reservationFiles as $file) {
            $fileName = basename($file);
            $fileUrl = url('storage/files/FAQ/reservation/' . $fileName);

            $reservationFileUrls[] = [
                'name' => $fileName,
                'url'  => $fileUrl
            ];
        }
    }

    // Property
    if ($propertyFiles) {
        foreach ($propertyFiles as $file) {
            $fileName = basename($file);
            $fileUrl = url('storage/files/FAQ/property/' . $fileName);

            $propertyFileUrls[] = [
                'name' => $fileName,
                'url'  => $fileUrl
            ];
        }
    }

    // Appart
    if ($appartFiles) {
        foreach ($appartFiles as $file) {
            $fileName = basename($file);
            $fileUrl = url('storage/files/FAQ/appart/' . $fileName);

            $appartFileUrls[] = [
                'name' => $fileName,
                'url'  => $fileUrl
            ];
        }
    }

    // Parametres
    if ($paramettresFiles) {
        foreach ($paramettresFiles as $file) {
            $fileName = basename($file);
            $fileUrl = url('storage/files/FAQ/Paramettres/' . $fileName);

            $paramettreFileUrls[] = [
                'name' => $fileName,
                'url'  => $fileUrl
            ];
        }
    }
    // dd($reservationFileUrls, $propertyFileUrls, $appartFileUrls, $paramettreFileUrls);

    return view('partners.pages.faq', compact(
        'reservationFileUrls',
        'propertyFileUrls',
        'appartFileUrls',
        'paramettreFileUrls'
    ));
}


    // End Commodity


    // property type

    public function propertiesTypes()
    {
       
        $propertiesTypes = PropertyType::all();
        return response()->json([
            'status' => true,
            'message' => 'Liste des type de propriété',
            'propertiesTypes' => $propertiesTypes], 200);
    }

    public function storePropertyType(Request $request)
    {

        // var_dump($request->all());

        DB::beginTransaction();
        try{

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'etat' => 'nullable|string|in:actif,inactif',
            ]);

            $propertyType = PropertyType::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'etat' => "actif",
            ]);

            DB::commit();
            return response()->json(['message' => 'Type de propriété créée avec succès', 'commodity' => $propertyType], 201);

        }catch(\Exception $e){
            DB::rollBack();

            return response()->json(['message' => 'Une erreur s’est produite lors de la création de la commodité.' . $e], 500);
        }
        
    }

     public function updatePropertyType(Request $request, $id)
    {

        try{
            DB::beginTransaction();

             $propertyType = PropertyType::findOrFail($id);

            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            

            $propertyType->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            DB::commit();

            return response()->json(['message' => 'Commodity mise à jour avec succès', 'propertyType' => $propertyType], 201);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Une erreur s’est produite lors de la mise à jour du type de propriété.' . $e], 500);
        }
       
    }

    public function destroyPropertyType($id)
    {

        try{
            DB::beginTransaction();

            $propertiesType = PropertyType::findOrFail($id);
            $propertiesType->delete();

            DB::commit();

            return response()->json(['message' => 'Type de propriété supprimée avec succès', 'propertiesType' => $propertiesType], 201);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => 'Une erreur s’est produite lors de la suppression de la commodité.' . $e], 500);
        }
       

    }


}
