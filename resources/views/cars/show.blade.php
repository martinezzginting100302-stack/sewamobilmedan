@extends('layouts.app')

@section('title', 'Detail Mobil - Sewa Mobil Medan')
@section('page_title', 'Detail Mobil')

@section('content')

    <div class="card">
        <div class="d-flex mb-2" style="justify-content:space-between;align-items:flex-start;">
            <div>
                <h2 style="margin:0 0 4px;">{{ $car->nama_mobil }}</h2>
                <span class="badge badge-{{ $car->status }}">{{ ucfirst($car->status) }}</span>
            </div>

            <div class="d-flex">
                <a href="{{ route('cars.edit', $car) }}" class="btn btn-sm btn-primary">Edit</a>
                <form action="{{ route('cars.destroy', $car) }}" method="POST" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus {{ $car->nama_mobil }}?')">
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        @if($car->foto)
            <img src="{{ asset('storage/' . $car->foto) }}"
                 alt="{{ $car->nama_mobil }}" class="car-photo-lg" style="margin-bottom:18px;">
        @else
            <div class="placeholder-photo-lg mb-2">Tidak ada foto</div>
        @endif

        <table class="detail-table">
            <tr><th>Nama Mobil</th><td>{{ $car->nama_mobil }}</td></tr>
            <tr><th>Merk</th><td>{{ $car->merk }}</td></tr>
            <tr><th>Tipe</th><td>{{ $car->tipe ?? '-' }}</td></tr>
            <tr><th>Tahun</th><td>{{ $car->tahun ?? '-' }}</td></tr>
            <tr><th>Plat Nomor</th><td>{{ $car->plat_nomor }}</td></tr>
            <tr>
                <th>Harga Sewa</th>
                <td>Rp {{ number_format($car->harga_sewa, 0, ',', '.') }} / hari</td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="badge badge-{{ $car->status }}">{{ ucfirst($car->status) }}</span></td>
            </tr>
            <tr><th>Deskripsi</th><td>{{ $car->deskripsi ?? '-' }}</td></tr>
            <tr><th>Dibuat</th><td>{{ $car->created_at->format('d M Y - H:i') }}</td></tr>
            <tr>
                <th>Total Booking</th>
                <td>{{ $car->bookings_count ?? $car->bookings->count() }} booking</td>
            </tr>
        </table>
    </div>

    @if($car->relationLoaded('bookings') && $car->bookings->isNotEmpty())
        <div class="card">
            <h2>Riwayat Booking</h2>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($car->bookings as $booking)
                            <tr>
                                <td>{{ $booking->nama_penyewa }}</td>
                                <td>
                                    {{ $booking->tanggal_mulai->format('d M Y') }}
                                    – {{ $booking->tanggal_selesai->format('d M Y') }}
                                </td>
                                <td>{{ $booking->jumlah_hari }} hari</td>
                                <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-{{ $booking->status }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline"
                                       href="{{ route('bookings.show', $booking) }}">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-2">
        <a href="{{ route('cars.index') }}" class="btn btn-secondary">← Kembali ke Data Mobil</a>
    </div>

@endsection