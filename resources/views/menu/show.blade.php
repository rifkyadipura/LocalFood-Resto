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
                        <a href="{{ asset($menu->foto) }}" target="_blank">
                            <img src="{{ asset($menu->foto) }}" alt="Foto {{ $menu->nama_menu }}" class="img-thumbnail mb-3" width="100%">
                        </a>
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
                                <td>{{ $menu->nama_menu }}</td>
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
                                <td>{{ $menu->kategori->nama_kategory ?? 'Tidak ada kategori' }}</td>
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
                            <tr>
                                <th>Dibuat Oleh</th>
                                <td>{{ $menu->pembuat ? $menu->pembuat->nama_lengkap : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Diperbarui Oleh</th>
                                <td>{{ $menu->pengupdate ? $menu->pengupdate->nama_lengkap : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ $menu->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Diperbarui Pada</th>
                                <td>{{ $menu->updated_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
