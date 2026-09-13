@extends('layouts.app')

@section('title', 'Data Mobil - Sewa Mobil Medan')
@section('page_title', 'Data Mobil')

@section('content')

    <div class="d-flex mb-2">
        <div style="flex:1;"></div>
        <a class="btn btn-primary" href="{{ route('cars.create') }}">+ Tambah Mobil</a>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Mobil</th>
                        <th>Merk</th>
                        <th>Tahun</th>
                        <th>Plat Nomor</th>
                        <th>Harga / Hari</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cars as $car)
                        <tr>
                            <td>
                                @if($car->foto)
                                    <img src="{{ asset('storage/' . $car->foto) }}"
                                         alt="{{ $car->nama_mobil }}"
                                         class="car-photo">
                                @else
                                    <div class="placeholder-photo">No photo</div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $car->nama_mobil }}</strong>
                                @if($car->tipe)
                                    <div class="text-muted">{{ $car->tipe }}</div>
                                @endif
                            </td>
                            <td>{{ $car->merk }}</td>
                            <td>{{ $car->tahun }}</td>
                            <td>{{ $car->plat_nomor }}</td>
                            <td>Rp {{ number_format($car->harga_sewa, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $car->status }}">
                                    {{ ucfirst($car->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a class="btn btn-sm btn-outline"
                                       href="{{ route('cars.show', $car) }}">Detail</a>
                                    <a class="btn btn-sm btn-primary"
                                       href="{{ route('cars.edit', $car) }}">Edit</a>
                                    <form action="{{ route('cars.destroy', $car) }}"
                                          method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus {{ $car->nama_mobil }}?')">
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
                                    <div class="big">🚙</div>
                                    Belum ada data mobil. Klik "Tambah Mobil" untuk mulai.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection