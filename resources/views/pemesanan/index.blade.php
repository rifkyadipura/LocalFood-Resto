<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LocalFood Resto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .menu-button {
            font-size: 1rem;
            color: white;
            cursor: pointer;
            margin-left: 15px;
            text-decoration: none;
            border: 1px solid white;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-button:hover {
            color: #ffc107;
            border-color: #ffc107;
        }

        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            max-width: 500px;
            width: 100%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .modal-close {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .remove-item {
            color: red;
            cursor: pointer;
        }

        .remove-item:hover {
            text-decoration: underline;
        }

        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
            z-index: 1100;
        }

        .notification.show {
            display: block;
        }

        /* Atur ukuran kartu agar seragam */
        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .card-img-container {
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        .card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: auto;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            min-height: 2.5rem;
            margin-bottom: 5px;
        }

        .card-text {
            max-height: 3rem;
            overflow-y: auto;
            margin-bottom: 5px;
            padding-right: 5px;
            white-space: normal;
        }

        .card-stock {
            font-size: 0.9rem;
            color: gray;
            margin-bottom: 5px;
            height: 1.5rem;
            line-height: 1.5rem;
        }

        .card-text.fw-bold {
            margin-top: 5px;
            height: 1.5rem;
            line-height: 1.5rem;
            text-align: left;
        }

        .cart-item-name {
            flex-grow: 1; /* Membuat elemen nama mengambil ruang tersisa */
            text-align: left;
            margin-right: 10px;
        }

        .cart-item-price {
            flex-shrink: 0; /* Mencegah elemen harga menyusut */
            text-align: right;
            width: 100px; /* Beri lebar tetap untuk keseragaman */
        }

        .remove-item {
            flex-shrink: 0; /* Tetap di posisi tanpa menyusut */
            text-align: center;
            width: 30px; /* Lebar tetap untuk tombol hapus */
            color: red;
            cursor: pointer;
        }

        .remove-item:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">LocalFood Resto</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Menu</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="#" class="menu-button" id="menuIcon">
                    Orders <span id="orderBadge" class="ms-2 text-success fw-bold"></span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Modal Overlay -->
    <div class="modal-overlay" id="modalOverlay"></div>

    <!-- Modal -->
    <div class="modal" id="cartModal">
        <div class="modal-header">
            <h5>Daftar Pesanan</h5>
            <span class="modal-close" id="modalClose">&times;</span>
        </div>
        <div class="modal-body" id="cartItems">
            <p>Belum ada pesanan.</p>
        </div>
        <div class="modal-footer mt-3">
            <div class="d-flex justify-content-between mb-3">
                <span class="fw-bold">Total Bayar:</span>
                <span id="totalPayment">Rp 0</span>
            </div>
            <button class="btn btn-success w-100">Eksekusi Pesanan</button>
        </div>
    </div>

    <!-- Modal Payment Method -->
    <div class="modal" id="paymentMethodModal">
        <div class="modal-header">
            <h5>Pilih Metode Pembayaran</h5>
            <span class="modal-close-payment" id="modalClosePayment" style="cursor: pointer;">&times;</span>
        </div>
        <div class="modal-body">
            <!-- Tombol untuk memilih metode pembayaran -->
            <button id="payWithCash" class="btn btn-primary w-100 mb-2">Cash</button>
            <button id="payWithQris" class="btn btn-secondary w-100">QRIS</button>
        </div>
    </div>

    <!-- Modal Input Cash -->
    <div class="modal" id="cashPaymentModal" style="display: none;">
        <div class="modal-header">
            <h5>Pembayaran dengan Cash</h5>
            <span class="modal-close-payment" id="modalCloseCash" style="cursor: pointer;">&times;</span>
        </div>
        <div class="modal-body">
            <p>Total Bayar: <span id="totalBayar">Rp 0</span></p>
            <div class="mb-3">
                <label for="uangDibayar" class="form-label">Masukkan Uang Dibayar:</label>
                <input type="text" id="uangDibayar" class="form-control" placeholder="Jumlah uang yang dibayarkan">
            </div>
            <button id="submitCashPayment" class="btn btn-success w-100">Proses Pembayaran</button>
        </div>
    </div>

    <!-- Modal QRIS -->
    <div class="modal" id="qrisPaymentModal" style="display: none;">
        <div class="modal-header">
            <h5>Pembayaran dengan QRIS</h5>
            <span class="modal-close-payment" id="modalCloseQris" style="cursor: pointer;">&times;</span>
        </div>
        <div class="modal-body text-center">
            <img src="{{ asset('QRIS/QRIS.jpg') }}" alt="QRIS" class="img-fluid mb-3">
            <p>Total yang harus dibayar: <span id="totalBayarQris">Rp 0</span></p>
            <button id="confirmQrisPayment" class="btn btn-success w-100">Konfirmasi Pembayaran</button>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification">
        Makanan berhasil diinput!
    </div>

    <!-- Section Cards -->
    <div class="container mt-5">
    <!-- Tampilkan kategori Makanan Berat -->
    <h3 class="text-primary">Makanan Berat</h3>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($menus->where('kategory_id', 1) as $menu)
        <div class="col">
            <div class="card h-100">
                <div class="card-img-container">
                    <img src="{{ $menu->foto }}" class="card-img-top" alt="{{ $menu->name }}">
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $menu->name }}</h5>
                    <p class="card-text fw-bold">Harga: Rp {{ number_format($menu->harga, 2, ',', '.') }}</p>
                    <p class="card-stock">Stok: {{ $menu->stok }}</p>
                    <p class="card-text">{{ $menu->deskripsi }}</p>
                    <input type="number" class="form-control mb-2 menu-quantity"
                        placeholder="Jumlah Pesanan" min="1" max="{{ $menu->stok }}"
                        data-menu="{{ $menu->name }}" data-price="{{ $menu->harga }}" data-id="{{ $menu->id }}">
                    <button class="btn btn-primary w-100 btn-add-to-cart">Pesan</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Tampilkan kategori Makanan Ringan -->
    <h3 class="text-primary mt-5">Makanan Ringan</h3>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($menus->where('kategory_id', 2) as $menu)
        <div class="col">
            <div class="card h-100">
                <div class="card-img-container">
                    <img src="{{ $menu->foto }}" class="card-img-top" alt="{{ $menu->name }}">
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $menu->name }}</h5>
                    <p class="card-text fw-bold">Harga: Rp {{ number_format($menu->harga, 2, ',', '.') }}</p>
                    <p class="card-stock">Stok: {{ $menu->stok }}</p>
                    <p class="card-text">{{ $menu->deskripsi }}</p>
                    <input type="number" class="form-control mb-2 menu-quantity"
                        placeholder="Jumlah Pesanan" min="1" max="{{ $menu->stok }}"
                        data-menu="{{ $menu->name }}" data-price="{{ $menu->harga }}" data-id="{{ $menu->id }}">
                    <button class="btn btn-primary w-100 btn-add-to-cart">Pesan</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Tampilkan kategori Minuman -->
    <h3 class="text-primary mt-5">Minuman</h3>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($menus->where('kategory_id', 3) as $menu)
        <div class="col">
            <div class="card h-100">
                <div class="card-img-container">
                    <img src="{{ $menu->foto }}" class="card-img-top" alt="{{ $menu->name }}">
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $menu->name }}</h5>
                    <p class="card-text fw-bold">Harga: Rp {{ number_format($menu->harga, 2, ',', '.') }}</p>
                    <p class="card-stock">Stok: {{ $menu->stok }}</p>
                    <p class="card-text">{{ $menu->deskripsi }}</p>
                    <input type="number" class="form-control mb-2 menu-quantity"
                        placeholder="Jumlah Pesanan" min="1" max="{{ $menu->stok }}"
                        data-menu="{{ $menu->name }}" data-price="{{ $menu->harga }}" data-id="{{ $menu->id }}">
                    <button class="btn btn-primary w-100 btn-add-to-cart">Pesan</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    </div>

    <script>
        const cart = [];

        // Menambah item ke dalam keranjang
        document.querySelectorAll('.btn-add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                const parent = this.parentElement;
                const quantityInput = parent.querySelector('.menu-quantity');
                const quantity = parseInt(quantityInput.value);
                const menuName = quantityInput.getAttribute('data-menu');
                const price = parseFloat(quantityInput.getAttribute('data-price'));
                const menuId = parent.querySelector('.menu-quantity').getAttribute('data-id');
                const stockElement = parent.querySelector('.card-stock');
                const currentStock = parseInt(stockElement.textContent.split(': ')[1]);

                if (!quantity || quantity <= 0) {
                    alert('Masukkan jumlah pesanan yang valid!');
                    return;
                }

                if (quantity > currentStock) {
                    alert('Jumlah pesanan melebihi stok yang tersedia!');
                    return;
                }

                const total = quantity * price;

                cart.push({
                    menu_id: quantityInput.getAttribute('data-id'), // Menggunakan id dari data-menu-id
                    name: menuName,
                    quantity,
                    price,
                    total
                });

                // Mengurangi stok
                const newStock = currentStock - quantity;
                stockElement.textContent = `Stok: ${newStock}`;
                quantityInput.value = '';
                updateCartModal();

                document.getElementById('orderBadge').textContent = cart.length;

                showNotification();
            });
        });

        // Memperbarui modal keranjang
        function updateCartModal() {
            const cartItems = document.getElementById('cartItems');
            const totalPayment = document.getElementById('totalPayment');

            cartItems.innerHTML = '';

            if (cart.length === 0) {
                cartItems.innerHTML = '<p>Belum ada pesanan.</p>';
                totalPayment.textContent = 'Rp 0';
                return;
            }

            let totalAmount = 0;

            cart.forEach((item, index) => {
                const itemRow = document.createElement('div');
                itemRow.className = 'd-flex justify-content-between align-items-center border-bottom pb-2 mb-2';
                itemRow.innerHTML = `
                    <span class="cart-item-name">${item.quantity}x ${item.name}</span>
                    <span class="cart-item-price">Rp ${item.total.toLocaleString('id-ID')}</span>
                    <span class="remove-item" data-index="${index}">&times;</span>
                `;
                cartItems.appendChild(itemRow);
                totalAmount += item.total;
            });

            totalPayment.textContent = `Rp ${totalAmount.toLocaleString('id-ID')}`;

            // Tambahkan event listener untuk menghapus item
            cartItems.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function () {
                    const itemIndex = parseInt(this.getAttribute('data-index'));

                    // Kembalikan stok ke jumlah awal
                    const removedItem = cart[itemIndex];
                    const stockElement = document.querySelector(`.menu-quantity[data-id="${removedItem.menu_id}"]`).parentElement.querySelector('.card-stock');
                    const currentStock = parseInt(stockElement.textContent.split(': ')[1]);
                    stockElement.textContent = `Stok: ${currentStock + removedItem.quantity}`;

                    // Hapus item dari keranjang
                    cart.splice(itemIndex, 1);

                    // Perbarui keranjang dan jumlah item di badge
                    updateCartModal();
                    document.getElementById('orderBadge').textContent = cart.length;
                });
            });
        }

        function showNotification() {
            const notification = document.getElementById('notification');
            notification.textContent = 'Pesanan berhasil ditambahkan ke keranjang!';
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 2000); // Notifikasi akan hilang setelah 2 detik
        }

        document.getElementById('menuIcon').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('modalOverlay').style.display = 'block';
            document.getElementById('cartModal').style.display = 'block';
        });

        document.getElementById('modalClose').addEventListener('click', function() {
            document.getElementById('modalOverlay').style.display = 'none';
            document.getElementById('cartModal').style.display = 'none';
        });

        document.getElementById('modalOverlay').addEventListener('click', function() {
            document.getElementById('modalOverlay').style.display = 'none';
            document.getElementById('cartModal').style.display = 'none';
        });

        // Menampilkan modal metode pembayaran
        document.querySelector('.btn-success').addEventListener('click', function () {
            if (cart.length === 0) {
                alert('Keranjang kosong!');
                return;
            }

            // Menampilkan modal jika variabel 'showPaymentModal' bernilai true
            @if(session('showPaymentModal'))
                document.getElementById('paymentMethodModal').style.display = 'block';
                document.getElementById('modalOverlay').style.display = 'block';
            @endif
            // Tampilkan modal metode pembayaran
            document.getElementById('paymentMethodModal').style.display = 'block';
            document.getElementById('modalOverlay').style.display = 'block';
        });

        // Close modal pembayaran utama
        document.getElementById('modalClosePayment').addEventListener('click', closePaymentModal);

        // Untuk memilih Cash
        document.getElementById('payWithCash').addEventListener('click', function () {
            // Tampilkan modal cash
            document.getElementById('paymentMethodModal').style.display = 'none';
            document.getElementById('cashPaymentModal').style.display = 'block';

            // Tampilkan total bayar
            const totalBayar = cart.reduce((sum, item) => sum + item.total, 0);
            document.getElementById('totalBayar').textContent = `Rp ${totalBayar.toLocaleString('id-ID')}`;
        });

        // Fungsi untuk memformat angka ke Rupiah
        function formatRupiah(angka, prefix = "Rp ") {
            const numberString = angka.replace(/[^,\d]/g, "").toString(); // Hapus karakter non-digit
            const split = numberString.split(",");
            const sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                const separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            return prefix + rupiah + (split[1] !== undefined ? "," + split[1] : "");
        }

        // Event untuk memformat input menjadi Rupiah
        document.getElementById('uangDibayar').addEventListener('input', function (e) {
            const input = e.target;
            const cleanValue = input.value.replace(/[^\d]/g, ""); // Hapus format Rupiah sebelumnya
            input.value = formatRupiah(cleanValue); // Terapkan format Rupiah
        });

        // Proses pembayaran cash
        document.getElementById('submitCashPayment').addEventListener('click', function () {
            const totalBayar = parseFloat(
                document.getElementById('totalBayar').innerText.replace(/[^,\d]/g, "")
            ); // Total bayar tanpa format Rupiah
            const uangDibayar = parseFloat(
                document.getElementById('uangDibayar').value.replace(/[^,\d]/g, "")
            ); // Uang dibayar tanpa format Rupiah

            if (uangDibayar < totalBayar) {
                alert('Uang yang dibayarkan kurang!');
                return;
            }

            const kembalian = uangDibayar - totalBayar;

            submitPayment('Cash', uangDibayar, kembalian);
        });

        // Close modal cash
        document.getElementById('modalCloseCash').addEventListener('click', closePaymentModal);

        // Untuk memilih QRIS
        document.getElementById('payWithQris').addEventListener('click', function () {
            // Tampilkan modal QRIS
            document.getElementById('paymentMethodModal').style.display = 'none';
            document.getElementById('qrisPaymentModal').style.display = 'block';

            // Perbarui total bayar di modal QRIS
            const totalBayar = cart.reduce((sum, item) => sum + item.total, 0);
            document.getElementById('totalBayarQris').textContent = `Rp ${totalBayar.toLocaleString('id-ID')}`;
        });

        // Konfirmasi pembayaran QRIS
        document.getElementById('confirmQrisPayment').addEventListener('click', function () {
            const totalBayar = cart.reduce((sum, item) => sum + item.total, 0);

            // QRIS: uang_dibayar sama dengan total bayar, tidak ada kembalian
            submitPayment('QRIS', totalBayar, 0);
        });

        // Close modal QRIS
        document.getElementById('modalCloseQris').addEventListener('click', closePaymentModal);

        // Helper untuk menutup semua modal
        function closePaymentModal() {
            document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
            document.getElementById('modalOverlay').style.display = 'none';
        }

        // Function untuk submit data pembayaran
        function submitPayment(method, uangDibayar = 0, uangKembalian = 0) {
            try {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/proses-pembayaran'; // Endpoint untuk memproses pembayaran
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="cart" value='${JSON.stringify(cart)}'>
                    <input type="hidden" name="metode" value="${method}">
                    <input type="hidden" name="uang_dibayar" value="${uangDibayar}">
                    <input type="hidden" name="uang_kembalian" value="${uangKembalian}">
                `;
                document.body.appendChild(form);
                form.submit();
            } catch (error) {
                console.error('Terjadi kesalahan saat memproses pembayaran:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
