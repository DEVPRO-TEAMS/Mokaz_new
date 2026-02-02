<?php

use App\Models\Visit;
use App\Models\PageView;
use Illuminate\Http\Request;
use App\Models\VisitHistorique;
use App\Models\PageViewHistorique;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ReconductionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Partner\PartnerController;
use App\Http\Controllers\User\ReservationController;
use App\Http\Controllers\Comment\TestimoniController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Properties\AppartController;
use App\Http\Controllers\Properties\PropertyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// })->name('welcome');

Auth::routes();

Route::get('storage/files/{file}', function ($file) {
    $path = base_path(env('STORAGE_FILES') . $file);

    if (!file_exists($path)) {
        abort(404);
    }

    $fileContents = file_get_contents($path);
    $mimeType = mime_content_type($path);

    return Response::make($fileContents, 200, ['Content-Type' => $mimeType]);
})->where('file', '.*');

Route::get('/', [PagesController::class, 'index'])->name('welcome');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// route de navigation
Route::get('/apropos', [PagesController::class, 'indexApropos'])->name('apropos');
Route::get('/appart-by-property/{uuid}', [PagesController::class, 'appartByProperty'])->name('appart.by.property');
Route::get('/all-apparts', [PagesController::class, 'allApparts'])->name('appart.all');
Route::get('/reservation', [PagesController::class, 'indexReservations'])->name('reservation');
Route::get('/contact', [PagesController::class, 'indexContact'])->name('contact');
Route::get('/faq', [PagesController::class, 'indexFaq'])->name('faq');
Route::get('/confidentialite', [PagesController::class, 'indexPolitiq'])->name('politiq');
Route::get('/detail/appartement/{uuid}', [PagesController::class, 'show'])->name('appart.detail.show');

Route::get('/reservation/paiement-waiting/{reservation_uuid}', [ReservationController::class, 'paiementWaiting'])->name('reservation.paiement.waiting');
Route::get('/reservation/detail/{reservation_uuid}', [ReservationController::class, 'reservationDetail'])->name('reservation.detail');


Route::get('/reservation/paiement-success/{reservation_uuid}', [ReservationController::class, 'paiementSuccess'])->name('reservation.paiement.success');
Route::get('/reservation/paiement-failed/{reservation_uuid}', [ReservationController::class, 'paiementFailed'])->name('reservation.paiement.failed');


Route::get('/contrat-prestataire/{email}', [PagesController::class, 'contratPrestataire'])->name('contratPrestataire');




Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest', 'PreventBackHistory')->group(function () {});
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/import-city-country', [AdminController::class, 'importCityCountry'])->name('import.city.country');

        Route::get('/dashboard', [AdminController::class, 'index'])->name('index');
        Route::get('/statistics', [DashboardController::class, 'index'])->name('statistics');

        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');

        // validation de demande de partenariat 
        Route::get('/demande/list', [AdminController::class, 'viewDemande'])->name('demande.view');

        Route::get('/indexLocation', [AdminController::class, 'indexLocation'])->name('indexLocation');
        Route::post('/storeLocationImage', [AdminController::class, 'storeLocationImage'])->name('storeLocationImage');

        Route::get('/allProprety/list', [AdminController::class, 'allProprety'])->name('proprety.view');
        Route::get('/property/show/{uuid}', [PropertyController::class, 'show'])->name('properties.show');
        // approve et refuser une propriete 
        Route::post('/approveProperty/{uuid}', [AdminController::class, 'approveProperty'])->name('approveProperty');
        Route::post('/rejectProperty/{uuid}', [AdminController::class, 'rejectProperty'])->name('rejectProperty');

        // approve et refuser une appartement 
        Route::post('/approveAppart/{uuid}', [AdminController::class, 'approveAppart'])->name('approveAppart');
        Route::post('/rejectAppart/{uuid}', [AdminController::class, 'rejectAppart'])->name('rejectAppart');

        Route::get('/partner/list', [PartnerController::class, 'partners'])->name('partner.index');
        Route::post('/partner/add', [PartnerController::class, 'storePartner'])->name('storePartner');
        Route::post('/partner/update/{uuid}', [PartnerController::class, 'updatePartner'])->name('updatePartner');
        Route::post('/partner/destroy/{uuid}', [PartnerController::class, 'destroyPartner'])->name('destroyPartner');
        Route::get('/partner/show/{uuid}', [PartnerController::class, 'showPartner'])->name('showPartner');

        Route::get('/partner/property/show/{uuid}', [PropertyController::class, 'show'])->name('properties.show');
        // user 
        Route::post('/update/user/{uuid}', [UserController::class, 'update'])->name('user.update');
        Route::post('/destroy/user/{uuid}', [UserController::class, 'destroy'])->name('user.destroy');

        // reservation
        Route::get('/reservation/index', [ReservationController::class, 'index'])->name('reservation.index');
        Route::get('/reservation/show/{uuid}', [ReservationController::class, 'show'])->name('reservation.show');

        // Comment route 
        Route::get('/comment/index', [CommentController::class, 'index'])->name('comment.index');
        // Route::get('/comment/show/{uuid}', [CommentController::class, 'show'])->name('comment.show');
        Route::post('/approveComment/{uuid}', [CommentController::class, 'approveComment'])->name('approveComment');
        Route::post('/rejectComment/{uuid}', [CommentController::class, 'rejectComment'])->name('rejectComment');

        // Testimonial route 
        Route::get('/testimonial/index', [TestimoniController::class, 'index'])->name('testimonial.index');
        Route::post('/testimonial/add', [TestimoniController::class, 'store'])->name('store.testimonial');
        Route::post('/testimonial/update/{uuid}', [TestimoniController::class, 'update'])->name('update.testimonial');
        Route::post('/testimonial/destroy/{uuid}', [TestimoniController::class, 'destroy'])->name('destroy.testimonial');
    });
});

