@extends('layouts.app')

@section('title', 'Edit Mobil - Sewa Mobil Medan')
@section('page_title', 'Edit Mobil')

@section('content')

    <div class="card" style="max-width:640px;">
        <form action="{{ route('cars.update', $car) }}" method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_mobil">Nama Mobil</label>
                <input type="text" name="nama_mobil" id="nama_mobil"
                       class="form-control"
                       value="{{ old('nama_mobil', $car->nama_mobil) }}" required>
            </div>

            <div class="form-group">
                <label for="merk">Merk</label>
                <input type="text" name="merk" id="merk"
                       class="form-control"
                       value="{{ old('merk', $car->merk) }}" required>
            </div>

            <div class="form-group">
                <label for="tipe">Tipe</label>
                <input type="text" name="tipe" id="tipe"
                       class="form-control"
                       value="{{ old('tipe', $car->tipe) }}">
            </div>

            <div class="form-group">
                <label for="tahun">Tahun</label>
                <input type="number" name="tahun" id="tahun"
                       class="form-control"
                       value="{{ old('tahun', $car->tahun) }}"
                       min="1990" max="{{ date('Y') + 1 }}" required>
            </div>

            <div class="form-group">
                <label for="plat_nomor">Plat Nomor</label>
                <input type="text" name="plat_nomor" id="plat_nomor"
                       class="form-control"
                       value="{{ old('plat_nomor', $car->plat_nomor) }}" required>
            </div>

            <div class="form-group">
                <label for="harga_sewa">Harga Sewa / Hari (Rp)</label>
                <input type="number" name="harga_sewa" id="harga_sewa"
                       class="form-control"
                       value="{{ old('harga_sewa', $car->harga_sewa) }}"
                       min="0" step="5000" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    @foreach(['tersedia', 'disewa', 'maintenance'] as $st)
                        <option value="{{ $st }}"
                            {{ old('status', $car->status) == $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
                <div class="hint">Status mobil otomatis menjadi "Disewa" ketika ada booking aktif.</div>
            </div>

            <div class="form-group">
                <label for="foto">Foto Mobil</label>

                @if($car->foto)
                    <div style="margin-bottom:10px;">
                        <img src="{{ asset('storage/' . $car->foto) }}"
                             alt="{{ $car->nama_mobil }}" class="car-photo">
                    </div>
                @endif

                <input type="file" name="foto" id="foto"
                       class="form-control" accept="image/*">
                <div class="hint">Kosongkan jika tidak ingin mengganti foto. Maksimal 2 MB.</div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="5"
                          class="form-control">{{ old('deskripsi', $car->deskripsi) }}</textarea>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('cars.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

@endsection