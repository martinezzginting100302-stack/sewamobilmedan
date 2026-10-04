<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SewaMobilMedan
| Auth: Sanctum Bearer token.
| Header wajib: Accept: application/json
|--------------------------------------------------------------------------
*/

// Publik
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Mobil — customer otomatis hanya melihat yang tersedia (lihat controller).
    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{car}', [CarController::class, 'show']);

    // Peminjaman — customer hanya milik sendiri (lihat controller).
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::post('/bookings/{booking}/payment', [BookingController::class, 'uploadPayment']);

    // Pembayaran milik sendiri.
    Route::get('/payments/mine', [PaymentController::class, 'mine']);

    // ---- Khusus admin ----
    Route::middleware('role:admin')->group(function () {
        Route::post('/cars', [CarController::class, 'store']);
        Route::put('/cars/{car}', [CarController::class, 'update']);
        Route::patch('/cars/{car}', [CarController::class, 'update']);
        Route::delete('/cars/{car}', [CarController::class, 'destroy']);

        Route::put('/bookings/{booking}', [BookingController::class, 'update']);
        Route::patch('/bookings/{booking}', [BookingController::class, 'update']);
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
        Route::post('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
        Route::post('/bookings/{booking}/payment/verify', [BookingController::class, 'verifyPayment']);

        Route::get('/payments', [PaymentController::class, 'index']);

        Route::get('/customers', [CustomerController::class, 'index']);
        Route::get('/customers/{user}', [CustomerController::class, 'show']);
    });
});
