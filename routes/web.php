<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegisterForm'])
    ->name('register');

Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('cars', CarController::class);

    Route::get('/laporan', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/laporan/export', [ReportController::class, 'export'])
        ->name('reports.export');

    Route::resource('bookings', BookingController::class);

    Route::post('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])
        ->name('bookings.status');
});