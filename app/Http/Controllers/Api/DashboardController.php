<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';

        if ($isAdmin) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_mobil' => Car::count(),
                    'mobil_tersedia' => Car::where('status', 'tersedia')->count(),
                    'mobil_disewa' => Car::where('status', 'disewa')->count(),
                    'mobil_maintenance' => Car::where('status', 'maintenance')->count(),
                    'booking_menunggu' => Booking::where('status', 'menunggu')->count(),
                    'booking_aktif' => Booking::whereIn('status', ['menunggu', 'dikonfirmasi'])->count(),
                    'total_customer' => User::where('role', 'customer')->count(),
                    'pembayaran_menunggu' => Booking::where('status_pembayaran', 'menunggu_verifikasi')->count(),
                    'pembayaran_lunas' => Booking::where('status_pembayaran', 'lunas')->sum('total_harga'),
                    'booking_terbaru' => BookingResource::collection(
                        Booking::with(['car', 'user'])->latest('id')->limit(8)->get()
                    ),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'mobil_tersedia' => Car::where('status', 'tersedia')->latest('id')->limit(6)->get()->map(fn ($c) => [
                    'id' => $c->id,
                    'nama_mobil' => $c->nama_mobil,
                    'merk' => $c->merk,
                    'tipe' => $c->tipe,
                    'tahun' => $c->tahun,
                    'plat_nomor' => $c->plat_nomor,
                    'harga_sewa' => (float) $c->harga_sewa,
                    'foto_url' => $c->foto ? asset('storage/' . $c->foto) : null,
                ]),
                'booking_saya' => BookingResource::collection(
                    Booking::with('car')->where('user_id', $user->id)->latest('id')->get()
                ),
            ],
        ]);
    }
}
