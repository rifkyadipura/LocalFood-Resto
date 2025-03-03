@extends('layouts.appDashboard')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg rounded-lg border-0">
        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center rounded-top">
            <h4 class="mb-0">Edit Menu</h4>
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

            <form action="{{ route('menu.update', $menu->menu_item_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Nama Menu (hanya untuk admin dan Head Staff) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Head Staff'))
                <div class="mb-3">
                    <label for="menu_name" class="form-label">Nama Menu</label>
                    <input type="text" name="menu_name" id="menu_name" class="form-control" value="{{ $menu->menu_name }}" required>
                </div>

                {{-- Harga (hanya untuk admin dan Head Staff) --}}
                <div class="mb-3">
                    <label for="price_display" class="form-label">Harga</label>
                    <input type="text" id="price_display" class="form-control" value="{{ $menu->price ? 'Rp ' . number_format($menu->price, 0, ',', '.') : '' }}" required>
                    <input type="hidden" name="price" id="price" value="{{ $menu->price }}">
                </div>
                @endif

                {{-- Stok (dapat diakses oleh semua) --}}
                <div class="mb-3">
                    <label for="stock" class="form-label">Stok</label>
                    <input type="number" name="stock" id="stock" class="form-control" value="{{ $menu->stock }}" required>
                </div>

                {{-- Kategori (hanya untuk admin dan Head Staff) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Head Staff'))
                <div class="mb-3">
                    <label for="category" class="form-label">Kategori</label>
                    <select name="category_id" id="category" class="form-select" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->category_id }}" {{ $menu->category_id == $category->category_id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Status (hanya untuk admin dan Head Staff) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Head Staff'))
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="1" {{ $menu->status ? 'selected' : '' }}>Tersedia</option>
                        <option value="0" {{ !$menu->status ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>
                @endif

                {{-- Foto (hanya untuk admin dan Head Staff) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Head Staff'))
                <div class="mb-3">
                    <label for="image" class="form-label">Foto</label>
                    @if ($menu->image)
                        <img src="{{ asset($menu->image) }}" alt="Foto {{ $menu->menu_name }}" class="img-thumbnail mb-2" width="150">
                    @endif
                    <input type="file" name="image" id="image" class="form-control">
                </div>
                @endif

                {{-- Deskripsi (hanya untuk admin dan Head Staff) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Head Staff'))
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ $menu->description }}</textarea>
                </div>
                @endif

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const priceDisplay = document.getElementById('price_display');
        const priceHidden = document.getElementById('price');

        if (priceHidden.value) {
            priceDisplay.value = formatRupiah(priceHidden.value);
        }

        priceDisplay.addEventListener('input', function (e) {
            const value = e.target.value.replace(/[^0-9]/g, '');
            priceDisplay.value = formatRupiah(value);
            priceHidden.value = value;
        });

        function formatRupiah(number) {
            if (!number) return 'Rp ';
            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
            }).format(number);
        }

        const stockInput = document.getElementById('stock');
        const statusSelect = document.getElementById('status');

        function updateStatusOptions() {
            const stockValue = parseInt(stockInput.value) || 0;

            if (isNaN(stockValue) || stockValue === 0) {
                statusSelect.value = "0";
                statusSelect.querySelector('option[value="1"]').disabled = true;
                statusSelect.querySelector('option[value="0"]').disabled = false;
            } else {
                statusSelect.value = "1";
                statusSelect.querySelector('option[value="0"]').disabled = true;
                statusSelect.querySelector('option[value="1"]').disabled = false;
            }

            if (stockInput.value === '') {
                statusSelect.value = '';
                statusSelect.querySelector('option[value="0"]').disabled = true;
                statusSelect.querySelector('option[value="1"]').disabled = true;
            }
        }

        stockInput.addEventListener('input', updateStatusOptions);
        updateStatusOptions();

        document.querySelector('form').addEventListener('submit', function (e) {
            const stockValue = parseInt(stockInput.value) || 0;
            const statusValue = statusSelect.value;

            if (stockInput.value === '') {
                e.preventDefault();
                alert('Harap isi stok terlebih dahulu sebelum menyimpan.');
                return;
            }

            if (stockValue === 0 && statusValue == "1") {
                e.preventDefault();
                alert('Status "Tersedia" tidak boleh dipilih jika stok kosong.');
            }
        });
    });
</script>
@endsection
