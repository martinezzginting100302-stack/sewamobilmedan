<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'car_id' => $this->car_id,
            'nama_penyewa' => $this->nama_penyewa,
            'no_telepon' => $this->no_telepon,
            'alamat' => $this->alamat,
            'tanggal_mulai' => $this->tanggal_mulai?->format('Y-m-d'),
            'tanggal_selesai' => $this->tanggal_selesai?->format('Y-m-d'),
            'jumlah_hari' => $this->jumlah_hari,
            'harga_per_hari' => (float) $this->harga_per_hari,
            'total_harga' => (float) $this->total_harga,
            'total_harga_formatted' => 'Rp ' . number_format($this->total_harga, 0, ',', '.'),
            'status' => $this->status,
            'metode_pembayaran' => $this->metode_pembayaran,
            'status_pembayaran' => $this->status_pembayaran ?? 'belum_bayar',
            'bukti_pembayaran' => $this->bukti_pembayaran,
            'bukti_pembayaran_url' => $this->bukti_pembayaran ? asset('storage/' . $this->bukti_pembayaran) : null,
            'car' => $this->whenLoaded('car', fn () => new CarResource($this->car)),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
