<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMobil = Car::count();
        $mobilTersedia = Car::where('status', 'tersedia')->count();
        $mobilDisewa = Car::where('status', 'disewa')->count();
        $mobilMaintenance = Car::where('status', 'maintenance')->count();

        $bookingMenunggu = Booking::where('status', 'menunggu')->count();
        $bookingAktif = Booking::whereIn('status', ['menunggu', 'dikonfirmasi'])->count();
        $bookingSelesai = Booking::where('status', 'selesai')->count();

        $pendapatan = Booking::whereIn('status', ['selesai', 'dikonfirmasi'])
            ->sum('total_harga');

        $bookingTerbaru = Booking::with('car')
            ->latest('id')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'totalMobil',
            'mobilTersedia',
            'mobilDisewa',
            'mobilMaintenance',
            'bookingMenunggu',
            'bookingAktif',
            'bookingSelesai',
            'pendapatan',
            'bookingTerbaru',
        ));
    }
}