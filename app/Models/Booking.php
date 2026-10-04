<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'car_id',
        'nama_penyewa',
        'no_telepon',
        'alamat',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'harga_per_hari',
        'total_harga',
        'status',
        'metode_pembayaran',
        'status_pembayaran',
        'bukti_pembayaran',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}