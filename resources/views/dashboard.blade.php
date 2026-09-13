@extends('layouts.app')

@section('title', 'Dashboard - Sewa Mobil Medan')
@section('page_title', 'Dashboard')

@section('content')

    <div class="stats">

        <div class="stat stat-blue">
            <div class="label">Total Mobil</div>
            <div class="value">{{ $totalMobil }}</div>
            <div class="sub">Semua armada terdaftar</div>
        </div>

        <div class="stat stat-green">
            <div class="label">Mobil Tersedia</div>
            <div class="value">{{ $mobilTersedia }}</div>
            <div class="sub">Siap untuk disewa</div>
        </div>

        <div class="stat stat-indigo">
            <div class="label">Sedang Disewa</div>
            <div class="value">{{ $mobilDisewa }}</div>
            <div class="sub">Sedang digunakan pelanggan</div>
        </div>

        <div class="stat stat-amber">
            <div class="label">Maintenance</div>
            <div class="value">{{ $mobilMaintenance }}</div>
            <div class="sub">Dalam perawatan</div>
        </div>

        <div class="stat stat-blue">
            <div class="label">Booking Menunggu</div>
            <div class="value">{{ $bookingMenunggu }}</div>
            <div class="sub">Perlu konfirmasi</div>
        </div>

        <div class="stat stat-green">
            <div class="label">Pendapatan</div>
            <div class="value">Rp {{ number_format($pendapatan, 0, ',', '.') }}</div>
            <div class="sub">Dari booking selesai & terkonfirmasi</div>
        </div>

    </div>

    <div class="card">
        <h2>Booking Terbaru</h2>

        @if($bookingTerbaru->isEmpty())
            <div class="empty">
                <div class="big">📅</div>
                Belum ada booking. Mulai buat booking pertama Anda.
            </div>
        @else

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Mobil</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookingTerbaru as $booking)
                            <tr>
                                <td>{{ $booking->nama_penyewa }}</td>
                                <td>{{ $booking->car->nama_mobil }}</td>
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
                                       href="{{ route('bookings.show', $booking) }}">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif
    </div>

    <div class="d-flex mt-2">
        <a class="btn btn-primary" href="{{ route('cars.create') }}">+ Tambah Mobil</a>
        <a class="btn btn-secondary" href="{{ route('bookings.create') }}">Buat Booking</a>
        <a class="btn btn-outline" href="{{ route('reports.index') }}">Lihat Laporan</a>
    </div>

@endsection