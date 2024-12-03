@extends('layouts.appDashboard')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg rounded-lg border-0">
        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center rounded-top">
            <h4 class="mb-0">Edit Menu</h4>
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

            <form action="{{ route('update.menu', $menu->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Menu</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $menu->name }}" required>
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" name="harga" id="harga" class="form-control" value="{{ intval($menu->harga) }}" min="0" required>
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" value="{{ $menu->stok }}" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="1" {{ $menu->status ? 'selected' : '' }}>Tersedia</option>
                        <option value="0" {{ !$menu->status ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto</label>
                    @if ($menu->foto)
                        <img src="{{ asset($menu->foto) }}" alt="Foto {{ $menu->name }}" class="img-thumbnail mb-2" width="150">
                    @endif
                    <input type="file" name="foto" id="foto" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3">{{ $menu->deskripsi }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
