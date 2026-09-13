<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sewamobilmedan.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->call(CarSeeder::class);
    }
}