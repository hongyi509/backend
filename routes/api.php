<?php

use App\Http\Controllers\Api\LicenceController;
use App\Http\Controllers\Api\PaypalOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Pour écran de test...
Route::get('/time', function (Request $request) {
    return [
        'time' => time()
    ];
});

Route::post('/licence/exists', [LicenceController::class, 'postExists']);
Route::post('/licence/restore', [LicenceController::class, 'postRestore']);

Route::post('/paypal/order/init', [PaypalOrderController::class, 'postInit']);
Route::post('/paypal/order/captured', [PaypalOrderController::class, 'postCaptured']);

Route::fallback(function (){
    abort(404, 'API resource not found');
});