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

            <form action="{{ route('menu.update', $menu->menu_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Nama Menu (hanya untuk admin dan Kepala Staf) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Kepala Staf'))
                <div class="mb-3">
                    <label for="nama_menu" class="form-label">Nama Menu</label>
                    <input type="text" name="nama_menu" id="nama_menu" class="form-control" value="{{ $menu->nama_menu }}" required>
                </div>

                {{-- Harga (hanya untuk admin dan Kepala Staf) --}}
                <div class="mb-3">
                    <label for="harga_display" class="form-label">Harga</label>
                    <input type="text" id="harga_display" class="form-control" value="{{ $menu->harga ? 'Rp ' . number_format($menu->harga, 0, ',', '.') : '' }}" required>
                    <input type="hidden" name="harga" id="harga" value="{{ $menu->harga }}">
                </div>
                @endif

                {{-- Stok (dapat diakses oleh semua) --}}
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" value="{{ $menu->stok }}" required>
                </div>

                {{-- Kategori (hanya untuk admin dan Kepala Staf) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Kepala Staf'))
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select name="kategory_id" id="kategori" class="form-select" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach ($kategories as $kategori)
                            <option value="{{ $kategori->kategory_id }}" {{ $menu->kategory_id == $kategori->kategory_id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategory }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Status (hanya untuk admin dan Kepala Staf) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Kepala Staf'))
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="1" {{ $menu->status ? 'selected' : '' }}>Tersedia</option>
                        <option value="0" {{ !$menu->status ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>
                @endif

                {{-- Foto (hanya untuk admin dan Kepala Staf) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Kepala Staf'))
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto</label>
                    @if ($menu->foto)
                        <img src="{{ asset($menu->foto) }}" alt="Foto {{ $menu->name }}" class="img-thumbnail mb-2" width="150">
                    @endif
                    <input type="file" name="foto" id="foto" class="form-control">
                </div>
                @endif

                {{-- Deskripsi (hanya untuk admin dan Kepala Staf) --}}
                @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Kepala Staf'))
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3">{{ $menu->deskripsi }}</textarea>
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
        const hargaDisplay = document.getElementById('harga_display');
        const hargaHidden = document.getElementById('harga');

        // Format awal saat halaman dimuat
        if (hargaHidden.value) {
            hargaDisplay.value = formatRupiah(hargaHidden.value);
        }

        // Event listener untuk memformat input teks
        hargaDisplay.addEventListener('input', function (e) {
            const value = e.target.value.replace(/[^0-9]/g, ''); // Hanya angka
            hargaDisplay.value = formatRupiah(value);
            hargaHidden.value = value; // Simpan angka murni ke input hidden
        });

        function formatRupiah(number) {
            if (!number) return 'Rp ';
            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
            }).format(number);
        }

            // Validasi Status Berdasarkan Stok
        const stokInput = document.getElementById('stok'); // Input stok
        const statusSelect = document.getElementById('status'); // Dropdown status

        // Fungsi untuk memeriksa stok dan memperbarui status
        function updateStatusOptions() {
            const stokValue = parseInt(stokInput.value) || 0; // Ambil nilai stok atau 0 jika kosong

            if (isNaN(stokValue) || stokValue === 0) {
                // Jika stok kosong atau tidak valid, paksa status "Tidak Tersedia"
                statusSelect.value = "0"; // Set "Tidak Tersedia"
                statusSelect.querySelector('option[value="1"]').disabled = true; // Disable "Tersedia"
                statusSelect.querySelector('option[value="0"]').disabled = false; // Aktifkan "Tidak Tersedia"
            } else {
                // Jika stok lebih dari 0, paksa status "Tersedia"
                statusSelect.value = "1"; // Set "Tersedia"
                statusSelect.querySelector('option[value="0"]').disabled = true; // Disable "Tidak Tersedia"
                statusSelect.querySelector('option[value="1"]').disabled = false; // Aktifkan "Tersedia"
            }

            // Jika input kosong (belum diisi), kedua opsi status dinonaktifkan
            if (stokInput.value === '') {
                statusSelect.value = ''; // Reset pilihan status
                statusSelect.querySelector('option[value="0"]').disabled = true;
                statusSelect.querySelector('option[value="1"]').disabled = true;
            }
        }

        // Panggil fungsi saat stok berubah
        stokInput.addEventListener('input', updateStatusOptions);

        // Jalankan saat halaman dimuat (untuk nilai default)
        updateStatusOptions();

        // Validasi sebelum submit form
        document.querySelector('form').addEventListener('submit', function (e) {
            const stokValue = parseInt(stokInput.value) || 0;
            const statusValue = statusSelect.value;

            if (stokInput.value === '') {
                e.preventDefault();
                alert('Harap isi stok terlebih dahulu sebelum menyimpan.');
                return;
            }

            if (stokValue === 0 && statusValue == "1") {
                e.preventDefault();
                alert('Status "Tersedia" tidak boleh dipilih jika stok kosong.');
            }
        });
    });
</script>
@endsection
