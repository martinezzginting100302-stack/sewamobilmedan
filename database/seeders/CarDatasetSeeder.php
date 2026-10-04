<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

/**
 * Impor armada rental dari "Car Dataset 1945-2020.csv".
 *
 * Pemetaan ke kebutuhan SewaMobilMedan:
 *  - merk        <- Make
 *  - nama_mobil  <- "Make Model" (mis. "Toyota Yaris")
 *  - tipe        <- Body_type (Crossover/Hatchback/Sedan/dll)
 *  - tahun       <- Year_from (difilter >= 2012, layak rental)
 *  - plat_nomor  <- format Medan "BK XXXX XX", unik & deterministik
 *  - harga_sewa  <- tier per tipe bodi + umur unit
 *  - status      <- mayoritas tersedia, sebagian disewa/maintenance
 *  - deskripsi   <- spesifikasi (transmisi, kursi, mesin, BBM, penggerak)
 *  - foto        <- null (dataset tidak menyediakan foto)
 *
 * Jalankan: php artisan db:seed --class=CarDatasetSeeder
 * Path CSV bisa dioverride via env CAR_DATASET_PATH.
 */
class CarDatasetSeeder extends Seeder
{
    private const MAKES = [
        'Toyota', 'Honda', 'Suzuki', 'Mitsubishi', 'Nissan', 'Daihatsu',
        'Mazda', 'Hyundai', 'Kia', 'Isuzu', 'Wuling', 'Ford', 'Chevrolet',
    ];

    private const BODIES = [
        'Minivan', 'Crossover', 'Sedan', 'Hatchback', 'Wagon', 'Liftback', 'Pickup',
    ];

    private const LIMIT = 40;

    private const BASE_PRICE = [
        'Minivan' => 550000,
        'Crossover' => 500000,
        'Sedan' => 400000,
        'Hatchback' => 350000,
        'Wagon' => 400000,
        'Liftback' => 375000,
        'Pickup' => 450000,
    ];

    public function run(): void
    {
        $path = env('CAR_DATASET_PATH', 'C:/Users/Administrator/Downloads/Car Dataset 1945-2020.csv');

        if (! is_file($path) || ! is_readable($path)) {
            $this->command->error("File dataset tidak ditemukan: {$path}");
            $this->command->line('Set env CAR_DATASET_PATH ke lokasi file CSV.');

            return;
        }

        $groups = $this->collectBestRows($path);

        if (empty($groups)) {
            $this->command->error('Tidak ada baris yang cocok dengan filter rental.');

            return;
        }

        // Ambil bergiliran per merk agar armada bervariasi
        // (bukan hanya merk prioritas pertama).
        $selected = $this->roundRobin($groups, self::LIMIT);

        // Bersihkan hasil impor sebelumnya (namespace plat BK 10xx)
        // agar seeder idempoten meski urutan seleksi berubah.
        Car::where('plat_nomor', 'like', 'BK 10__ __')->delete();

        $count = 0;

        foreach ($selected as $i => $row) {
            $make = trim($row['Make']);
            $model = trim($row['Modle']);
            $body = trim($row['Body_type']);
            $tahun = max(2012, min((int) $row['Year_from'], (int) date('Y')));

            $plat = $this->plateFor($i);

            Car::updateOrCreate(
                ['plat_nomor' => $plat],
                [
                    'nama_mobil' => "{$make} {$model}",
                    'merk' => $make,
                    'tipe' => $body !== '' ? $body : null,
                    'tahun' => $tahun,
                    'harga_sewa' => $this->priceFor($body, $tahun),
                    'status' => $this->statusFor($i),
                    'deskripsi' => $this->describe($make, $model, $row),
                    'foto' => null,
                ]
            );
            $count++;
        }

        $this->command->info("Berhasil memasukkan {$count} mobil dari dataset.");
    }

    /**
     * Kumpulkan satu baris terbaik (spesifikasi paling lengkap)
     * per kombinasi Make + Model yang layak rental.
     *
     * @return array<int, array<string, string>>
     */
    private function collectBestRows(string $path): array
    {
        $specCols = [
            'transmission', 'number_of_seats', 'engine_type', 'capacity_cm3',
            'engine_hp', 'mixed_fuel_consumption_per_100_km_l',
            'drive_wheels', 'number_of_doors', 'fuel_tank_capacity_l',
        ];

        $best = [];
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        // Hilangkan BOM bila ada.
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== count($header)) {
                continue;
            }
            $row = array_combine($header, $data);

