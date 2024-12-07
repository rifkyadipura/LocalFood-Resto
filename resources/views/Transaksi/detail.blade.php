@extends('layouts.appDashboard')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg rounded-lg border-0">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center rounded-top">
            <h4 class="mb-0">Detail Transaksi: {{ $kode_transaksi }}</h4>
            <a href="{{ route('transaksi.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Transaksi
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Menu</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksis as $index => $transaksi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaksi->menu->name }}</td>
                            <td>{{ $transaksi->jumlah }}</td>
                            <td>Rp{{ number_format($transaksi->total_harga, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <button class="btn btn-primary" onclick="window.print()">Cetak</button>
        </div>
    </div>
</div>
@endsection
