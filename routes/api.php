<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\InscriptionApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('v1/payments/university/webhook', [PaymentWebhookController::class, 'handle']);
Route::get('v1/registration/{token}', [InscriptionApiController::class, 'showForUniversity']);
