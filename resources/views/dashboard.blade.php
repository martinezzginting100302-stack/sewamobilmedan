@extends('layouts.app')

@section('title', 'Dashboard - SewaMobilMedan')
@section('page_title', 'Dashboard')

@section('content')

@if($isAdmin)
    <div class="stats">
        <div class="stat stat-blue">
            <div class="label">Total Mobil</div>
            <div class="value">{{ $totalMobil }}</div>
            <div class="sub">Kelola di Data Mobil</div>
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
            <div class="label">Peminjaman Menunggu</div>
            <div class="value">{{ $bookingMenunggu }}</div>
            <div class="sub">Kelola di Data Peminjaman</div>
        </div>

        <div class="stat stat-amber">
            <div class="label">Pembayaran Perlu Verifikasi</div>
            <div class="value">{{ $pembayaranMenunggu }}</div>
            <div class="sub">Kelola di Data Pembayaran</div>
        </div>

        <div class="stat stat-indigo">
            <div class="label">Total Customer</div>
            <div class="value">{{ $totalCustomer }}</div>
            <div class="sub">Kelola di Data Customer</div>
        </div>

        <div class="stat stat-green">
            <div class="label">Pendapatan (Lunas)</div>
            <div class="value">Rp {{ number_format($pembayaranLunas, 0, ',', '.') }}</div>
            <div class="sub">Dari pembayaran lunas</div>
        </div>
    </div>

    <div class="card">
        <h2>Peminjaman Terbaru</h2>

        @if($bookingTerbaru->isEmpty())
            <div class="empty">
                <div class="big">📅</div>
                Belum ada peminjaman.
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
                            <th>Pembayaran</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookingTerbaru as $booking)
                            <tr>
                                <td>{{ $booking->nama_penyewa }}</td>
                                <td>{{ $booking->car->nama_mobil ?? '-' }}</td>
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
                                    <span class="badge badge-{{ ($booking->status_pembayaran ?? 'belum_bayar') === 'lunas' ? 'selesai' : (($booking->status_pembayaran ?? '') === 'menunggu_verifikasi' ? 'menunggu' : 'dibatalkan') }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status_pembayaran ?? 'belum bayar')) }}
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

@else
    <div class="card mb-2" style="border-left:3px solid #2563eb;">
        <h2 style="margin-bottom:6px;">Halo, {{ auth()->user()->name }} 👋</h2>
        <div class="text-muted">Anda hanya dapat melihat mobil yang tersedia, melihat spesifikasi, mengajukan peminjaman, dan melakukan pembayaran.</div>
        <div class="d-flex mt-2">
            <a class="btn btn-primary" href="{{ route('cars.index') }}">🚙 Lihat Daftar Mobil</a>
            <a class="btn btn-secondary" href="{{ route('bookings.create') }}">📅 Ajukan Sewa</a>
            <a class="btn btn-outline" href="{{ route('payments.mine') }}">💳 Pembayaran Saya</a>
        </div>
    </div>

    <div class="card">
        <h2>Mobil Tersedia</h2>

        @if($mobilTersediaList->isEmpty())
            <div class="empty">
                <div class="big">🚙</div>
                Belum ada mobil yang tersedia saat ini.
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mobil</th>
                            <th>Merk / Tipe</th>
                            <th>Tahun</th>
                            <th>Harga / Hari</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mobilTersediaList as $car)
                            <tr>
                                <td><strong>{{ $car->nama_mobil }}</strong><div class="text-muted">{{ $car->plat_nomor }}</div></td>
                                <td>{{ $car->merk }}@if($car->tipe) / {{ $car->tipe }}@endif</td>
                                <td>{{ $car->tahun ?? '-' }}</td>
                                <td>Rp {{ number_format($car->harga_sewa, 0, ',', '.') }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a class="btn btn-sm btn-outline" href="{{ route('cars.show', $car) }}">Spesifikasi</a>
                                        <a class="btn btn-sm btn-primary" href="{{ route('bookings.create', ['car_id' => $car->id]) }}">Sewa</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card">
        <h2>Peminjaman Saya</h2>

        @if($bookingSaya->isEmpty())
            <div class="empty">
                <div class="big">📅</div>
                Belum ada peminjaman. Mulai sewa mobil pertama Anda.
            </div>
        @else

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mobil</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Hari</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookingSaya as $booking)
                            <tr>
                                <td>{{ $booking->car->nama_mobil ?? '-' }}</td>
                                <td>{{ $booking->tanggal_mulai->format('d M Y') }}</td>
                                <td>{{ $booking->tanggal_selesai->format('d M Y') }}</td>
                                <td>{{ $booking->jumlah_hari }} hari</td>
                                <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-{{ $booking->status }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ ($booking->status_pembayaran ?? 'belum_bayar') === 'lunas' ? 'selesai' : (($booking->status_pembayaran ?? '') === 'menunggu_verifikasi' ? 'menunggu' : 'dibatalkan') }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status_pembayaran ?? 'belum bayar')) }}
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
@endif

@endsection