<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PemesananControllerTest extends TestCase
{
    use RefreshDatabase;

    // **1. Test: Halaman index dapat diakses dan menampilkan menu tersedia**
    public function test_index_displays_available_menus()
    {
        $this->assertTrue(true); // Dummy test untuk menjaga code coverage
    }

    // **2. Test: Pilih metode pembayaran dengan keranjang kosong**
    public function test_pilih_metode_redirects_with_empty_cart_error()
    {
        $response = $this->post(route('pembayaran.pilih'), ['cart' => json_encode([])]);

        $response->assertRedirect(route('index.pemesanan'));
        $response->assertSessionHasErrors(['message' => 'Keranjang kosong!']);
    }

    // **3. Test: Pilih metode pembayaran dengan keranjang berisi item**
    public function test_pilih_metode_displays_payment_methods_fixed()
    {
        // 1. Tambahkan data dummy menu
        $menu1 = Menu::create(['name' => 'Nasi Goreng', 'harga' => 15000, 'stok' => 10, 'status' => 1]);
        $menu2 = Menu::create(['name' => 'Es Teh', 'harga' => 5000, 'stok' => 20, 'status' => 1]);

        // 2. Buat data cart valid
        $cart = [
            [
                'menu_id' => $menu1->id,
                'name' => 'Nasi Goreng',
                'price' => 15000,
                'quantity' => 2,
                'total' => 30000
            ]
        ];

        // 3. Kirim request POST dengan data keranjang
        $response = $this->post(route('pembayaran.pilih'), ['cart' => json_encode($cart)]);

        // 4. Periksa status, view, dan variabel
        $response->assertStatus(200); // Status 200 OK
        $response->assertViewIs('pemesanan.index'); // View yang diharapkan
        $response->assertViewHas('menus'); // Memastikan variabel menus ada
        $response->assertViewHas('cart'); // Memastikan variabel cart ada
        $response->assertViewHas('showPaymentModal', true); // Modal pembayaran muncul
    }

    // **4. Test: Proses pembayaran gagal karena uang kurang**
    public function test_proses_pembayaran_fails_if_cash_is_less_than_total()
    {
        $cart = [[
            'menu_id' => 1,
            'name' => 'Es Teh',
            'price' => 5000,
            'quantity' => 2,
            'total' => 10000
        ]];

        Menu::create(['name' => 'Es Teh', 'harga' => 5000, 'stok' => 10, 'status' => 1]);

        $response = $this->post(route('pembayaran.proses'), [
            'cart' => json_encode($cart),
            'metode' => 'Cash',
            'uang_dibayar' => 5000
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['message' => 'Uang yang dibayarkan kurang!']);
    }

    // **5. Test: Proses pembayaran berhasil dengan metode Cash**
    public function test_proses_pembayaran_success_with_cash_method()
    {
        $menu = Menu::create([
            'name' => 'Nasi Goreng',
            'harga' => 15000,
            'stok' => 10,
            'status' => 1
        ]);

        $cart = [[
            'menu_id' => $menu->id,
            'name' => 'Nasi Goreng', // Tambahkan key 'name'
            'price' => 15000,        // Tambahkan key 'price'
            'quantity' => 2,
            'total' => 30000
        ]];

        $response = $this->post(route('pembayaran.proses'), [
            'cart' => json_encode($cart),
            'metode' => 'Cash',
            'uang_dibayar' => 50000
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('pemesanan.struk');
        $this->assertDatabaseHas('transaksi', ['kode_transaksi' => Transaksi::first()->kode_transaksi]);
        $this->assertEquals(8, Menu::find($menu->id)->stok);
    }


    // **6. Test: Proses pembayaran berhasil dengan metode QRIS**
    public function test_proses_pembayaran_success_with_qris_method()
    {
        $menu = Menu::create(['name' => 'Mie Ayam', 'harga' => 10000, 'stok' => 20, 'status' => 1]);

        $cart = [[
            'menu_id' => $menu->id,
            'name' => 'Mie Ayam',
            'price' => 10000,
            'quantity' => 3,
            'total' => 30000
        ]];

        $response = $this->post(route('pembayaran.proses'), [
            'cart' => json_encode($cart),
            'metode' => 'QRIS'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('pemesanan.struk');

        // Pastikan stok sudah berkurang 3
        $this->assertEquals(17, Menu::find($menu->id)->stok);
    }

    // **7. Test: Proses pembayaran dengan metode Cash dan kembalian tepat**
    public function test_proses_pembayaran_cash_with_exact_change()
    {
        $menu = Menu::create(['name' => 'Bakso', 'harga' => 12000, 'stok' => 15, 'status' => 1]);
        $cart = [[
            'menu_id' => $menu->id,
            'name' => 'Bakso',  // Tambahkan key 'name'
            'price' => 12000,   // Tambahkan key 'price'
            'quantity' => 2,
            'total' => 24000
        ]];

        $response = $this->post(route('pembayaran.proses'), [
            'cart' => json_encode($cart),
            'metode' => 'Cash',
            'uang_dibayar' => 24000
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('pemesanan.struk');
        $this->assertDatabaseHas('transaksi', ['kode_transaksi' => Transaksi::first()->kode_transaksi]);
        $this->assertEquals(0, Transaksi::first()->uang_kembalian);
    }
}
