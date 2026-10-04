<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    /**
     * GET /api/bookings — admin: semua (filter status),
     * customer: hanya milik sendiri.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Booking::with(['car', 'user'])->latest('id');

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->input('status_pembayaran'));
        }

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($query->get()),
        ]);
    }

    /**
     * POST /api/bookings — customer & admin boleh mengajukan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'nama_penyewa' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'metode_pembayaran' => 'nullable|in:transfer_bank,e_wallet,qris,cash',
        ]);

        $car = Car::findOrFail($validated['car_id']);

        if ($car->status === 'maintenance') {
            return response()->json([
                'success' => false,
                'message' => 'Mobil sedang dalam maintenance dan tidak bisa disewa.',
            ], 422);
        }

        if ($this->isBooked($car->id, $validated['tanggal_mulai'], $validated['tanggal_selesai'])) {
            return response()->json([
                'success' => false,
                'message' => 'Mobil sudah dibooking pada tanggal yang dipilih.',
            ], 422);
        }

        $booking = $this->persistBooking($car, $validated, 'menunggu', $request->user()->id);
        $booking->load(['car', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil diajukan.',
            'data' => new BookingResource($booking),
        ], 201);
    }

    public function show(Request $request, Booking $booking)
    {
        if ($request->user()->role !== 'admin' && $booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk booking ini.',
            ], 403);
        }

        $booking->load(['car', 'user']);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking),
        ]);
    }

    /** Admin only */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'nama_penyewa' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'metode_pembayaran' => 'nullable|in:transfer_bank,e_wallet,qris,cash',
        ]);

        if ($this->isBooked(
            $validated['car_id'],
            $validated['tanggal_mulai'],
            $validated['tanggal_selesai'],
            $booking->id
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Mobil sudah dibooking pada tanggal yang dipilih.',
            ], 422);
        }

        $car = Car::findOrFail($validated['car_id']);
        $oldCarId = $booking->car_id;

        $data = $this->buildBookingData($car, $validated);
        $data['status'] = $booking->status;

        $booking->update($data);
        $this->syncCarStatus($oldCarId);
        $this->syncCarStatus($booking->car_id);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil diperbarui.',
            'data' => new BookingResource($booking->fresh(['car', 'user'])),
        ]);
    }

    /** Admin only */
    public function destroy(Booking $booking)
    {
        $carId = $booking->car_id;
        $booking->delete();
        $this->syncCarStatus($carId);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dihapus.',
        ]);
    }

    /** Admin only */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,dikonfirmasi,selesai,dibatalkan',
        ]);

        $booking->update(['status' => $validated['status']]);
        $this->syncCarStatus($booking->car_id);

        return response()->json([
            'success' => true,
            'message' => 'Status booking diubah menjadi ' . $validated['status'] . '.',
            'data' => new BookingResource($booking->fresh(['car', 'user'])),
        ]);
    }

    /**
     * POST /api/bookings/{booking}/payment — pemilik atau admin.
     */
    public function uploadPayment(Request $request, Booking $booking)
    {
        if ($request->user()->role !== 'admin' && $booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk booking ini.',
            ], 403);
        }

        $validated = $request->validate([
            'metode_pembayaran' => 'required|in:transfer_bank,e_wallet,qris,cash',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = ['metode_pembayaran' => $validated['metode_pembayaran']];

        if ($request->hasFile('bukti_pembayaran')) {
            if ($booking->bukti_pembayaran) {
                Storage::disk('public')->delete($booking->bukti_pembayaran);
            }
            $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('payments', 'public');
            $data['status_pembayaran'] = 'menunggu_verifikasi';
        } elseif ($validated['metode_pembayaran'] === 'cash') {
            $data['status_pembayaran'] = 'menunggu_verifikasi';
        }

        $booking->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikirim. Menunggu verifikasi admin.',
            'data' => new BookingResource($booking->fresh(['car', 'user'])),
        ]);
    }

    /**
     * POST /api/bookings/{booking}/payment/verify — admin only.
     */
    public function verifyPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status_pembayaran' => 'required|in:lunas,ditolak,menunggu_verifikasi,belum_bayar',
        ]);

        $booking->update(['status_pembayaran' => $validated['status_pembayaran']]);

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran diubah menjadi ' . $validated['status_pembayaran'] . '.',
            'data' => new BookingResource($booking->fresh(['car', 'user'])),
        ]);
    }

    // ---- helpers (mirror web logic) ----

    protected function isBooked(int $carId, string $mulai, string $selesai, ?int $exceptId = null): bool
    {
        $query = Booking::where('car_id', $carId)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->where(function ($q) use ($mulai, $selesai) {
                $q->whereBetween('tanggal_mulai', [$mulai, $selesai])
                    ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])
                    ->orWhere(function ($q) use ($mulai, $selesai) {
                        $q->where('tanggal_mulai', '<=', $mulai)
                            ->where('tanggal_selesai', '>=', $selesai);
                    });
            });

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    protected function buildBookingData(Car $car, array $validated): array
    {
        $jumlahHari = (strtotime($validated['tanggal_selesai']) - strtotime($validated['tanggal_mulai'])) / 86400 + 1;

        return [
            'car_id' => $car->id,
            'nama_penyewa' => $validated['nama_penyewa'],
            'no_telepon' => $validated['no_telepon'],
            'alamat' => $validated['alamat'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => (int) $jumlahHari,
            'harga_per_hari' => $car->harga_sewa,
            'total_harga' => ((int) $jumlahHari) * $car->harga_sewa,
            'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
        ];
    }

    protected function persistBooking(Car $car, array $validated, string $status, ?int $userId = null): Booking
    {
        $data = $this->buildBookingData($car, $validated);
        $data['user_id'] = $userId ?? auth()->id();
        $data['status'] = $status;

        $booking = Booking::create($data);
        $this->syncCarStatus($booking->car_id);

        return $booking;
    }

    protected function syncCarStatus(int $carId): void
    {
        $car = Car::find($carId);
        if (! $car || $car->status === 'maintenance') {
            return;
        }

        $hasActive = Booking::where('car_id', $carId)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->exists();

        $newStatus = $hasActive ? 'disewa' : 'tersedia';
        if ($car->status !== $newStatus) {
            $car->update(['status' => $newStatus]);
        }
    }
}
