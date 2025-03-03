<?php

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\ShippingRatesController;
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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('rates', [ShippingRatesController::class, 'index']);
Route::post('fetch-address', [ShippingRatesController::class, 'fetchAddress']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
