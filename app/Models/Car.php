<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    protected $fillable = [
        'nama_mobil',
        'merk',
        'tipe',
        'tahun',
        'plat_nomor',
        'harga_sewa',
        'status',
        'deskripsi',
        'foto',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}