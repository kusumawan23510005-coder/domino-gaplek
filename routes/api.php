<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TopUpController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// SEMUA YANG BUTUH TOKEN (LOGIN) ADA DI DALAM GROUP INI
Route::middleware('auth:sanctum')->group(function () {
    // Fitur Saldo
    Route::post('/topup', [TopUpController::class, 'topup']);
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
    Route::get('/topup/history', [TopUpController::class, 'history']);
    Route::delete('/topup/history/{id}', [TopUpController::class, 'destroy']);

    // --- FITUR BARU: UPDATE SALDO HASIL GAME ---
    Route::post('/game/result', [TopUpController::class, 'gameResult']);
});