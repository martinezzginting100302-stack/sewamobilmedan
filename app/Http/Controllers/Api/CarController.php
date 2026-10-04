<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    /**
     * GET /api/cars — customer hanya melihat yang tersedia.
     */
    public function index(Request $request)
    {
        $isAdmin = $request->user()->role === 'admin';

        $query = Car::withCount('bookings')->latest('id');

        if (! $isAdmin) {
            $query->where('status', 'tersedia');
        } elseif ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_mobil', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data' => CarResource::collection($query->get()),
        ]);
    }

    public function show(Car $car)
    {
        $car->loadCount('bookings');

        return response()->json([
            'success' => true,
            'data' => new CarResource($car),
        ]);
    }

    /** Admin only */
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

        $car = Car::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data mobil berhasil ditambahkan.',
            'data' => new CarResource($car),
        ], 201);
    }

    /** Admin only */
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

        return response()->json([
            'success' => true,
            'message' => 'Data mobil berhasil diperbarui.',
            'data' => new CarResource($car->fresh()),
        ]);
    }

    /** Admin only */
    public function destroy(Car $car)
    {
        if ($car->bookings()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Mobil tidak dapat dihapus karena masih memiliki data booking.',
            ], 422);
        }

        if ($car->foto) {
            Storage::disk('public')->delete($car->foto);
        }

        $car->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data mobil berhasil dihapus.',
        ]);
    }
}
