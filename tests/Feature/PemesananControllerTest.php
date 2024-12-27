<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PemesananControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman index dapat diakses oleh pengguna login
     */
    public function test_index_page_is_accessible_by_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('index.pemesanan'));

        $response->assertStatus(200);
        $response->assertViewIs('pemesanan.index');
    }

    /**
     * Test: Halaman index mengarahkan ke login jika tidak login
     */
    public function test_index_page_redirects_to_login_for_guests()
    {
        $response = $this->get(route('index.pemesanan'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Pilih metode pembayaran berhasil dengan keranjang tidak kosong
     */
    public function test_pilih_metode_succeeds_with_non_empty_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cart = json_encode([
            ['menu_id' => 1, 'quantity' => 2, 'total' => 50000],
        ]);

        $response = $this->post(route('pembayaran.pilih'), ['cart' => $cart]);

        $response->assertStatus(200);
        $response->assertViewIs('pemesanan.index');
        $response->assertViewHas('cart');
    }

    /**
     * Test: Pilih metode pembayaran gagal dengan keranjang kosong
     */
    public function test_pilih_metode_fails_with_empty_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('pembayaran.pilih'), ['cart' => '[]']);

        $response->assertRedirect(route('index.pemesanan'));
        $response->assertSessionHasErrors('message');
    }

    /**
     * Test: Proses pembayaran berhasil
     */
    public function test_proses_pembayaran_succeeds()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $menu = Menu::factory()->create(['stok' => 10, 'status' => 1]);

        $cart = json_encode([
            [
                'menu_id' => $menu->menu_id,
                'name' => $menu->nama_menu,
                'quantity' => 2,
                'price' => $menu->harga, // Tambahkan key 'price'
                'total' => $menu->harga * 2, // Total harga untuk item
            ],
        ]);

        // Pastikan uang_dibayar >= totalBayar
        $uangDibayar = ($menu->harga * 2) + 1000; // Tambahkan sedikit lebih banyak untuk memastikan cukup

        $data = [
            'cart' => $cart,
            'uang_dibayar' => $uangDibayar,
            'metode' => 'Cash',
        ];

        $response = $this->post(route('pembayaran.proses'), $data);

        $response->assertStatus(200); // Pastikan respons sukses
        $response->assertViewIs('pemesanan.struk'); // Periksa view
        $this->assertDatabaseHas('menu', ['menu_id' => $menu->menu_id, 'stok' => 8]); // Stok berkurang
    }

    /**
     * Test: Proses pembayaran gagal jika keranjang kosong
     */
    public function test_proses_pembayaran_fails_with_empty_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'cart' => '[]',
            'uang_dibayar' => 50000,
            'metode' => 'Cash',
        ];

        $response = $this->post(route('pembayaran.proses'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors('message');
    }

    /**
     * Test: Proses pembayaran gagal jika uang yang dibayarkan kurang
     */
    public function test_proses_pembayaran_fails_if_insufficient_payment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $menu = Menu::factory()->create(['stok' => 10, 'status' => 1]);

        $cart = json_encode([
            ['menu_id' => $menu->menu_id, 'quantity' => 2, 'total' => 40000],
        ]);

        $data = [
            'cart' => $cart,
            'uang_dibayar' => 20000,
            'metode' => 'Cash',
        ];

        $response = $this->post(route('pembayaran.proses'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors('message');
    }
}
