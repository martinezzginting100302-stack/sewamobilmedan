@extends('layouts.app')

@section('title', 'Laporan - Sewa Mobil Medan')
@section('page_title', 'Laporan Booking')

@section('content')

    <div class="card mb-2">
        <form method="GET" action="{{ route('reports.index') }}" class="form-inline">
            <div class="form-group">
                <label for="dari">Dari Tanggal</label>
                <input type="date" name="dari" id="dari" class="form-control" value="{{ $dari }}">
            </div>

            <div class="form-group">
                <label for="sampai">Sampai Tanggal</label>
                <input type="date" name="sampai" id="sampai" class="form-control" value="{{ $sampai }}">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach(['menunggu', 'dikonfirmasi', 'selesai', 'dibatalkan'] as $st)
                        <option value="{{ $st }}" {{ $status == $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">Filter</button>

            @if($dari || $sampai || $status)
                <a href="{{ route('reports.index') }}" class="btn btn-outline">Reset</a>
            @endif

            <a href="{{ route('reports.export', ['dari' => $dari, 'sampai' => $sampai, 'status' => $status]) }}"
               class="btn btn-success">⬇ Export CSV</a>
        </form>
    </div>

    <div class="stats">

        <div class="stat stat-blue">
            <div class="label">Total Booking</div>
            <div class="value">{{ $totalBooking }}</div>
            <div class="sub">Transaksi pada rentang terpilih</div>
        </div>

        <div class="stat stat-indigo">
            <div class="label">Total Hari Sewa</div>
            <div class="value">{{ $totalHari }}</div>
            <div class="sub">Akumulasi hari seluruh booking</div>
        </div>

        <div class="stat stat-amber">
            <div class="label">Nilai Booking</div>
            <div class="value">Rp {{ number_format($totalNilaiBooking, 0, ',', '.') }}</div>
            <div class="sub">Seluruh booking non-batal</div>
        </div>

        <div class="stat stat-green">
            <div class="label">Pendapatan</div>
            <div class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="sub">Booking selesai & terkonfirmasi</div>
        </div>

    </div>

    <div class="card">
        <h2>Rincian Booking</h2>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Penyewa</th>
                        <th>Mobil</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Hari</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->nama_penyewa }}</strong>
                                <div class="text-muted">{{ $booking->no_telepon }}</div>
                            </td>
                            <td>{{ $booking->car->nama_mobil }} ({{ $booking->car->plat_nomor }})</td>
                            <td>{{ $booking->tanggal_mulai->format('d M Y') }}</td>
                            <td>{{ $booking->tanggal_selesai->format('d M Y') }}</td>
                            <td>{{ $booking->jumlah_hari }}</td>
                            <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $booking->status }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty">
                                    <div class="big">📊</div>
                                    Tidak ada data pada rentang yang dipilih.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection