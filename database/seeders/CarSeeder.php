<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $cars = [
            [
                'nama_mobil' => 'Toyota Avanza',
                'merk' => 'Toyota',
                'tipe' => 'MPV',
                'tahun' => 2022,
                'plat_nomor' => 'BK 1234 AB',
                'harga_sewa' => 350000,
                'status' => 'tersedia',
                'deskripsi' => 'Avanza terbaru, kapasitas 7 penumpang, konsumsi BBM irit, AC dingin, cocok untuk keluarga.',
            ],
            [
                'nama_mobil' => 'Daihatsu Xenia',
                'merk' => 'Daihatsu',
                'tipe' => 'MPV',
                'tahun' => 2021,
                'plat_nomor' => 'BK 2345 CD',
                'harga_sewa' => 300000,
                'status' => 'tersedia',
                'deskripsi' => 'Xenia matic, nyaman untuk perjalanan antar kota maupun dalam kota.',
            ],
            [
                'nama_mobil' => 'Honda Brio',
                'merk' => 'Honda',
                'tipe' => 'Hatchback',
                'tahun' => 2023,
                'plat_nomor' => 'BK 3456 EF',
                'harga_sewa' => 250000,
                'status' => 'tersedia',
                'deskripsi' => 'Brio Satya terbaru, lincah di perkotaan, irit bahan bakar.',
            ],
            [
                'nama_mobil' => 'Toyota Innova Reborn',
                'merk' => 'Toyota',
                'tipe' => 'MPV',
                'tahun' => 2020,
                'plat_nomor' => 'BK 4567 GH',
                'harga_sewa' => 600000,
                'status' => 'tersedia',
                'deskripsi' => 'Innova Reborn diesel, lega dan nyaman untuk perjalanan jauh antar provinsi.',
            ],
            [
                'nama_mobil' => 'Suzuki Ertiga',
                'merk' => 'Suzuki',
                'tipe' => 'MPV',
                'tahun' => 2022,
                'plat_nomor' => 'BK 5678 IJ',
                'harga_sewa' => 320000,
                'status' => 'tersedia',
                'deskripsi' => 'Ertiga baru, kapasitas 7 penumpang, mesin bertenaga dan irit.',
            ],
            [
                'nama_mobil' => 'Toyota Fortuner',
                'merk' => 'Toyota',
                'tipe' => 'SUV',
                'tahun' => 2021,
                'plat_nomor' => 'BK 6789 KL',
                'harga_sewa' => 900000,
                'status' => 'tersedia',
                'deskripsi' => 'Fortuner 4x2 diesel, gagah untuk medan luar kota, dengan sopir opsional.',
            ],
            [
                'nama_mobil' => 'Honda Jazz',
                'merk' => 'Honda',
                'tipe' => 'Hatchback',
                'tahun' => 2019,
                'plat_nomor' => 'BK 7890 MN',
                'harga_sewa' => 280000,
                'status' => 'tersedia',
                'deskripsi' => 'Jazz RS matic, sporty dan irit untuk kebutuhan harian.',
            ],
            [
                'nama_mobil' => 'Mitsubishi Xpander',
                'merk' => 'Mitsubishi',
                'tipe' => 'MPV',
                'tahun' => 2022,
                'plat_nomor' => 'BK 8901 OP',
                'harga_sewa' => 380000,
                'status' => 'tersedia',
                'deskripsi' => 'Xpander Ultimate, desain modern, nyaman untuk keluarga.',
            ],
            [
                'nama_mobil' => 'Toyota Camry',
                'merk' => 'Toyota',
                'tipe' => 'Sedan',
                'tahun' => 2020,
                'plat_nomor' => 'BK 9012 QR',
                'harga_sewa' => 750000,
                'status' => 'tersedia',
                'deskripsi' => 'Camry luxury, cocok untuk tamu VVIP atau keperluan acara penting.',
            ],
            [
                'nama_mobil' => 'Hyundai Stargazer',
                'merk' => 'Hyundai',
                'tipe' => 'MPV',
                'tahun' => 2023,
                'plat_nomor' => 'BK 0987 ST',
                'harga_sewa' => 400000,
                'status' => 'tersedia',
                'deskripsi' => 'Stargazer terbaru, futuristik, fitur keselamatan lengkap.',
            ],
            [
                'nama_mobil' => 'Nissan Grand Livina',
                'merk' => 'Nissan',
                'tipe' => 'MPV',
                'tahun' => 2018,
                'plat_nomor' => 'BK 1230 UV',
                'harga_sewa' => 330000,
                'status' => 'maintenance',
                'deskripsi' => 'Grand Livina sedang dalam perawatan berkala, akan kembali aktif.'
            ],
            [
                'nama_mobil' => 'Isuzu Elf Long',
                'merk' => 'Isuzu',
                'tipe' => 'Bus / Elf',
                'tahun' => 2019,
                'plat_nomor' => 'BK 1254 WX',
                'harga_sewa' => 950000,
                'status' => 'tersedia',
                'deskripsi' => 'Elf long, 15 penumpang, cocok untuk study tour dan keluarga besar.',
            ],
        ];

        DB::table('bookings')->delete();

        DB::table('cars')->delete();

        foreach ($cars as $car) {
            Car::create($car);
        }
    }
}