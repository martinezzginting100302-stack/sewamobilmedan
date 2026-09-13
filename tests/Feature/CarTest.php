<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    private function carData(array $overrides = []): array
    {
        return array_merge([
            'nama_mobil' => 'Toyota Avanza',
            'merk' => 'Toyota',
            'tipe' => 'MPV',
            'tahun' => 2022,
            'plat_nomor' => 'BK 1234 AB',
            'harga_sewa' => 350000,
            'status' => 'tersedia',
            'deskripsi' => 'Mobil keluarga',
        ], $overrides);
    }

    public function test_cars_index_requires_authentication()
    {
        $this->get('/cars')->assertRedirect('/login');
    }

    public function test_cars_index_can_be_rendered()
    {
        Car::create($this->carData());

        $this->actingAs($this->user)
            ->get('/cars')
            ->assertOk()
            ->assertSee('Toyota Avanza');
    }

    public function test_user_can_create_car()
    {
        $this->actingAs($this->user)
            ->post('/cars', $this->carData())
            ->assertRedirect(route('cars.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cars', [
            'nama_mobil' => 'Toyota Avanza',
            'plat_nomor' => 'BK 1234 AB',
        ]);
    }

    public function test_car_creation_validates_required_fields()
    {
        $this->actingAs($this->user)
            ->post('/cars', [])
            ->assertSessionHasErrors(['nama_mobil', 'merk', 'tahun', 'plat_nomor', 'harga_sewa', 'status']);

        $this->assertDatabaseCount('cars', 0);
    }

    public function test_car_creation_rejects_duplicate_plate()
    {
        Car::create($this->carData());

        $this->actingAs($this->user)
            ->post('/cars', $this->carData())
            ->assertSessionHasErrors('plat_nomor');
    }

    public function test_user_can_store_car_with_photo()
    {
        Storage::fake('public');

        $data = $this->carData([
            'foto' => UploadedFile::fake()->image('avanza.png'),
        ]);

        $this->actingAs($this->user)
            ->post('/cars', $data)
            ->assertSessionHas('success');

        $car = Car::where('plat_nomor', 'BK 1234 AB')->first();

        $this->assertNotNull($car);
        $this->assertNotNull($car->foto);
        Storage::disk('public')->assertExists($car->foto);
    }

    public function test_user_can_update_car()
    {
        $car = Car::create($this->carData());

        $this->actingAs($this->user)
            ->put("/cars/{$car->id}", $this->carData([
                'nama_mobil' => 'Toyota Avanza Veloz',
                'harga_sewa' => 400000,
            ]))
            ->assertRedirect(route('cars.index'));

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'nama_mobil' => 'Toyota Avanza Veloz',
            'harga_sewa' => 400000,
        ]);
    }

    public function test_user_can_delete_car_without_booking()
    {
        $car = Car::create($this->carData());

        $this->actingAs($this->user)
            ->delete("/cars/{$car->id}")
            ->assertRedirect(route('cars.index'));

        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }

    public function test_car_with_booking_cannot_be_deleted()
    {
        $car = Car::create($this->carData());

        Booking::create([
            'car_id' => $car->id,
            'nama_penyewa' => 'Budi',
            'no_telepon' => '081234567890',
            'tanggal_mulai' => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'jumlah_hari' => 2,
            'harga_per_hari' => 350000,
            'total_harga' => 700000,
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->user)
            ->delete("/cars/{$car->id}")
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('cars', ['id' => $car->id]);
    }
}