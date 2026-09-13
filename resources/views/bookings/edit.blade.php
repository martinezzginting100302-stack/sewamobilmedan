@extends('layouts.app')

@section('title', 'Edit Booking - Sewa Mobil Medan')
@section('page_title', 'Edit Booking')

@section('content')

    <div class="card" style="max-width:680px;">
        <form action="{{ route('bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="car_id">Pilih Mobil</label>
                <select name="car_id" id="car_id" class="form-control" required>
                    @foreach($cars as $car)
                        <option value="{{ $car->id }}"
                            {{ old('car_id', $booking->car_id) == $car->id ? 'selected' : '' }}>
                            {{ $car->nama_mobil }} — {{ $car->plat_nomor }} —
                            Rp {{ number_format($car->harga_sewa, 0, ',', '.') }}/hari
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nama_penyewa">Nama Penyewa</label>
                <input type="text" name="nama_penyewa" id="nama_penyewa"
                       class="form-control"
                       value="{{ old('nama_penyewa', $booking->nama_penyewa) }}" required>
            </div>

            <div class="form-group">
                <label for="no_telepon">No. Telepon</label>
                <input type="text" name="no_telepon" id="no_telepon"
                       class="form-control"
                       value="{{ old('no_telepon', $booking->no_telepon) }}" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3"
                          class="form-control">{{ old('alamat', $booking->alamat) }}</textarea>
            </div>

            <div class="form-inline" style="margin-bottom:18px;">
                <div class="form-group">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                           class="form-control"
                           value="{{ old('tanggal_mulai', $booking->tanggal_mulai->format('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                           class="form-control"
                           value="{{ old('tanggal_selesai', $booking->tanggal_selesai->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

@endsection