# SewaMobilMedan

Sistem informasi rental mobil berbasis web yang dibangun dengan **Laravel 12**. Aplikasi ini digunakan untuk mengelola armada mobil, pencatatan booking penyewaan, manajemen status sewa, hingga laporan pendapatan.

## FITUR

- **Autentikasi**
  - Registrasi, login, dan logout
  - Proteksi seluruh halaman admin (wajib login)

- **Manajemen Mobil**
  - CRUD data mobil (nama, merk, tipe, tahun, plat nomor, harga sewa, status, deskripsi)
  - Upload foto mobil (format JPG/PNG/WEBP, maks 2 MB)
  - Status mobil: `tersedia`, `disewa`, `maintenance`
  - Mobil yang memiliki booking tidak dapat dihapus

- **Manajemen Booking**
  - Pembuatan booking dengan pilihan tanggal sewa
  - Perhitungan otomatis jumlah hari dan total harga
  - Deteksi otomatis tabrakan jadwal (mobil yang sudah dibooking pada tanggal tersebut tidak bisa dipesan)
  - Edit booking, hapus booking
  - Alur status: `menunggu` → `dikonfirmasi` → `selesai` / `dibatalkan`
  - Sinkronisasi otomatis status mobil (ada booking aktif = "disewa", selesai/batal = "tersedia")
  - Filter booking berdasarkan status

- **Dashboard**
  - Ringkasan armada: total, tersedia, disewa, maintenance
  - Jumlah booking menunggu, booking aktif, booking selesai
  - Total pendapatan
  - Daftar booking terbaru

- **Laporan**
  - Filter berdasarkan rentang tanggal dan status
  - Ringkasan: total booking, total hari sewa, nilai booking, pendapatan
  - Export ke CSV

## TEKNOLOGI

| Bagian     | Teknologi            |
|------------|----------------------|
| Framework  | Laravel 12           |
| Bahasa     | PHP 8.3+             |
| Database   | MySQL / MariaDB 5.7+ |
| Frontend   | Blade + CSS (tanpa framework) |

## STRUKTUR UTAMA

```
app/Http/Controllers/
├── Auth/
│   ├── LoginController.php
│   └── RegisterController.php
├── BookingController.php
├── CarController.php
├── DashboardController.php
└── ReportController.php

resources/views/
├── auth/          # login & register
├── bookings/      # halaman booking
├── cars/          # halaman data mobil
├── layouts/app.blade.php  # layout utama (sidebar)
└── reports/       # halaman laporan

routes/web.php    # definisi seluruh route
database/seeders/ # seeder (admin + data mobil)
tests/Feature/    # test otomatis
```

## INSTALASI

1. Install Laravel 12 + dependencies:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

2. Buat database MySQL (misal `sewa_mobil_medan`) lalu sesuaikan konfigurasi di `.env`:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sewa_mobil_medan
   DB_USERNAME=root
   DB_PASSWORD=password_anda
   ```

3. Jalankan migrasi, seeding, dan link storage:

   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   ```

4. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

   Akses di `http://127.0.0.1:8000`.

### Akses via domain lokal (Laragon)

Aplikasi juga bisa diakses melalui `http://sewamobilmedan` (Apache Laragon):

1. Pastikan entry berikut ada di `C:\Windows\System32\drivers\etc\hosts`:

   ```
   127.0.0.1 sewamobilmedan
   ```

2. Pastikan file vhost berikut ada di `C:\laragon\etc\apache2\sites-enabled\sewamobilmedan.conf`:

   ```
   <VirtualHost *:80>
       ServerName sewamobilmedan
       DocumentRoot "C:/laragon/www/SewaMobilMedan/public"
       <Directory "C:/laragon/www/SewaMobilMedan/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. Restart Apache lalu buka `http://sewamobilmedan`.

## AKUN SEEDER (DEMO)

Kredensial administrator yang dibuat oleh seeder:

| Email                    | Password   |
|--------------------------|------------|
| `admin@sewamobilmedan.com` | `admin123` |

## TEST

Jalankan seluruh test otomatis:

```bash
php artisan test
```

Test mencakup: autentikasi, CRUD mobil, upload foto, validasi, deteksi tabrakan booking, manajemen status, dan sinkronisasi status mobil.

---

© 2026 SewaMobilMedan. Sistem Rental Mobil Medan.