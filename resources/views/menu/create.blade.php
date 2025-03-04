@extends('layouts.appDashboard')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg rounded-lg border-0">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center rounded-top">
            <h4 class="mb-0">Tambah Menu Baru</h4>
            <a href="{{ route('menu.index') }}" class="btn btn-light">
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

            <form action="{{ route('menu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="menu_name" class="form-label">Nama Menu</label>
                    <input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="Masukkan nama menu" required>
                </div>
                <div class="mb-3">
                    <label for="formatted_price" class="form-label">Harga</label>
                    <input type="text" name="formatted_price" id="formatted_price" class="form-control" placeholder="Masukkan harga menu" required>
                    <input type="hidden" name="price" id="price">
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">Stok</label>
                    <input type="number" name="stock" id="stock" class="form-control" placeholder="Masukkan stok menu" required>
                </div>
                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="1">Tersedia</option>
                        <option value="0">Tidak Tersedia</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Foto</label>
                    <input type="file" name="image" id="image" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const formattedPrice = document.getElementById('formatted_price');
    const price = document.getElementById('price');

    formattedPrice.addEventListener('input', function (e) {
        let value = this.value.replace(/\D/g, '');
        let formattedValue = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);

        this.value = formattedValue;
        price.value = value;
    });

    // Validasi Status Berdasarkan Stok
    const stockInput = document.getElementById('stock');
    const statusSelect = document.getElementById('status');

    stockInput.addEventListener('input', function () {
        const stockValue = parseInt(this.value);
        if (stockValue > 0) {
            statusSelect.value = "1";
            statusSelect.querySelector('option[value="0"]').disabled = true;
            statusSelect.querySelector('option[value="1"]').disabled = false;
        } else {
            statusSelect.value = "0";
            statusSelect.querySelector('option[value="1"]').disabled = true;
            statusSelect.querySelector('option[value="0"]').disabled = false;
        }
    });

    document.querySelector('form').addEventListener('submit', function (e) {
        const stockValue = parseInt(stockInput.value);
        const statusValue = statusSelect.value;

        if (stockValue == 0 && statusValue == "1") {
            e.preventDefault();
            alert('Status "Tersedia" tidak boleh dipilih jika stok kosong.');
        }
    });
</script>

@endsection
