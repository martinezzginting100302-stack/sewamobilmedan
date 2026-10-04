<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class CustomerController extends Controller
{
    /** Admin only */
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->withCount('bookings')
            ->latest('id')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'bookings_count' => $u->bookings_count,
                'created_at' => $u->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    /** Admin only */
    public function show(User $user)
    {
        if ($user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'User bukan customer.',
            ], 404);
        }

        $user->load(['bookings.car']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'bookings' => $user->bookings->map(fn ($b) => [
                    'id' => $b->id,
                    'car' => $b->car?->nama_mobil,
                    'tanggal_mulai' => $b->tanggal_mulai?->format('Y-m-d'),
                    'tanggal_selesai' => $b->tanggal_selesai?->format('Y-m-d'),
                    'total_harga' => (float) $b->total_harga,
                    'status' => $b->status,
                    'status_pembayaran' => $b->status_pembayaran,
                ]),
            ],
        ]);
    }
}
