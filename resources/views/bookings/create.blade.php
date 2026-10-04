@extends('layouts.app')

@section('title', 'Buat Booking - Sewa Mobil Medan')
@section('page_title', 'Buat Booking')

@section('content')

    <div class="card" style="max-width:680px;">
        @if($cars->isEmpty())
            <div class="empty">
                <div class="big">🚗</div>
                Tidak ada mobil yang tersedia untuk disewa saat ini.
            </div>

            <div class="d-flex">
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('cars.create') }}" class="btn btn-primary">
                    + Tambah Mobil
                </a>
                @else
                <a href="{{ route('cars.index') }}" class="btn btn-primary">
                    Lihat Daftar Mobil
                </a>
                @endif
                <a href="{{ auth()->user()->role === 'admin' ? route('bookings.index') : route('dashboard') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>
        @else

        <form action="{{ route('bookings.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="car_id">Pilih Mobil</label>
                <select name="car_id" id="car_id" class="form-control" required>
                    <option value="">-- Pilih Mobil --</option>
                    @foreach($cars as $car)
                        @php $selected = old('car_id', $selectedCarId ?? ''); @endphp
                        <option value="{{ $car->id }}"
                            {{ (string) $selected === (string) $car->id ? 'selected' : '' }}>
                            {{ $car->nama_mobil }} — {{ $car->plat_nomor }} —
                            Rp {{ number_format($car->harga_sewa, 0, ',', '.') }}/hari
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nama_penyewa">Nama Penyewa</label>
                <input type="text" name="nama_penyewa" id="nama_penyewa"
                       class="form-control" value="{{ old('nama_penyewa') }}"
                       placeholder="Nama lengkap penyewa" required>
            </div>

            <div class="form-group">
                <label for="no_telepon">No. Telepon</label>
                <input type="text" name="no_telepon" id="no_telepon"
                       class="form-control" value="{{ old('no_telepon') }}"
                       placeholder="Contoh: 081234567890" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3"
                          class="form-control"
                          placeholder="Alamat penyewa">{{ old('alamat') }}</textarea>
            </div>

            <div class="form-inline" style="margin-bottom:18px;">
                <div class="form-group">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                           class="form-control" value="{{ old('tanggal_mulai') }}"
                           min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                           class="form-control" value="{{ old('tanggal_selesai') }}"
                           min="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="metode_pembayaran">Metode Pembayaran</label>
                <select name="metode_pembayaran" id="metode_pembayaran" class="form-control">
                    <option value="">-- Pilih Metode (bisa diisi nanti) --</option>
                    @foreach(['transfer_bank' => 'Transfer Bank', 'e_wallet' => 'E-Wallet', 'qris' => 'QRIS', 'cash' => 'Tunai (Cash)'] as $val => $label)
                        <option value="{{ $val }}" {{ old('metode_pembayaran') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="hint">Bukti pembayaran dapat diunggah setelah pengajuan dibuat, di halaman Detail.</div>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary">Buat Booking</button>
                <a href="{{ auth()->user()->role === 'admin' ? route('bookings.index') : route('cars.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>

        @endif
    </div>

@endsection