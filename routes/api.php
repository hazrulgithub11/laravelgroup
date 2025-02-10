<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\ProviderController;

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

Route::get('/telegram/set-webhook', [TelegramController::class, 'setWebhook']);
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);
Route::get('/telegram/test-message/{chat_id}', [TelegramController::class, 'testMessage']);
Route::get('/providers/{service}', [ProviderController::class, 'getByService']);
Route::post('/telegram/test-notification', [TelegramController::class, 'sendTestNotification'])
    ->middleware('auth');
