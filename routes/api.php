<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('game')->group(function () {
    Route::post('/create', [GameController::class,'create']);
    Route::delete('/delete/{id}', [GameController::class,'delete']);
    Route::post('/proposeCombination', [GameController::class,'proposeCombination']);
    Route::post('/previewResponse', [GameController::class,'previewResponse']);
});
