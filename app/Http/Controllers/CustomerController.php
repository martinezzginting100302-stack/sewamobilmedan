<?php

namespace App\Http\Controllers;

use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->withCount('bookings')
            ->latest('id')
            ->get();

        return view('customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        if ($user->role !== 'customer') {
            abort(404);
        }

        $user->load(['bookings.car']);

        return view('customers.show', compact('user'));
    }
}
