@extends('layouts.appDashboard')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg rounded-lg border-0">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center rounded-top">
            <h4 class="mb-0">Tambah Menu Baru</h4>
            <a href="{{ route('index.menu') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Menu
            </a>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('store.menu') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Menu</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama menu" required>
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" name="harga" id="harga" class="form-control" placeholder="Masukkan harga menu" step="0.01" min="0" required>
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" placeholder="Masukkan stok menu" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="1">Tersedia</option>
                        <option value="0">Tidak Tersedia</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto</label>
                    <input type="file" name="foto" id="foto" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
