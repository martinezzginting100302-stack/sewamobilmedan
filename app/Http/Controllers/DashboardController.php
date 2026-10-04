<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        $totalMobil = Car::count();
        $mobilTersedia = Car::where('status', 'tersedia')->count();
        $mobilDisewa = Car::where('status', 'disewa')->count();
        $mobilMaintenance = Car::where('status', 'maintenance')->count();

        $bookingMenunggu = $isAdmin ? Booking::where('status', 'menunggu')->count() : 0;
        $bookingAktif = $isAdmin ? Booking::whereIn('status', ['menunggu', 'dikonfirmasi'])->count() : 0;
        $bookingSelesai = $isAdmin ? Booking::where('status', 'selesai')->count() : 0;

        $pendapatan = $isAdmin ? Booking::whereIn('status', ['selesai', 'dikonfirmasi'])
            ->sum('total_harga') : 0;

        // Khusus admin: kelola pembayaran & customer.
        $totalCustomer = $isAdmin ? User::where('role', 'customer')->count() : 0;
        $pembayaranMenunggu = $isAdmin ? Booking::where('status_pembayaran', 'menunggu_verifikasi')->count() : 0;
        $pembayaranLunas = $isAdmin ? Booking::where('status_pembayaran', 'lunas')->sum('total_harga') : 0;

        $bookingTerbaru = $isAdmin ? Booking::with('car')
            ->latest('id')
            ->limit(8)
            ->get() : Booking::with('car')
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(8)
            ->get();

        // Katalog untuk customer: hanya mobil tersedia + spesifikasi.
        $mobilTersediaList = $isAdmin
            ? collect()
            : Car::where('status', 'tersedia')->latest('id')->limit(6)->get();

        $bookingSaya = $isAdmin
            ? collect()
            : Booking::with('car')->where('user_id', $user->id)->latest('id')->get();

        return view('dashboard', compact(
            'totalMobil',
            'mobilTersedia',
            'mobilDisewa',
            'mobilMaintenance',
            'bookingMenunggu',
            'bookingAktif',
            'bookingSelesai',
            'pendapatan',
            'totalCustomer',
            'pembayaranMenunggu',
            'pembayaranLunas',
            'bookingTerbaru',
            'mobilTersediaList',
            'bookingSaya',
            'isAdmin',
        ));
    }
}