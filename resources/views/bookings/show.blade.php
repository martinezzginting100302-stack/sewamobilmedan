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
                @if($isAdmin)
                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-primary">Edit</a>
                <form action="{{ route('bookings.destroy', $booking) }}" method="POST" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus booking ini?')">
                        Hapus
                    </button>
                </form>
                @endif
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
            <tr><th>Metode Pembayaran</th><td>{{ $booking->metode_pembayaran ? ucfirst(str_replace('_', ' ', $booking->metode_pembayaran)) : '-' }}</td></tr>
            <tr><th>Status Pembayaran</th><td>
                <span class="badge badge-{{ ($booking->status_pembayaran ?? 'belum_bayar') === 'lunas' ? 'selesai' : (($booking->status_pembayaran ?? '') === 'menunggu_verifikasi' ? 'menunggu' : 'dibatalkan') }}">{{ ucfirst(str_replace('_', ' ', $booking->status_pembayaran ?? 'belum bayar')) }}</span>
            </td></tr>
            @if($booking->bukti_pembayaran)
            <tr><th>Bukti Pembayaran</th><td><a href="{{ asset('storage/' . $booking->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline">Lihat Bukti</a></td></tr>
            @endif
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

    @if(!$isAdmin)
        <div class="card">
            <h2>Pembayaran Saya</h2>
            @if(($booking->status_pembayaran ?? '') === 'lunas')
                <p class="text-muted">Pembayaran Anda sudah <strong>diverifikasi lunas</strong> oleh admin. Terima kasih.</p>
            @else
                <form action="{{ route('bookings.payment', $booking) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="metode_pembayaran">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                            @foreach(['transfer_bank' => 'Transfer Bank', 'e_wallet' => 'E-Wallet', 'qris' => 'QRIS', 'cash' => 'Tunai (Cash)'] as $val => $label)
                                <option value="{{ $val }}" {{ ($booking->metode_pembayaran ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bukti_pembayaran">Bukti Pembayaran (JPG/PNG/WEBP, maks 2MB)</label>
                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control" accept="image/*">
                        <div class="hint">Untuk tunai, bukti tidak wajib — admin akan verifikasi saat serah terima.</div>
                    </div>
                    @if($booking->bukti_pembayaran)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $booking->bukti_pembayaran) }}" alt="Bukti" style="max-width:280px;border-radius:8px;border:1px solid #e2e8f0;">
                        </div>
                    @endif
                    <button type="submit" class="btn btn-primary">Kirim Pembayaran</button>
                </form>
            @endif
        </div>
    @endif

    @if($isAdmin)
        <div class="card">
            <h2>Verifikasi Pembayaran</h2>
            @if($booking->bukti_pembayaran)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $booking->bukti_pembayaran) }}" alt="Bukti" style="max-width:280px;border-radius:8px;border:1px solid #e2e8f0;">
                    <div class="mt-2"><a href="{{ asset('storage/' . $booking->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline">Lihat Ukuran Penuh</a></div>
                </div>
            @else
                <p class="text-muted">Belum ada bukti pembayaran yang diunggah.</p>
            @endif
            <div class="d-flex mt-2">
                @foreach(['lunas' => 'btn-success', 'ditolak' => 'btn-danger', 'menunggu_verifikasi' => 'btn-warning', 'belum_bayar' => 'btn-secondary'] as $target => $class)
                    <form action="{{ route('bookings.payment.verify', $booking) }}" method="POST" style="margin:0;">
                        @csrf
                        <input type="hidden" name="status_pembayaran" value="{{ $target }}">
                        <button type="submit" class="btn btn-sm {{ $class }}">{{ ucfirst(str_replace('_', ' ', $target)) }}</button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    @if($isAdmin && isset($transitions[$booking->status]))
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
        @if($isAdmin)
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">← Kembali ke Data Booking</a>
        @else
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Kembali ke Dashboard</a>
        <a href="{{ route('cars.index') }}" class="btn btn-outline">Lihat Daftar Mobil</a>
        @endif
    </div>

@endsection