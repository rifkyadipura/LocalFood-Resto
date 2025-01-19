<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transactions = [];
        $date = Carbon::create(2025, 1, 1, 8, 0, 0); // Mulai dari jam 08:00 pagi

        // Daftar semua menu yang tersedia
        $allMenus = [
            ['menu_id' => 1, 'harga' => 15000.00], // Soto Bandung
            ['menu_id' => 3, 'harga' => 18000.00], // Nasi Goreng Spesial
            ['menu_id' => 5, 'harga' => 25000.00], // Rendang Daging Sapi
            ['menu_id' => 8, 'harga' => 8000.00],  // Pisang Goreng Keju
            ['menu_id' => 9, 'harga' => 5000.00],  // Tahu Isi
            ['menu_id' => 10, 'harga' => 7000.00], // Risoles Mayo
            ['menu_id' => 11, 'harga' => 5000.00], // Es Teh Manis
            ['menu_id' => 12, 'harga' => 12000.00], // Es Kopi Susu
            ['menu_id' => 13, 'harga' => 7000.00],  // Es Jeruk Segar
            ['menu_id' => 14, 'harga' => 10000.00], // Teh Tarik
        ];

        // Pecahan uang rupiah yang tersedia
        $availableBills = [1000, 2000, 5000, 10000, 20000, 50000, 75000, 100000];

        // Loop untuk membuat data harian dari 1 Januari hingga 18 Januari 2025
        for ($i = 0; $i < 18; $i++) { // Perpanjangan hingga 18 hari
            $dailyMenus = collect($allMenus)->shuffle()->take(rand(5, 8)); // Pilih menu acak antara 5 hingga 8 setiap hari
            $dailyTime = $date->copy();

            $dailyTransactionCount = 1; // Reset jumlah transaksi harian ke 1

            foreach ($dailyMenus as $menu) {
                $jumlah = rand(1, 3); // Jumlah menu yang dibeli
                $totalHarga = $menu['harga'] * $jumlah;

                // Tentukan metode pembayaran secara acak
                $metodePembayaran = rand(0, 1) ? 'QRIS' : 'Cash';

                if ($metodePembayaran === 'QRIS') {
                    $uangDibayar = $totalHarga; // QRIS uangnya pas
                    $uangKembalian = 0; // Tidak ada kembalian
                } else {
                    // Pilih uangDibayar secara acak, pastikan cukup untuk membayar totalHarga
                    $uangDibayar = collect($availableBills)
                        ->filter(fn($bill) => $bill >= $totalHarga) // Ambil hanya pecahan yang cukup
                        ->random(); // Pilih salah satu secara acak

                    $uangKembalian = $uangDibayar - $totalHarga;
                }

                $transactions[] = [
                    'transaksi_id' => count($transactions) + 1,
                    'kode_transaksi' => 'TRX-' . $date->format('Ymd') . '-' . $dailyTransactionCount,
                    'menu_id' => $menu['menu_id'],
                    'users_id' => 3,
                    'jumlah' => $jumlah,
                    'total_harga' => $totalHarga,
                    'uang_dibayar' => $uangDibayar,
                    'uang_kembalian' => $uangKembalian,
                    'metode_pembayaran' => $metodePembayaran,
                    'created_at' => $dailyTime->format('Y-m-d H:i:s'),
                    'updated_at' => $dailyTime->format('Y-m-d H:i:s'),
                ];

                $dailyTransactionCount++; // Increment jumlah transaksi harian

                // Tambahkan waktu untuk transaksi berikutnya di hari yang sama
                $dailyTime->addHours(rand(1, 3));
            }

            // Tambahkan hari berikutnya untuk transaksi berikutnya
            $date->addDay()->setTime(8, 0, 0); // Reset waktu ke jam 08:00 pagi
        }

        // Masukkan data ke database
        DB::table('transaksi')->insert($transactions);
    }
}
