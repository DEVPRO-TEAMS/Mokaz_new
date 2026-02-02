<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ReconductionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Partner\PartnerController;
use App\Http\Controllers\User\ReservationController;
use App\Http\Controllers\Properties\AppartController;
use App\Http\Controllers\Properties\PropertyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// parametrage

Route::post('/variable/store', [SettingController::class, 'storeVariable'])->name('storeVariable');

// reservations 

Route::post('/reservation/store', [ReservationController::class, 'store']);
Route::post('/reservation/update-by-paiement/{uuid}', [ReservationController::class, 'updateByPaiement']);
Route::get('/reservation/download-receipt/{uuid}', [ReservationController::class, 'downloadReceipt'])
    ->name('reservation.download-receipt');

Route::post('/cron/autoRemiseReservation', [ReservationController::class, 'autoRemiseReservation']);
Route::post('/customerIsPresent', [ReservationController::class, 'customerIsPresent']);


Route::post('/get-paiement-data', [ReservationController::class, 'getPaiementData']);
Route::post('/cron/get-paiement-status/{reservation_code}', [ReservationController::class, 'getPaiementStatus']);

Route::post('/my-reservation', [ReservationController::class, 'myReservation'])->name('myReservation');


Route::get('setting/index/propertiesTypes', [SettingController::class, 'propertiesTypes'])->name('index.propertiesTypes');
Route::post('setting/store/propertyType', [SettingController::class, 'storePropertyType'])->name('store.propertyType');
Route::post('setting/update/{id}/propertyType', [SettingController::class, 'updatePropertyType'])->name('update.propertyType');
Route::delete('setting/destroy/{id}/propertyType', [SettingController::class, 'destroyPropertyType'])->name('destroy.propertyType');

Route::post('partner/demande/store', [PartnerController::class, 'store']);
Route::post('/partnership/accept/{id}', [AdminController::class, 'accepterDemande']);
Route::post('/demande/rejet/{id}', [AdminController::class, 'rejetDemande'])->name('api.rejet.demande');


// api reconduction de bien
Route::post('/reconduction/store', [ReconductionController::class, 'store']);




Route::get('/get-cities-by-country', [PropertyController::class, 'getCities']);

// api property
Route::post('/property/add', [PropertyController::class, 'store']);
Route::post('/property/update/{uuid}', [PropertyController::class, 'update']);
Route::post('/property/destroy/{uuid}', [PropertyController::class, 'destroy']);
Route::get('/get-all-properties', [PagesController::class, 'getAllProperties']);

// api appartements
Route::post('/appart/add', [AppartController::class, 'store']);
Route::post('/appart/update/{uuid}/{property_uuid}', [AppartController::class, 'update']);
Route::post('/appart/destroy/{uuid}', [AppartController::class, 'destroy']);
Route::post('/delete-appart-image/{uuid}', [AppartController::class, 'deleteAppartImage']);
Route::post('/delete-appart-tarif/{uuid}', [AppartController::class, 'deleteAppartTarif']);

Route::post('/add-comments', [PagesController::class, 'addComment']);
Route::get('/get-comments', [PagesController::class, 'getComments']);

Route::post('/comment/destroy/{uuid}', [CommentController::class, 'destroy']);

Route::post('/send-sms-helper', [PagesController::class, 'demoSms']);


Route::prefix('dashboard')->group(function () {
    Route::get('/kpis', [DashboardController::class, 'getKPIData']);
    Route::get('/traffic-chart', [DashboardController::class, 'getTrafficChartData']);
    Route::get('/sources', [DashboardController::class, 'getSourcesData']);
    Route::get('/top-cities', [DashboardController::class, 'getTopCities']);
    Route::get('/geographic', [DashboardController::class, 'getGeographicData']);
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
