<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /** Admin: semua pembayaran + ringkasan. */
    public function index(Request $request)
    {
        $query = Booking::with(['car', 'user'])->latest('id');

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->input('status_pembayaran'));
        }

        $payments = $query->get();

        return response()->json([
            'success' => true,
            'summary' => [
                'total_lunas' => Booking::where('status_pembayaran', 'lunas')->sum('total_harga'),
                'menunggu_verifikasi' => Booking::where('status_pembayaran', 'menunggu_verifikasi')->count(),
            ],
            'data' => BookingResource::collection($payments),
        ]);
    }

    /** Customer: milik sendiri. */
    public function mine(Request $request)
    {
        $payments = Booking::with('car')
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($payments),
        ]);
    }
}
