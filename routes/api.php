<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controller
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TopUpController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua route API ada di sini. Akses via:
| http://127.0.0.1:8000/api/...
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route yang butuh login token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/topup', [TopUpController::class, 'topup']);

    // Tes apakah token bekerja
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
});