            $make = trim($row['Make'] ?? '');
            $model = trim($row['Modle'] ?? '');
            $body = trim($row['Body_type'] ?? '');
            $yearFrom = (int) ($row['Year_from'] ?? 0);

            if ($make === '' || $model === '') {
                continue;
            }
            if (! in_array($make, self::MAKES, true)) {
                continue;
            }
            if ($yearFrom < 2012) {
                continue;
            }
            if (! in_array($body, self::BODIES, true)) {
                continue;
            }

            $score = 0;
            foreach ($specCols as $col) {
                if (trim($row[$col] ?? '') !== '') {
                    $score++;
                }
            }

            $key = "{$make}|{$model}|{$body}";
            if (! isset($best[$key]) || $score > $best[$key]['score']) {
                $best[$key] = ['row' => $row, 'score' => $score];
            }
        }
        fclose($handle);

        $priority = array_flip(self::MAKES);
        $rows = array_map(fn ($item) => $item['row'], array_values($best));

        usort($rows, function ($a, $b) use ($priority) {
            $pa = $priority[trim($a['Make'])] ?? 99;
            $pb = $priority[trim($b['Make'])] ?? 99;

            return $pa <=> $pb ?: strcmp(trim($a['Modle']), trim($b['Modle']));
        });

        return $rows;
    }

    /**
     * Ambil baris bergiliran per merk (round-robin) mengikuti
     * urutan prioritas merk, supaya armada bervariasi.
     *
     * @param array<int, array<string, string>> $rows
     * @return array<int, array<string, string>>
     */
    private function roundRobin(array $rows, int $limit): array
    {
        $buckets = [];
        foreach ($rows as $row) {
            $buckets[trim($row['Make'])][] = $row;
        }

        $orderedMakes = array_values(array_intersect(self::MAKES, array_keys($buckets)));
        foreach (array_keys($buckets) as $make) {
            if (! in_array($make, $orderedMakes, true)) {
                $orderedMakes[] = $make;
            }
        }

        $selected = [];
        $i = 0;
        while (count($selected) < $limit) {
            $progress = false;
            foreach ($orderedMakes as $make) {
                if (isset($buckets[$make][$i])) {
                    $selected[] = $buckets[$make][$i];
                    $progress = true;
                    if (count($selected) >= $limit) {
                        break 2;
                    }
                }
            }
            if (! $progress) {
                break;
            }
            $i++;
        }

        return $selected;
    }

    private function plateFor(int $index): string
    {
        $number = 1000 + $index;
        $first = chr(65 + ($index % 26));
        $second = chr(65 + (int) (($index / 26) % 26));

        return "BK {$number} {$first}{$second}";
    }

    private function priceFor(string $body, int $tahun): int
    {
        $base = self::BASE_PRICE[$body] ?? 400000;
        $price = $base + max(0, $tahun - 2012) * 15000;

        return (int) (round($price / 5000) * 5000);
    }

    private function statusFor(int $index): string
    {
        if ($index % 20 === 19) {
            return 'maintenance';
        }
        if ($index % 10 === 9) {
            return 'disewa';
        }

        return 'tersedia';
    }

    private function describe(string $make, string $model, array $row): string
    {
        $parts = [];
        $g = fn (string $key) => trim($row[$key] ?? '');

        if ($g('transmission') !== '') {
            $parts[] = "transmisi {$g('transmission')}";
        }
        if ($g('number_of_seats') !== '') {
            $parts[] = "{$g('number_of_seats')} kursi";
        }
        if ($g('engine_type') !== '' || $g('capacity_cm3') !== '' || $g('engine_hp') !== '') {
            $mesin = trim("{$g('engine_type')} {$g('capacity_cm3')}cc");
            if ($g('engine_hp') !== '') {
                $mesin .= " ({$g('engine_hp')} HP)";
            }
            $parts[] = "mesin {$mesin}";
        }
        if ($g('mixed_fuel_consumption_per_100_km_l') !== '') {
            $parts[] = "konsumsi BBM ±{$g('mixed_fuel_consumption_per_100_km_l')} L/100km";
        }
        if ($g('drive_wheels') !== '') {
            $parts[] = "penggerak {$g('drive_wheels')}";
        }

        $body = trim($row['Body_type'] ?? '');
        $desc = "{$make} {$model}" . ($body !== '' ? " tipe {$body}" : '');
        if (! empty($parts)) {
            $desc .= ' dengan ' . implode(', ', $parts);
        }
        $desc .= '. Cocok untuk perjalanan keluarga maupun bisnis di Medan.';

        return $desc;
    }
}
