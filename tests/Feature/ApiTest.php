<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Sanctum guard caches the authenticated user inside the app instance,
     * so switching Bearer tokens within one test needs forgetGuards().
     * (Real HTTP requests boot a fresh app, unaffected.)
     */
    protected function apiAs(string $token)
    {
        $this->app['auth']->forgetGuards();

        return $this->withToken($token);
    }
    protected function adminToken(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test')->plainTextToken;

        return [$admin, $token];
    }

    protected function customerToken(): array
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $token = $customer->createToken('test')->plainTextToken;

        return [$customer, $token];
    }

    public function test_api_register_and_login()
    {
        $this->postJson('/api/register', [
            'name' => 'Budi API',
            'email' => 'budi@api.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $this->postJson('/api/login', [
            'email' => 'budi@api.test',
            'password' => 'password123',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $this->postJson('/api/login', [
            'email' => 'budi@api.test',
            'password' => 'salah',
        ])->assertUnauthorized();
    }

    public function test_api_unauthenticated_rejected()
    {
        $this->getJson('/api/cars')->assertUnauthorized();
        $this->getJson('/api/bookings')->assertUnauthorized();
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_customer_only_sees_available_cars()
    {
        [$customer, $token] = $this->customerToken();

        Car::create([
            'nama_mobil' => 'Avanza', 'merk' => 'Toyota', 'tahun' => 2022,
            'plat_nomor' => 'BK 1111 AA', 'harga_sewa' => 350000, 'status' => 'tersedia',
        ]);
        Car::create([
            'nama_mobil' => 'Rusak', 'merk' => 'Daihatsu', 'tahun' => 2020,
            'plat_nomor' => 'BK 2222 BB', 'harga_sewa' => 300000, 'status' => 'maintenance',
        ]);

        $res = $this->apiAs($token)->getJson('/api/cars')->assertOk();
        $this->assertCount(1, $res->json('data'));
        $this->assertEquals('tersedia', $res->json('data.0.status'));

        // Admin melihat semua
        [$admin, $adminToken] = $this->adminToken();
        $resAdmin = $this->apiAs($adminToken)->getJson('/api/cars')->assertOk();
        $this->assertGreaterThanOrEqual(2, count($resAdmin->json('data')));
    }

    public function test_customer_cannot_access_admin_routes()
    {
        [$customer, $token] = $this->customerToken();

        $this->apiAs($token)->postJson('/api/cars', [
            'nama_mobil' => 'X', 'merk' => 'Y', 'tahun' => 2022,
            'plat_nomor' => 'BK 9999 ZZ', 'harga_sewa' => 100000, 'status' => 'tersedia',
        ])->assertForbidden();

        $this->apiAs($token)->getJson('/api/customers')->assertForbidden();
        $this->apiAs($token)->getJson('/api/payments')->assertForbidden();
    }

    public function test_booking_flow_with_payment()
    {
        [$customer, $token] = $this->customerToken();
        [$admin, $adminToken] = $this->adminToken();

        $car = Car::create([
            'nama_mobil' => 'Innova', 'merk' => 'Toyota', 'tahun' => 2023,
            'plat_nomor' => 'BK 3333 CC', 'harga_sewa' => 500000, 'status' => 'tersedia',
        ]);

        // Customer ajukan booking
        $start = now()->addDay()->format('Y-m-d');
        $end = now()->addDays(2)->format('Y-m-d');

        $res = $this->apiAs($token)->postJson('/api/bookings', [
            'car_id' => $car->id,
            'nama_penyewa' => 'Budi',
            'no_telepon' => '08123456789',
            'tanggal_mulai' => $start,
            'tanggal_selesai' => $end,
            'metode_pembayaran' => 'transfer_bank',
        ])->assertCreated()->assertJsonPath('success', true);

        $bookingId = $res->json('data.id');
        $this->assertEquals(2, $res->json('data.jumlah_hari'));
        $this->assertEquals(1000000, (float) $res->json('data.total_harga'));

        // Customer lain tidak boleh intip
        [$other, $otherToken] = $this->customerToken();
        $this->apiAs($otherToken)->getJson("/api/bookings/{$bookingId}")->assertForbidden();

        // Pemilik boleh lihat
        $this->apiAs($token)->getJson("/api/bookings/{$bookingId}")->assertOk();

        // Upload pembayaran (cash tanpa file)
        $this->apiAs($token)->postJson("/api/bookings/{$bookingId}/payment", [
            'metode_pembayaran' => 'cash',
        ])->assertOk()->assertJsonPath('data.status_pembayaran', 'menunggu_verifikasi');

        // Admin verifikasi lunas
        $this->apiAs($adminToken)->postJson("/api/bookings/{$bookingId}/payment/verify", [
            'status_pembayaran' => 'lunas',
        ])->assertOk()->assertJsonPath('data.status_pembayaran', 'lunas');

        // Admin ubah status booking
        $this->apiAs($adminToken)->postJson("/api/bookings/{$bookingId}/status", [
            'status' => 'dikonfirmasi',
        ])->assertOk();

        // Double booking tanggal sama harus ditolak
        $this->apiAs($token)->postJson('/api/bookings', [
            'car_id' => $car->id,
            'nama_penyewa' => 'Budi',
            'no_telepon' => '08123456789',
            'tanggal_mulai' => $start,
            'tanggal_selesai' => $end,
        ])->assertStatus(422);
    }

    public function test_dashboard_roles()
    {
        [$customer, $token] = $this->customerToken();
        [$admin, $adminToken] = $this->adminToken();

        $this->apiAs($adminToken)->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonStructure(['data' => ['total_mobil', 'total_customer', 'booking_terbaru']]);

        $this->apiAs($token)->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonStructure(['data' => ['mobil_tersedia', 'booking_saya']]);
    }
}
