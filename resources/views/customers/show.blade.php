@extends('layouts.app')

@section('title', 'Detail Customer - Sewa Mobil Medan')
@section('page_title', 'Detail Customer')

@section('content')

    <div class="card">
        <h2 style="margin-bottom:4px;">{{ $user->name }}</h2>
        <div class="text-muted mb-2">{{ $user->email }} — terdaftar {{ $user->created_at->format('d M Y') }}</div>

        <table class="detail-table">
            <tr><th>Nama</th><td>{{ $user->name }}</td></tr>
            <tr><th>Email</th><td>{{ $user->email }}</td></tr>
            <tr><th>Role</th><td><span class="badge badge-tersedia">{{ $user->role }}</span></td></tr>
            <tr><th>Total Peminjaman</th><td>{{ $user->bookings->count() }} transaksi</td></tr>
        </table>
    </div>

    <div class="card">
        <h2>Riwayat Peminjaman</h2>
        @if($user->bookings->isEmpty())
            <div class="empty">
                <div class="big">📅</div>
                Customer ini belum pernah mengajukan peminjaman.
            </div>
        @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mobil</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->bookings as $booking)
                        <tr>
                            <td>{{ $booking->car->nama_mobil ?? '-' }}</td>
                            <td>{{ $booking->tanggal_mulai->format('d M Y') }} – {{ $booking->tanggal_selesai->format('d M Y') }}</td>
                            <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                            <td><span class="badge badge-{{ ($booking->status_pembayaran ?? 'belum_bayar') === 'lunas' ? 'selesai' : 'menunggu' }}">{{ ucfirst(str_replace('_', ' ', $booking->status_pembayaran ?? 'belum bayar')) }}</span></td>
                            <td><a class="btn btn-sm btn-outline" href="{{ route('bookings.show', $booking) }}">Detail</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="mt-2">
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">← Kembali ke Data Customer</a>
    </div>

@endsection
