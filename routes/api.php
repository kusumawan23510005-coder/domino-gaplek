<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\RoomController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- 1. OTENTIKASI (Halaman Depan / Publik) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- 2. FITUR TERPROTEKSI (Harus Login Dulu) ---
// Semua yang ada di dalam grup ini butuh Token (Bearer Token)
Route::middleware('auth:sanctum')->group(function () {

    // --- PROFIL USER ---
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    // --- LOGOUT (Pindahkan ke sini!) ---
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- FITUR GAME & HISTORY ---
    Route::post('/game/result', [GameController::class, 'submitResult']);
    Route::get('/history/game', [GameController::class, 'history']);
    Route::get('/history/topup', [GameController::class, 'getTopupHistory']);
    Route::delete('/history', [GameController::class, 'clearHistory']);

    // --- FITUR ROOM (MULTIPLAYER) ---
    Route::post('/rooms/create', [RoomController::class, 'create']);
    Route::post('/rooms/join', [RoomController::class, 'join']);
    Route::get('/rooms/{code}', [RoomController::class, 'detail']);
    Route::get('/rooms/{code}/state', [RoomController::class, 'getState']);
    Route::post('/rooms/{code}/play', [RoomController::class, 'playCard']);
    Route::post('/rooms/{code}/pass', [RoomController::class, 'passTurn']);
});
