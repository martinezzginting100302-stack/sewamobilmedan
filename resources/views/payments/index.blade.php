@extends('layouts.app')

@section('title', 'Data Pembayaran - Sewa Mobil Medan')
@section('page_title', 'Data Pembayaran (Admin)')

@section('content')

    <div class="stats">
        <div class="stat stat-green">
            <div class="label">Total Lunas</div>
            <div class="value">Rp {{ number_format($totalLunas, 0, ',', '.') }}</div>
            <div class="sub">Pembayaran terverifikasi</div>
        </div>
        <div class="stat stat-amber">
            <div class="label">Menunggu Verifikasi</div>
            <div class="value">{{ $menungguCount }}</div>
            <div class="sub">Perlu tindakan admin</div>
        </div>
    </div>

    <div class="d-flex mb-2">
        <form method="GET" action="{{ route('payments.index') }}" class="form-inline">
            <div class="form-group">
                <label for="status_pembayaran">Filter Pembayaran</label>
                <select name="status_pembayaran" id="status_pembayaran" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach(['belum_bayar' => 'Belum Bayar', 'menunggu_verifikasi' => 'Menunggu Verifikasi', 'lunas' => 'Lunas', 'ditolak' => 'Ditolak'] as $val => $label)
                        <option value="{{ $val }}" {{ $filterPayment == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Terapkan</button>
            @if($filterPayment)
                <a href="{{ route('payments.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Penyewa</th>
                        <th>Mobil</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status Bayar</th>
                        <th>Bukti</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->nama_penyewa }}</strong>
                                <div class="text-muted">{{ $booking->user->email ?? '-' }}</div>
                            </td>
                            <td>{{ $booking->car->nama_mobil ?? '-' }}</td>
                            <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                            <td>{{ $booking->metode_pembayaran ? ucfirst(str_replace('_', ' ', $booking->metode_pembayaran)) : '-' }}</td>
                            <td>
                                <span class="badge badge-{{ ($booking->status_pembayaran ?? 'belum_bayar') === 'lunas' ? 'selesai' : (($booking->status_pembayaran ?? '') === 'menunggu_verifikasi' ? 'menunggu' : 'dibatalkan') }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status_pembayaran ?? 'belum bayar')) }}
                                </span>
                            </td>
                            <td>
                                @if($booking->bukti_pembayaran)
                                    <a href="{{ asset('storage/' . $booking->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline">Lihat</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline" href="{{ route('bookings.show', $booking) }}">Verifikasi</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty">
                                    <div class="big">💳</div>
                                    Belum ada data pembayaran.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
