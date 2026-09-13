@extends('layouts.app')

@section('title', 'Tambah Mobil - Sewa Mobil Medan')
@section('page_title', 'Tambah Mobil')

@section('content')

    <div class="card" style="max-width:640px;">
        <form action="{{ route('cars.store') }}" method="POST"
              enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="nama_mobil">Nama Mobil</label>
                <input type="text" name="nama_mobil" id="nama_mobil"
                       class="form-control" value="{{ old('nama_mobil') }}"
                       placeholder="Contoh: Toyota Avanza" required>
            </div>

            <div class="form-group">
                <label for="merk">Merk</label>
                <input type="text" name="merk" id="merk"
                       class="form-control" value="{{ old('merk') }}"
                       placeholder="Contoh: Toyota" required>
            </div>

            <div class="form-group">
                <label for="tipe">Tipe</label>
                <input type="text" name="tipe" id="tipe"
                       class="form-control" value="{{ old('tipe') }}"
                       placeholder="Contoh: MPV / Sedan / Hatchback">
            </div>

            <div class="form-group">
                <label for="tahun">Tahun</label>
                <input type="number" name="tahun" id="tahun"
                       class="form-control" value="{{ old('tahun') }}"
                       min="1990" max="{{ date('Y') + 1 }}"
                       placeholder="Contoh: 2022" required>
            </div>

            <div class="form-group">
                <label for="plat_nomor">Plat Nomor</label>
                <input type="text" name="plat_nomor" id="plat_nomor"
                       class="form-control" value="{{ old('plat_nomor') }}"
                       placeholder="Contoh: BK 1234 ABC" required>
            </div>

            <div class="form-group">
                <label for="harga_sewa">Harga Sewa / Hari (Rp)</label>
                <input type="number" name="harga_sewa" id="harga_sewa"
                       class="form-control" value="{{ old('harga_sewa') }}"
                       min="0" step="5000"
                       placeholder="Contoh: 350000" required>
                <div class="hint">Dalam Rupiah per hari.</div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="tersedia"
                        {{ old('status', 'tersedia') == 'tersedia' ? 'selected' : '' }}>
                        Tersedia
                    </option>
                    <option value="disewa"
                        {{ old('status') == 'disewa' ? 'selected' : '' }}>
                        Disewa
                    </option>
                    <option value="maintenance"
                        {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                        Maintenance
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="foto">Foto Mobil</label>
                <input type="file" name="foto" id="foto"
                       class="form-control" accept="image/*">
                <div class="hint">Format JPG, PNG, WEBP. Maksimal 2 MB.</div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="5"
                          class="form-control"
                          placeholder="Keterangan tambahan, fasilitas, fitur, dll.">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary">Simpan Mobil</button>
                <a href="{{ route('cars.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

@endsection