<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('car')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $bookings = $query->get();

        $filterStatus = $request->input('status', '');

        return view('bookings.index', compact('bookings', 'filterStatus'));
    }

    public function create()
    {
        $cars = Car::where('status', 'tersedia')
            ->orderBy('nama_mobil')
            ->get();

        return view('bookings.create', compact('cars'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBookingData($request);

        $car = Car::findOrFail($validated['car_id']);

        if ($this->isBooked($car->id, $validated['tanggal_mulai'], $validated['tanggal_selesai'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'tanggal_mulai' => 'Mobil sudah dibooking pada tanggal yang dipilih.',
                ]);
        }

        $booking = $this->persistBooking($car, $validated, 'menunggu');

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking untuk "' . $booking->nama_penyewa . '" berhasil dibuat.');
    }

    public function show(Booking $booking)
    {
        $booking->load('car');

        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $cars = Car::where(function ($q) use ($booking) {
            $q->where('status', 'tersedia')
                ->orWhere('id', $booking->car_id);
        })
            ->orderBy('nama_mobil')
            ->get();

        return view('bookings.edit', compact('booking', 'cars'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $this->validateBookingData($request);

        if ($this->isBooked(
            $validated['car_id'],
            $validated['tanggal_mulai'],
            $validated['tanggal_selesai'],
            $booking->id
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'tanggal_mulai' => 'Mobil sudah dibooking pada tanggal yang dipilih.',
                ]);
        }

        $car = Car::findOrFail($validated['car_id']);

        $data = $this->buildBookingData($car, $validated);
        $data['status'] = $booking->status;

        $booking->update($data);

        $this->syncCarStatus($booking->car_id);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,dikonfirmasi,selesai,dibatalkan',
        ]);

        $booking->update(['status' => $validated['status']]);

        $this->syncCarStatus($booking->car_id);

        $labels = [
            'menunggu' => 'Menunggu Konfirmasi',
            'dikonfirmasi' => 'Dikonfirmasi',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        return back()
            ->with('success', 'Status booking "' . $booking->nama_penyewa . '" diubah menjadi ' . $labels[$validated['status']] . '.');
    }

    public function destroy(Booking $booking)
    {
        $carId = $booking->car_id;

        $booking->delete();

        $this->syncCarStatus($carId);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking "' . $booking->nama_penyewa . '" berhasil dihapus.');
    }

    /*
     * ------------------------------------------------------------------
     *  Helper methods
     * ------------------------------------------------------------------
     */

    protected function validateBookingData(Request $request): array
    {
        return $request->validate([
            'car_id' => 'required|exists:cars,id',
            'nama_penyewa' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
    }

    protected function isBooked(
        int $carId,
        string $tanggalMulai,
        string $tanggalSelesai,
        ?int $exceptId = null
    ): bool {
        $query = Booking::where('car_id', $carId)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhere(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                        $q->where('tanggal_mulai', '<=', $tanggalMulai)
                            ->where('tanggal_selesai', '>=', $tanggalSelesai);
                    });
            });

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    protected function buildBookingData(Car $car, array $validated): array
    {
        $jumlahHari = (
            strtotime($validated['tanggal_selesai']) - strtotime($validated['tanggal_mulai'])
        ) / 86400 + 1;

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
        ];
    }

    protected function persistBooking(Car $car, array $validated, string $status): Booking
    {
        $data = $this->buildBookingData($car, $validated);
        $data['status'] = $status;

        $booking = Booking::create($data);

        $this->syncCarStatus($booking->car_id);

        return $booking;
    }

    protected function syncCarStatus(int $carId): void
    {
        $car = Car::find($carId);

        if (! $car) {
            return;
        }

        $hasActiveBooking = Booking::where('car_id', $carId)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->exists();

        $car->update([
            'status' => $hasActiveBooking ? 'disewa' : 'tersedia',
        ]);
    }
}