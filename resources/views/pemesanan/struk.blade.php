{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Struk Pembayaran</h1>
<table class="table table-bordered">
    <thead>
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
</table>
<p><strong>Total Bayar:</strong> Rp {{ number_format($totalBayar, 2, ',', '.') }}</p>
<p><strong>Uang Dibayar:</strong> Rp {{ number_format($uangDibayar, 2, ',', '.') }}</p>
<p><strong>Uang Kembalian:</strong> Rp {{ number_format($uangKembalian, 2, ',', '.') }}</p>

</body>
</html> --}}
<h1>Struk Pembayaran</h1>
<table class="table table-bordered">
    <thead>
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
</table>
<p><strong>Total Bayar:</strong> Rp {{ number_format($totalBayar, 2, ',', '.') }}</p>
<p><strong>Uang Dibayar:</strong> Rp {{ number_format($uangDibayar, 2, ',', '.') }}</p>
<p><strong>Uang Kembalian:</strong> Rp {{ number_format($uangKembalian, 2, ',', '.') }}</p>
