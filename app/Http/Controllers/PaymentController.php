<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Admin: daftar seluruh pembayaran (disaring per status).
     */
    public function index(Request $request)
    {
        $query = Booking::with(['car', 'user'])->latest('id');

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->input('status_pembayaran'));
        }

        $payments = $query->get();
        $filterPayment = $request->input('status_pembayaran', '');

        $totalLunas = Booking::where('status_pembayaran', 'lunas')->sum('total_harga');
        $menungguCount = Booking::where('status_pembayaran', 'menunggu_verifikasi')->count();

        return view('payments.index', compact('payments', 'filterPayment', 'totalLunas', 'menungguCount'));
    }

    /**
     * Customer: riwayat pembayaran milik sendiri.
     */
    public function mine()
    {
        $payments = Booking::with('car')
            ->where('user_id', auth()->id())
            ->latest('id')
            ->get();

        return view('payments.mine', compact('payments'));
    }
}
