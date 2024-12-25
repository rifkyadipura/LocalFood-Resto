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
                    <label for="nama_menu" class="form-label">Nama Menu</label>
                    <input type="text" name="nama_menu" id="nama_menu" class="form-control" placeholder="Masukkan nama menu" required>
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="text" name="formatted_harga" id="formatted_harga" class="form-control" placeholder="Masukkan harga menu" required>
                    <input type="hidden" name="harga" id="harga">
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" placeholder="Masukkan stok menu" required>
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select name="kategory_id" id="kategori" class="form-select" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach ($kategories as $kategori)
                            <option value="{{ $kategori->kategory_id }}">{{ $kategori->nama_kategory }}</option>
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
<script>
    const formattedHarga = document.getElementById('formatted_harga');
    const harga = document.getElementById('harga');

    formattedHarga.addEventListener('input', function (e) {
        // Ambil nilai dari input dan hapus format Rupiah
        let value = this.value.replace(/\D/g, '');
        // Ubah ke format Rupiah
        let formattedValue = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);
        // Tampilkan nilai yang diformat
        this.value = formattedValue;
        // Simpan nilai asli ke input tersembunyi
        harga.value = value;
    });

    // Validasi Status Berdasarkan Stok
    const stokInput = document.getElementById('stok'); // Input stok
    const statusSelect = document.getElementById('status'); // Dropdown status

    // Event saat stok diubah
    stokInput.addEventListener('input', function () {
        const stokValue = parseInt(this.value); // Ambil nilai stok sebagai angka

        if (stokValue > 0) {
            // Jika stok lebih dari 0, ubah status ke "Tersedia" dan disable "Tidak Tersedia"
            statusSelect.value = "1"; // Paksa ke "Tersedia"
            statusSelect.querySelector('option[value="0"]').disabled = true; // Disable "Tidak Tersedia"
            statusSelect.querySelector('option[value="1"]').disabled = false; // Aktifkan "Tersedia"
        } else {
            // Jika stok kosong (0), ubah status ke "Tidak Tersedia" dan disable "Tersedia"
            statusSelect.value = "0"; // Paksa ke "Tidak Tersedia"
            statusSelect.querySelector('option[value="1"]').disabled = true; // Disable "Tersedia"
            statusSelect.querySelector('option[value="0"]').disabled = false; // Aktifkan "Tidak Tersedia"
        }
    });

    // Validasi sebelum submit form
    document.querySelector('form').addEventListener('submit', function (e) {
        const stokValue = parseInt(stokInput.value); // Ambil nilai stok
        const statusValue = statusSelect.value; // Ambil nilai status

        // Cek apakah stok 0 tetapi status "Tersedia" dipilih
        if (stokValue == 0 && statusValue == "1") {
            e.preventDefault(); // Batalkan submit form
            alert('Status "Tersedia" tidak boleh dipilih jika stok kosong.');
        }
    });
</script>

@endsection