Route::prefix('user')->name('user.')->group(function () {
    Route::middleware('guest', 'PreventBackHistory')->group(function () {});
    Route::middleware(['auth', 'user'])->group(function () {
        Route::get('/dashboard', [UserController::class, 'index'])->name('index');
        Route::post('/update/user/{uuid}', [UserController::class, 'updateUser'])->name('updateUser');
        Route::post('/destroy/user/{uuid}', [UserController::class, 'destroy'])->name('destroy');
    });
});

Route::prefix('partner')->name('partner.')->group(function () {
    Route::middleware('guest', 'PreventBackHistory')->group(function () {
        Route::post('demande/store', [PartnerController::class, 'store']);
    });
    Route::middleware(['auth', 'partner'])->group(function () {
        Route::get('/dashboard', [PartnerController::class, 'index'])->name('index');
        // Route::get('/dashboard', [PartnerController::class, 'index'])->name('index');

        // Route property
        Route::get('/my-properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/property/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::get('/property/edit/{uuid}', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::get('/property/show/{uuid}', [PropertyController::class, 'show'])->name('properties.show');

        // Route reservation
        Route::get('/reservation/index', [ReservationController::class, 'index'])->name('reservation.index');
        Route::get('/reservation/show/{uuid}', [ReservationController::class, 'show'])->name('reservation.show');
        Route::post('/reservation/confirm/{uuid}', [ReservationController::class, 'confirmReservation'])->name('reservation.confirm');

        // Route::post('/appart/add', [AppartController::class, 'store'])->name('apartments.store');
        // Route apparts
        Route::get('/appart/create-in-property/{uuid}', [AppartController::class, 'create'])->name('apartments.create');
        Route::get('/appart/edit-in-property/{uuid}/{property_uuid}', [AppartController::class, 'edit'])->name('apartments.edit');


        // Comment route 
        Route::get('/comment/index', [CommentController::class, 'index'])->name('comment.index');
        // Route::get('/comment/show/{uuid}', [CommentController::class, 'show'])->name('comment.show');
        Route::post('/approveComment/{uuid}', [CommentController::class, 'approveComment'])->name('approveComment');
        Route::post('/rejectComment/{uuid}', [CommentController::class, 'rejectComment'])->name('rejectComment');


        // collaborator route
        Route::get('/partner/collaborator/list', [PartnerController::class, 'partnersCollaborator'])->name('collaborator.index');
        Route::post('/partner/collaborator/add', [PartnerController::class, 'addCollaborator'])->name('collaborator.store');
        Route::post('/partner/collaborator/update/{uuid}', [PartnerController::class, 'updateCollaborator'])->name('collaborator.update');
        Route::post('/partner/collaborator/destroy/{uuid}', [PartnerController::class, 'destroyCollaborator'])->name('destroyCollaborator');
    });

    // addCollaborator
});


Route::prefix('com-manager')->name('comManager.')->group(function () {
    Route::middleware('guest', 'PreventBackHistory')->group(function () {});
    Route::middleware(['auth', 'comManager'])->group(function () {
        Route::get('/statistics', [DashboardController::class, 'index'])->name('statistics');
    });
});


Route::prefix('setting')->name('setting.')->group(function () {
    Route::middleware('guest', 'PreventBackHistory')->group(function () {});
    Route::middleware(['auth'])->group(function () {
        Route::get('/index/commodity', [SettingController::class, 'indexCommodity'])->name('indexCommodity');
        Route::get('/index/appart', [SettingController::class, 'indexAppart'])->name('indexAppart');
        Route::get('/index/property', [SettingController::class, 'indexProperty'])->name('indexProperty');
        Route::get('/index/category', [SettingController::class, 'indexCategory'])->name('indexCategory');
        Route::post('/variable/store', [SettingController::class, 'storeVariable'])->name('storeVariable');
        Route::post('/variable/update/{uuid}', [SettingController::class, 'updateVariable'])->name('updateVariable');
        Route::post('/variable/destroy/{uuid}', [SettingController::class, 'destroyVariable'])->name('destroyVariable');

        // type de variable

    });
});

// Reconduction
Route::prefix('reconduction')->group(function () {
    Route::get('/reservation/{reservation_uuid}', [ReconductionController::class, 'reconduiReservation'])->name('reservation.reconduction');
});
    Route::get('/reconduction/show/{uuid}/{reservation_uuid}', [ReconductionController::class, 'show'])->name('reconduction.show');



// Route::post('/track/page-duration', function (Request $request) {

//     $uuid = $request->input('historique_uuid');

//     if (!$uuid) {
//         Log::warning('historique_uuid manquant : Page historique');
//         return response()->json(['status' => false]);
//     }

//     $historique = PageViewHistorique::where('uuid', $uuid)
//         ->whereNull('ended_at')
//         ->first();

//     if (!$historique) {
//         Log::info('Historique déjà fermé ou introuvable : Page historique ', ['uuid' => $uuid]);
//         return response()->json(['status' => true]);
//     }

//     $historique->update([
//         'ended_at' => now(),
//         'duration' => now()->diffInSeconds($historique->started_at),
//     ]);

//     Log::info('Durée page enregistrée pour page historique', [
//         'uuid' => $uuid,
//         'duration' => $historique->duration
//     ]);

//     return response()->json(['status' => true]);

// })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);



// Route::post('/track/visit-end', function (Request $request) {
//     $uuid = $request->input('visit_historique_uuid');

//     if (!$uuid) {
//         Log::warning('visit_historique_uuid manquant');
//         return response()->json(['status' => false]);
//     }

//     $historique = VisitHistorique::where('uuid', $uuid)
//         ->whereNull('ended_at')
//         ->first();

//     if ($historique) {
//         $historique->update([
//             'ended_at' => now(),
//             'duration' => now()->diffInSeconds($historique->started_at),
//         ]);
//         Log::info('Session fermée avec succès', ['visit_historique_uuid' => $uuid]);
//     }

//     // Nettoyer la session côté serveur
//     session()->forget(['visit_uuid', 'visit_historique_uuid', 'last_activity']);

//     return response()->json(['status' => true]);
// })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


// Route::post('/track/heartbeat', function (Request $request) {
//     $uuid = $request->input('visit_historique_uuid');

//     if (!$uuid) {
//         return response()->json(['status' => false]);
//     }

//     VisitHistorique::where('uuid', $uuid)
//         ->whereNull('ended_at')
//         ->update(['updated_at' => now()]);

//     // 🔥 Synchroniser la session serveur
//     session(['last_activity' => now()]);

//     return response()->json(['status' => true]);
// })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


// Tracking routes
Route::post('/track/visit-end', function (Request $request) {
    $uuid = $request->input('visit_historique_uuid');

    if (!$uuid) {
        return response()->json([
            'status' => false,
            'error' => 'uuid_missing'
        ]);
    }

    $historique = VisitHistorique::where('uuid', $uuid)
        ->whereNull('ended_at')
        ->first();

    if ($historique) {
        $duration = now()->diffInSeconds($historique->started_at);
        // Limiter à 2 heures max
        $duration = min($duration, 7200);

        $historique->update([
            'ended_at' => now(),
            'duration' => $duration,
        ]);

        Log::debug('Session fermée via beacon', [
            'uuid' => $uuid,
            'duration' => $duration
        ]);
    }

    // Nettoyer la session
    session()->forget([
        'visit_uuid',
        'visit_historique_uuid',
        'last_activity',
        'session_started_at'
    ]);

    return response()->json(['status' => true]);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/track/heartbeat', function (Request $request) {
    $uuid = $request->input('visit_historique_uuid');

    if (!$uuid) {
        return response()->json(['status' => false]);
    }

    VisitHistorique::where('uuid', $uuid)
        ->whereNull('ended_at')
        ->update([
            'last_activity_at' => now(),
            'updated_at' => now()
        ]);

    // Synchroniser la session serveur
    session(['last_activity' => now()]);

    return response()->json(['status' => true]);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/track/page-duration', function (Request $request) {
    $uuid = $request->input('historique_uuid');

    if (!$uuid) {
        return response()->json(['status' => false, 'error' => 'uuid_missing']);
    }

    $historique = PageViewHistorique::where('uuid', $uuid)
        ->whereNull('ended_at')
        ->first();

    if (!$historique) {
        return response()->json(['status' => true, 'message' => 'already_closed']);
    }

    $duration = now()->diffInSeconds($historique->started_at);
    // Limiter à 30 minutes max par page
    $duration = min($duration, 1800);

    $historique->update([
        'ended_at' => now(),
        'duration' => $duration,
    ]);

    return response()->json(['status' => true, 'duration' => $duration]);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/send/email', [MailController::class, 'sendMail'])->name('sendMail');



Route::get('api/country', function () {
    $response = Http::get('https://iso.lahrim.fr/countries');

    $data = $response->json();

    if (!is_array($data) || !isset($data['data'])) {
        return response()->json(['error' => 'Réponse invalide de l’API'], 500);
    }

    $countries = collect($data['data'])->map(function ($item) {
        return [
            'name' => $item['name'] ?? 'N/A',
            'flag' => $item['flag'] ?? '',
        ];
    });

    return $countries->values();
});
// Route::get('api/cities', function () {
//     $allCities = [];

//     $baseUrl = 'https://api.thecompaniesapi.com/v2/locations/cities';
//     $token = 'QkOk9xpTQwSDEvmD06dt7fD8SzNbr5ar';
//     $page = 1;

//     $response = Http::get($baseUrl, [
//             'token' => $token,
//             'page' => $page,
//         ]);

//         $dataResponse = $response->json();

//         foreach ($dataResponse['cities'] as $city) {
//             $Cities = [
//                 'code' => $city['code'],
//                 'ville' => $city['name'],
//                 'pays' => $city['country']['nameFr'],

//             ];
//             $meta = $dataResponse['meta'];

//             $allCities[] = $Cities;
//         }
//         $allCities['meta'] = $meta;

//         // 

//     do {


//         if (!isset($dataResponse['cities']) || !is_array($dataResponse['cities'])) {
//             break;
//         }

//         $allCities = array_merge($allCities, $dataResponse['cities']);

//         $currentPage = $data['meta']['currentPage'] ?? $page;
//         $lastPage = $data['meta']['lastPage'] ?? $page;
//         $page++;

//     } while ($currentPage < $lastPage);

//     $countries = collect($allCities)
//         // ->pluck('state')
//         ->filter()
//         ->unique()
//         ->values();

//         return response()->json($countries);
//     });

Route::get('api/cities', function () {
    $allCities = [];

    $baseUrl = 'https://api.thecompaniesapi.com/v2/locations/cities';
    $token = 'QkOk9xpTQwSDEvmD06dt7fD8SzNbr5ar';
    $page = 1;
    $lastPage = 1;

    do {
        $response = Http::get($baseUrl, [
            'token' => $token,
            'page' => $page,
        ]);

        $dataResponse = $response->json();

        if (!isset($dataResponse['cities']) || !is_array($dataResponse['cities'])) {
            break;
        }

        foreach ($dataResponse['cities'] as $city) {
            $allCities[] = [
                'code' => $city['code'] ?? null,
                'ville' => $city['name'] ?? null,
                'pays' => $city['country']['nameFr'] ?? null,
            ];
        }

        $currentPage = $dataResponse['meta']['currentPage'] ?? $page;
        $lastPage = $dataResponse['meta']['lastPage'] ?? $page;

        $page++;
    } while ($currentPage < $lastPage);

    // Retourne toutes les villes sans la pagination
    return response()->json($allCities);
    // afficher les villes dont le pays est Côte d'Ivoire
    // $filteredCities = collect($allCities)
    //     ->filter(function ($city) {
    //         return $city['pays'] == 'Côte d\'Ivoire';
    //     })
    //     ->values();

    // return response()->json($filteredCities);

});
    // return response()->json($dataResponse);
    // return response()->json($data);

// $data = [
        //     'code' => $dataResponse['cities']['code'],
        //     'pays' => $dataResponse['cities']['country'],
        //     'meta' => $dataResponse['meta'],
        // ];