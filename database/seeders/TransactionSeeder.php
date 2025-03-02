<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
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
            ['menu_item_id' => 1, 'price' => 15000.00], // Soto Bandung
            ['menu_item_id' => 3, 'price' => 18000.00], // Nasi Goreng Spesial
            ['menu_item_id' => 5, 'price' => 25000.00], // Rendang Daging Sapi
            ['menu_item_id' => 8, 'price' => 8000.00],  // Pisang Goreng Keju
            ['menu_item_id' => 9, 'price' => 5000.00],  // Tahu Isi
            ['menu_item_id' => 10, 'price' => 7000.00], // Risoles Mayo
            ['menu_item_id' => 11, 'price' => 5000.00], // Es Teh Manis
            ['menu_item_id' => 12, 'price' => 12000.00], // Es Kopi Susu
            ['menu_item_id' => 13, 'price' => 7000.00],  // Es Jeruk Segar
            ['menu_item_id' => 14, 'price' => 10000.00], // Teh Tarik
        ];

        // Pecahan uang rupiah yang tersedia
        $availableBills = [1000, 2000, 5000, 10000, 20000, 50000, 75000, 100000];

        // Loop untuk membuat data harian dari 1 Januari hingga 18 Januari 2025
        for ($i = 0; $i < 18; $i++) { // Perpanjangan hingga 18 hari
            $dailyMenus = collect($allMenus)->shuffle()->take(rand(5, 8)); // Pilih menu acak antara 5 hingga 8 setiap hari
            $dailyTime = $date->copy();

            $dailyTransactionCount = 1; // Reset jumlah transaksi harian ke 1

            foreach ($dailyMenus as $menu) {
                $quantity = rand(1, 3); // Jumlah menu yang dibeli
                $totalPrice = $menu['price'] * $quantity;

                // Hitung pajak (10%) dan total harga setelah pajak
                $tax = $totalPrice * 0.1;
                $totalPriceTaxed = $totalPrice + $tax;

                // Tentukan metode pembayaran secara acak
                $paymentMethod = rand(0, 1) ? 'QRIS' : 'Cash';

                if ($paymentMethod === 'QRIS') {
                    $amountPaid = $totalPriceTaxed; // QRIS uangnya pas
                    $changeAmount = 0; // Tidak ada kembalian
                } else {
                    // Pilih uangDibayar secara acak, pastikan cukup untuk membayar totalPriceTaxed
                    $amountPaid = collect($availableBills)
                        ->filter(fn($bill) => $bill >= $totalPriceTaxed) // Ambil hanya pecahan yang cukup
                        ->random(); // Pilih salah satu secara acak

                    $changeAmount = $amountPaid - $totalPriceTaxed;
                }

                $transactions[] = [
                    'transaction_id' => count($transactions) + 1,
                    'transaction_code' => 'TRX-' . $date->format('Ymd') . '-' . $dailyTransactionCount,
                    'menu_item_id' => $menu['menu_item_id'],
                    'user_id' => 3,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                    'total_price_taxed' => $totalPriceTaxed,
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'payment_method' => $paymentMethod,
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
        DB::table('transactions')->insert($transactions);
    }
}
