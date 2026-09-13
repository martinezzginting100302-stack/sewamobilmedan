@extends('layouts.app')

@section('title', 'Detail Booking - Sewa Mobil Medan')
@section('page_title', 'Detail Booking')

@section('content')

    <div class="card">
        <div class="d-flex mb-2" style="justify-content:space-between;align-items:flex-start;">
            <div>
                <h2 style="margin:0 0 4px;">{{ $booking->nama_penyewa }}</h2>
                <span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
            </div>

            <div class="d-flex">
                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-primary">Edit</a>
                <form action="{{ route('bookings.destroy', $booking) }}" method="POST" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus booking ini?')">
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <table class="detail-table">
            <tr><th>Nama Penyewa</th><td>{{ $booking->nama_penyewa }}</td></tr>
            <tr><th>No. Telepon</th><td>{{ $booking->no_telepon }}</td></tr>
            <tr><th>Alamat</th><td>{{ $booking->alamat ?? '-' }}</td></tr>
            <tr><th>Mobil</th><td>{{ $booking->car->nama_mobil }}</td></tr>
            <tr><th>Merk</th><td>{{ $booking->car->merk }}</td></tr>
            <tr><th>Plat Nomor</th><td>{{ $booking->car->plat_nomor }}</td></tr>
            <tr>
                <th>Tanggal Mulai</th>
                <td>{{ $booking->tanggal_mulai->format('d M Y') }}</td>
            </tr>
            <tr>
                <th>Tanggal Selesai</th>
                <td>{{ $booking->tanggal_selesai->format('d M Y') }}</td>
            </tr>
            <tr><th>Jumlah Hari</th><td>{{ $booking->jumlah_hari }} hari</td></tr>
            <tr>
                <th>Harga / Hari</th>
                <td>Rp {{ number_format($booking->harga_per_hari, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total Harga</th>
                <td>
                    <strong style="font-size:17px;">
                        Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                    </strong>
                </td>
            </tr>
            <tr><th>Status</th><td>
                <span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
            </td></tr>
            <tr>
                <th>Dibuat</th>
                <td>{{ $booking->created_at->format('d M Y - H:i') }}</td>
            </tr>
        </table>
    </div>

    @php
        $transitions = [
            'menunggu' => [
                ['dikonfirmasi', 'Konfirmasi Booking', 'btn-success'],
                ['dibatalkan', 'Batalkan', 'btn-warning'],
            ],
            'dikonfirmasi' => [
                ['selesai', 'Tandai Selesai', 'btn-success'],
                ['dibatalkan', 'Batalkan', 'btn-warning'],
            ],
            'selesai' => [
                ['dikonfirmasi', 'Kembalikan ke Dikonfirmasi', 'btn-primary'],
            ],
            'dibatalkan' => [
                ['menunggu', 'Aktifkan Kembali', 'btn-primary'],
            ],
        ];
    @endphp

    @if(isset($transitions[$booking->status]))
        <div class="card">
            <h2>Ubah Status</h2>

            <div class="d-flex">
                @foreach($transitions[$booking->status] as [$target, $label, $class])
                    <form action="{{ route('bookings.status', $booking) }}"
                          method="POST" style="margin:0;">
                        @csrf
                        <input type="hidden" name="status" value="{{ $target }}">
                        <button type="submit" class="btn {{ $class }}">
                            {{ $label }}
                        </button>
                    </form>
                @endforeach
            </div>

            <p class="text-muted mt-2">
                Status mobil akan otomatis menyesuaikan: aktif = "<strong>Disewa</strong>",
                selesai/dibatalkan = "<strong>Tersedia</strong>".
            </p>
        </div>
    @endif

    <div class="mt-2">
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">← Kembali ke Data Booking</a>
    </div>

@endsection