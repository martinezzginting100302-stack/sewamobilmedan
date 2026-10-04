@extends('layouts.app')

@section('title', 'Pembayaran Saya - Sewa Mobil Medan')
@section('page_title', 'Pembayaran Saya')

@section('content')

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mobil</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status Bayar</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $booking)
                        <tr>
                            <td>{{ $booking->car->nama_mobil ?? '-' }}</td>
                            <td>{{ $booking->tanggal_mulai->format('d M Y') }} – {{ $booking->tanggal_selesai->format('d M Y') }}</td>
                            <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                            <td>{{ $booking->metode_pembayaran ? ucfirst(str_replace('_', ' ', $booking->metode_pembayaran)) : '-' }}</td>
                            <td>
                                <span class="badge badge-{{ ($booking->status_pembayaran ?? 'belum_bayar') === 'lunas' ? 'selesai' : (($booking->status_pembayaran ?? '') === 'menunggu_verifikasi' ? 'menunggu' : 'dibatalkan') }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status_pembayaran ?? 'belum bayar')) }}
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="{{ route('bookings.show', $booking) }}">
                                    {{ ($booking->status_pembayaran ?? '') === 'lunas' ? 'Detail' : 'Bayar' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty">
                                    <div class="big">💳</div>
                                    Belum ada pembayaran. Ajukan sewa terlebih dahulu.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-2">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Kembali ke Dashboard</a>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ Ajukan Sewa</a>
    </div>

@endsection
