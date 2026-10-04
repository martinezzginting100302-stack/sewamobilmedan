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
        $query = Car::where('status', 'tersedia')->orderBy('nama_mobil');

        // Dukung preselect dari halaman detail mobil: /bookings/create?car_id=1
        $selectedCarId = request()->query('car_id');

        $cars = $query->get();

        return view('bookings.create', compact('cars', 'selectedCarId'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBookingData($request);

        $car = Car::findOrFail($validated['car_id']);

        // Customer hanya boleh membooking mobil yang benar-benar tersedia.
        // Cegah manipulasi car_id via DevTools untuk mobil disewa/maintenance.
        if ($car->status !== 'tersedia' && $this->isBooked($car->id, $validated['tanggal_mulai'], $validated['tanggal_selesai'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'car_id' => 'Mobil yang dipilih sedang tidak tersedia.',
                ]);
        }

        if ($car->status === 'maintenance') {
            return back()
                ->withInput()
                ->withErrors([
                    'car_id' => 'Mobil sedang dalam maintenance dan tidak bisa disewa.',
                ]);
        }

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
        // Otorisasi: customer hanya boleh melihat booking miliknya sendiri.
        // Mencegah IDOR (tebak /bookings/1, /bookings/2 milik orang lain).
        if (auth()->user()->role !== 'admin' && $booking->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki izin untuk melihat booking ini.');
        }

        $booking->load('car');

        $isAdmin = auth()->user()->role === 'admin';

        return view('bookings.show', compact('booking', 'isAdmin'));
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

    /**
     * Customer mengunggah bukti pembayaran.
     * Hanya pemilik booking (atau admin) yang boleh.
     */
    public function uploadPayment(Request $request, Booking $booking)
    {
        if (auth()->user()->role !== 'admin' && $booking->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki izin untuk booking ini.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => 'required|in:transfer_bank,e_wallet,qris,cash',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = ['metode_pembayaran' => $validated['metode_pembayaran']];

        if ($request->hasFile('bukti_pembayaran')) {
            if ($booking->bukti_pembayaran) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($booking->bukti_pembayaran);
            }
            $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('payments', 'public');
            $data['status_pembayaran'] = 'menunggu_verifikasi';
        } elseif ($validated['metode_pembayaran'] === 'cash') {
            // Bayar tunai di tempat: langsung menunggu verifikasi admin.
            $data['status_pembayaran'] = 'menunggu_verifikasi';
        }

        $booking->update($data);

        return back()->with('success', 'Pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }

    /**
     * Admin verifikasi pembayaran: lunas / ditolak / reset.
     */
    public function verifyPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status_pembayaran' => 'required|in:lunas,ditolak,menunggu_verifikasi,belum_bayar',
        ]);

        $booking->update(['status_pembayaran' => $validated['status_pembayaran']]);

        $labels = [
            'lunas' => 'Lunas',
            'ditolak' => 'Ditolak',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'belum_bayar' => 'Belum Bayar',
        ];

        return back()->with('success', 'Status pembayaran diubah menjadi ' . $labels[$validated['status_pembayaran']] . '.');
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
            'metode_pembayaran' => 'nullable|in:transfer_bank,e_wallet,qris,cash',
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
            'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
        ];
    }

    protected function persistBooking(Car $car, array $validated, string $status): Booking
    {
        $data = $this->buildBookingData($car, $validated);
        $data['user_id'] = auth()->id();
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

        // Jangan timpa status maintenance yang diset manual oleh admin.
        if ($car->status === 'maintenance') {
            return;
        }

        $hasActiveBooking = Booking::where('car_id', $carId)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->exists();

        $newStatus = $hasActiveBooking ? 'disewa' : 'tersedia';

        // Jika admin secara manual menandai maintenance, sync tidak boleh
        // mengembalikannya ke tersedia/disewa secara otomatis di sini.
        // (Maintenance hanya diubah lewat form Edit Mobil.)
        if ($car->status !== $newStatus) {
            $car->update(['status' => $newStatus]);
        }
    }
}