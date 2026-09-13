<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::withCount('bookings')
            ->latest('id')
            ->get();

        return view('cars.index', compact('cars'));
    }

    public function create()
    {
        return view('cars.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mobil' => 'required|string|max:255',
            'merk' => 'required|string|max:100',
            'tipe' => 'nullable|string|max:100',
            'tahun' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'plat_nomor' => 'required|string|max:20|unique:cars,plat_nomor',
            'harga_sewa' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,disewa,maintenance',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('cars', 'public');
        }

        Car::create($validated);

        return redirect()
            ->route('cars.index')
            ->with('success', 'Data mobil "' . $validated['nama_mobil'] . '" berhasil ditambahkan.');
    }

    public function show(Car $car)
    {
        $car->load([
            'bookings' => fn ($q) => $q->latest('id'),
        ]);

        return view('cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        return view('cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'nama_mobil' => 'required|string|max:255',
            'merk' => 'required|string|max:100',
            'tipe' => 'nullable|string|max:100',
            'tahun' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'plat_nomor' => 'required|string|max:20|unique:cars,plat_nomor,' . $car->id,
            'harga_sewa' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,disewa,maintenance',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($car->foto) {
                Storage::disk('public')->delete($car->foto);
            }

            $validated['foto'] = $request->file('foto')->store('cars', 'public');
        }

        $car->update($validated);

        return redirect()
            ->route('cars.index')
            ->with('success', 'Data mobil "' . $car->nama_mobil . '" berhasil diperbarui.');
    }

    public function destroy(Car $car)
    {
        if ($car->bookings()->exists()) {
            return back()->withErrors([
                'car' => 'Mobil tidak dapat dihapus karena masih memiliki data booking.',
            ]);
        }

        if ($car->foto) {
            Storage::disk('public')->delete($car->foto);
        }

        $car->delete();

        return redirect()
            ->route('cars.index')
            ->with('success', 'Data mobil "' . $car->nama_mobil . '" berhasil dihapus.');
    }
}