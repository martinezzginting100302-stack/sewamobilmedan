@extends('layouts.app')

@section('title', 'Data Booking - Sewa Mobil Medan')
@section('page_title', 'Data Booking')

@section('content')

    <div class="d-flex mb-2">
        <form method="GET" action="{{ route('bookings.index') }}" class="form-inline">
            <div class="form-group">
                <label for="status">Filter Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach(['menunggu', 'dikonfirmasi', 'selesai', 'dibatalkan'] as $st)
                        <option value="{{ $st }}"
                            {{ $filterStatus == $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">Terapkan</button>

            @if($filterStatus)
                <a href="{{ route('bookings.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>

        <div style="flex:1;"></div>

        <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ Buat Booking</a>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Penyewa</th>
                        <th>Mobil</th>
                        <th>Plat Nomor</th>
                        <th>Tanggal Sewa</th>
                        <th>Hari</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->nama_penyewa }}</strong>
                                <div class="text-muted">{{ $booking->no_telepon }}</div>
                            </td>
                            <td>{{ $booking->car->nama_mobil }}</td>
                            <td>{{ $booking->car->plat_nomor }}</td>
                            <td>
                                {{ $booking->tanggal_mulai->format('d M Y') }}
                                <br><span class="text-muted">s/d {{ $booking->tanggal_selesai->format('d M Y') }}</span>
                            </td>
                            <td>{{ $booking->jumlah_hari }} hari</td>
                            <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $booking->status }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a class="btn btn-sm btn-outline"
                                       href="{{ route('bookings.show', $booking) }}">Detail</a>
                                    <a class="btn btn-sm btn-primary"
                                       href="{{ route('bookings.edit', $booking) }}">Edit</a>
                                    <form action="{{ route('bookings.destroy', $booking) }}"
                                          method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus booking {{ $booking->nama_penyewa }}?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty">
                                    <div class="big">📅</div>
                                    Belum ada data booking.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection