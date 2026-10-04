@extends('layouts.app')

@section('title', 'Data Customer - Sewa Mobil Medan')
@section('page_title', 'Data Customer (Admin)')

@section('content')

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Total Peminjaman</th>
                        <th>Terdaftar</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td><strong>{{ $customer->name }}</strong></td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->bookings_count }} peminjaman</td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline"
                                   href="{{ route('customers.show', $customer) }}">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty">
                                    <div class="big">👥</div>
                                    Belum ada customer terdaftar.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
