<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_mobil' => $this->nama_mobil,
            'merk' => $this->merk,
            'tipe' => $this->tipe,
            'tahun' => $this->tahun,
            'plat_nomor' => $this->plat_nomor,
            'harga_sewa' => (float) $this->harga_sewa,
            'harga_sewa_formatted' => 'Rp ' . number_format($this->harga_sewa, 0, ',', '.'),
            'status' => $this->status,
            'deskripsi' => $this->deskripsi,
            'foto' => $this->foto,
            'foto_url' => $this->foto ? asset('storage/' . $this->foto) : null,
            'bookings_count' => $this->whenCounted('bookings_count'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
