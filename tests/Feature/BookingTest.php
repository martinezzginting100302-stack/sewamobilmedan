<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    private function createCar(string $plate = 'BK 111 AA'): Car
    {
        return Car::create([
            'nama_mobil' => 'Toyota Avanza',
            'merk' => 'Toyota',
            'tipe' => 'MPV',
            'tahun' => 2022,
            'plat_nomor' => $plate,
            'harga_sewa' => 350000,
            'status' => 'tersedia',
        ]);
    }

    private function bookingData(Car $car, array $overrides = []): array
    {
        return array_merge([
            'car_id' => $car->id,
            'nama_penyewa' => 'Budi Santoso',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Sudirman No. 1, Medan',
            'tanggal_mulai' => Carbon::now()->addDays(2)->toDateString(),
            'tanggal_selesai' => Carbon::now()->addDays(4)->toDateString(),
        ], $overrides);
    }

    public function test_bookings_index_requires_authentication()
    {
        $this->get('/bookings')->assertRedirect('/login');
    }

    public function test_user_can_create_booking()
    {
        $car = $this->createCar();

        $this->actingAs($this->user)
            ->post('/bookings', $this->bookingData($car))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'nama_penyewa' => 'Budi Santoso',
            'status' => 'menunggu',
        ]);

        // 3 hari x 350.000
        $booking = Booking::where('nama_penyewa', 'Budi Santoso')->first();

        $this->assertEquals(3, $booking->jumlah_hari);
        $this->assertEquals(1050000, $booking->total_harga);

        // Status mobil jadi "disewa"
        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'status' => 'disewa',
        ]);
    }

    public function test_booking_validation_rejects_past_dates()
    {
        $car = $this->createCar();

        $this->actingAs($this->user)
            ->post('/bookings', $this->bookingData($car, [
                'tanggal_mulai' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('tanggal_mulai');
    }

    public function test_overlapping_booking_is_rejected()
    {
        $car = $this->createCar();

        $start = Carbon::now()->addDays(3);
        $end = Carbon::now()->addDays(5);

        $this->actingAs($this->user)
            ->post('/bookings', $this->bookingData($car, [
                'tanggal_mulai' => $start->toDateString(),
                'tanggal_selesai' => $end->toDateString(),
            ]))
            ->assertSessionHas('success');

        // Booking kedua yang bertabrakan
        $this->actingAs($this->user)
            ->post('/bookings', $this->bookingData($car, [
                'nama_penyewa' => 'Andi Wijaya',
                'tanggal_mulai' => $start->copy()->addDay()->toDateString(),
                'tanggal_selesai' => $end->copy()->addDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('tanggal_mulai');

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_non_overlapping_booking_is_allowed()
    {
        $car = $this->createCar();

        $start = Carbon::now()->addDays(3);

        $this->actingAs($this->user)
            ->post('/bookings', $this->bookingData($car, [
                'tanggal_mulai' => $start->toDateString(),
                'tanggal_selesai' => $start->copy()->addDay()->toDateString(),
            ]))
            ->assertSessionHas('success');

        // Booking kedua tanpa tabrakan
        $this->actingAs($this->user)
            ->post('/bookings', $this->bookingData($car, [
                'nama_penyewa' => 'Andi Wijaya',
                'tanggal_mulai' => $start->copy()->addDays(3)->toDateString(),
                'tanggal_selesai' => $start->copy()->addDays(5)->toDateString(),
            ]))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_user_can_update_booking()
    {
        $car = $this->createCar();

        $booking = Booking::create([
            'car_id' => $car->id,
            'nama_penyewa' => 'Budi Santoso',
            'no_telepon' => '081234567890',
            'tanggal_mulai' => Carbon::now()->addDays(2)->toDateString(),
            'tanggal_selesai' => Carbon::now()->addDays(4)->toDateString(),
            'jumlah_hari' => 3,
            'harga_per_hari' => 350000,
            'total_harga' => 1050000,
            'status' => 'menunggu',
        ]);

        // Ubah jadi 2 hari -> total 700.000
        $this->actingAs($this->user)
            ->put("/bookings/{$booking->id}", $this->bookingData($car, [
                'tanggal_selesai' => Carbon::now()->addDays(3)->toDateString(),
                'nama_penyewa' => 'Budi Santoso Baru',
            ]))
            ->assertSessionHas('success');

        $booking->refresh();

        $this->assertEquals(2, $booking->jumlah_hari);
        $this->assertEquals(700000, $booking->total_harga);
        $this->assertEquals('Budi Santoso Baru', $booking->nama_penyewa);
    }

    public function test_status_change_to_selesai_frees_the_car()
    {
        $car = $this->createCar();

        $booking = Booking::create([
            'car_id' => $car->id,
            'nama_penyewa' => 'Budi Santoso',
            'no_telepon' => '081234567890',
            'tanggal_mulai' => Carbon::now()->addDays(2)->toDateString(),
            'tanggal_selesai' => Carbon::now()->addDays(4)->toDateString(),
            'jumlah_hari' => 3,
            'harga_per_hari' => 350000,
            'total_harga' => 1050000,
            'status' => 'menunggu',
        ]);

        // Status mobil menyesuaikan dengan booking aktif
        $car->update(['status' => 'disewa']);

        $this->assertDatabaseHas('cars', ['id' => $car->id, 'status' => 'disewa']);

        $this->actingAs($this->user)
            ->post("/bookings/{$booking->id}/status", ['status' => 'selesai'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'selesai']);
        $this->assertDatabaseHas('cars', ['id' => $car->id, 'status' => 'tersedia']);
    }

    public function test_status_change_to_dibatalkan_frees_the_car()
    {
        $car = $this->createCar();

        $booking = Booking::create([
            'car_id' => $car->id,
            'nama_penyewa' => 'Budi Santoso',
            'no_telepon' => '081234567890',
            'tanggal_mulai' => Carbon::now()->addDays(2)->toDateString(),
            'tanggal_selesai' => Carbon::now()->addDays(4)->toDateString(),
            'jumlah_hari' => 3,
            'harga_per_hari' => 350000,
            'total_harga' => 1050000,
            'status' => 'dikonfirmasi',
        ]);

        $this->actingAs($this->user)
            ->post("/bookings/{$booking->id}/status", ['status' => 'dibatalkan'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cars', ['id' => $car->id, 'status' => 'tersedia']);
    }

    public function test_invalid_status_is_rejected()
    {
        $car = $this->createCar();

        $booking = Booking::create([
            'car_id' => $car->id,
            'nama_penyewa' => 'Budi Santoso',
            'no_telepon' => '081234567890',
            'tanggal_mulai' => Carbon::now()->addDays(2)->toDateString(),
            'tanggal_selesai' => Carbon::now()->addDays(4)->toDateString(),
            'jumlah_hari' => 3,
            'harga_per_hari' => 350000,
            'total_harga' => 1050000,
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->user)
            ->post("/bookings/{$booking->id}/status", ['status' => 'status-aneh'])
            ->assertSessionHasErrors('status');
    }

    public function test_user_can_delete_booking()
    {
        $car = $this->createCar();

        $booking = Booking::create([
            'car_id' => $car->id,
            'nama_penyewa' => 'Budi Santoso',
            'no_telepon' => '081234567890',
            'tanggal_mulai' => Carbon::now()->addDays(2)->toDateString(),
            'tanggal_selesai' => Carbon::now()->addDays(4)->toDateString(),
            'jumlah_hari' => 3,
            'harga_per_hari' => 350000,
            'total_harga' => 1050000,
            'status' => 'dikonfirmasi',
        ]);

        $this->actingAs($this->user)
            ->delete("/bookings/{$booking->id}")
            ->assertRedirect(route('bookings.index'));

        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
        $this->assertDatabaseHas('cars', ['id' => $car->id, 'status' => 'tersedia']);
    }
}