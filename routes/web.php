<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
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

    Route::get('/bookings/create', [BookingController::class, 'create'])
        ->name('bookings.create');

    Route::post('/bookings', [BookingController::class, 'store'])
        ->name('bookings.store');

    Route::get('/bookings/{booking}', [BookingController::class, 'show'])
        ->name('bookings.show');

    // Customer: unggah bukti pembayaran milik sendiri.
    Route::post('/bookings/{booking}/payment', [BookingController::class, 'uploadPayment'])
        ->name('bookings.payment');

    // Customer: riwayat pembayaran sendiri.
    Route::get('/pembayaran-saya', [PaymentController::class, 'mine'])
        ->name('payments.mine');

    Route::get('/cars', [CarController::class, 'index'])
        ->name('cars.index');

    // whereNumber agar /cars/create tidak tertangkap sebagai {car}.
    Route::get('/cars/{car}', [CarController::class, 'show'])
        ->whereNumber('car')
        ->name('cars.show');

    Route::middleware('role:admin')->group(function () {
        Route::resource('cars', CarController::class)->except(['index', 'show']);

        Route::get('/laporan', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/laporan/export', [ReportController::class, 'export'])
            ->name('reports.export');

        Route::resource('bookings', BookingController::class)->except(['create', 'store', 'show']);

        Route::post('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])
            ->name('bookings.status');

        Route::post('/bookings/{booking}/payment/verify', [BookingController::class, 'verifyPayment'])
            ->name('bookings.payment.verify');

        // Admin: kelola pembayaran & customer.
        Route::get('/pembayaran', [PaymentController::class, 'index'])
            ->name('payments.index');

        Route::get('/customers', [CustomerController::class, 'index'])
            ->name('customers.index');

        Route::get('/customers/{user}', [CustomerController::class, 'show'])
            ->name('customers.show');
    });
});