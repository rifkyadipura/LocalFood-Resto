<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <!-- Link ke CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link ke Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg rounded-lg border-0">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center rounded-top">
                <h4 class="mb-0">Struk Pembayaran</h4>
            </div>
            <div class="card-body">
                <!-- Tabel Detail Struk -->
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Nama</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cart as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>Rp {{ number_format($item['price'], 2, ',', '.') }}</td>
                            <td>Rp {{ number_format($item['total'], 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Bayar:</strong></td>
                            <td>Rp {{ number_format($totalBayar, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Uang Dibayar:</strong></td>
                            <td>Rp {{ number_format($uangDibayar, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Uang Kembalian:</strong></td>
                            <td>Rp {{ number_format($uangKembalian, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Metode Pembayaran:</strong></td>
                            <td><strong>{{ $metode }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="/" class="btn btn-secondary no-print">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Halaman Awal
                </a>
                <button class="btn btn-primary no-print" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Cetak Struk
                </button>
            </div>
        </div>
    </div>

    <!-- Link ke JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
