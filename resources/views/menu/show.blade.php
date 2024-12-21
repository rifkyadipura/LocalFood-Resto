@extends('layouts.appDashboard')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg rounded-lg border-0">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center rounded-top">
            <h4 class="mb-0">Detail Menu</h4>
            <a href="{{ route('menu.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Menu
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <!-- Foto Menu -->
                    @if ($menu->foto)
                        <img src="{{ asset($menu->foto) }}" alt="Foto {{ $menu->name }}" class="img-thumbnail mb-3" width="100%">
                    @else
                        <p class="text-muted">Foto tidak tersedia</p>
                    @endif
                </div>
                <div class="col-md-8">
                    <!-- Detail Menu -->
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Nama</th>
                                <td>{{ $menu->name }}</td>
                            </tr>
                            <tr>
                                <th>Harga</th>
                                <td>Rp {{ number_format($menu->harga, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Stok</th>
                                <td>{{ $menu->stok }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>{{ $menu->kategori->name ?? 'Tidak ada kategori' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($menu->status == 1)
                                        <span class="badge bg-success">Tersedia</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Tersedia</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $menu->deskripsi ?? 'Tidak ada deskripsi' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
