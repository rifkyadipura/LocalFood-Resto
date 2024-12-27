<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\Kategory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman index menu dapat diakses
     */
    public function test_index_page_is_accessible()
    {
        // Arrange: Membuat pengguna dan login
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Akses halaman index menu
        $response = $this->get(route('menu.index'));

        // Assert: Periksa status dan view
        $response->assertStatus(200);
        $response->assertViewIs('menu.index');
    }

    /**
     * Test: Endpoint getData mengembalikan data menu
     */
    public function test_get_data_returns_menu_data()
    {
        // Arrange: Membuat pengguna dan beberapa data menu
        $user = User::factory()->create();
        $this->actingAs($user);

        Kategory::factory()->create();
        Menu::factory(5)->create();

        // Act: Akses endpoint getData
        $response = $this->get(route('menu.data'));

        // Assert: Periksa respons JSON
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Test: Halaman create menu dapat diakses oleh admin atau kepala staf
     */
    public function test_create_page_is_accessible_by_admin_or_kepala_staf()
    {
        // Arrange: Membuat pengguna admin dan login
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Act: Akses halaman create menu
        $response = $this->get(route('menu.create'));

        // Assert: Periksa status dan view
        $response->assertStatus(200);
        $response->assertViewIs('menu.create');
    }

    /**
     * Test: Halaman create menu tidak dapat diakses oleh non-admin
     */
    public function test_create_page_is_inaccessible_by_non_admin()
    {
        // Arrange: Membuat pengguna biasa
        $user = User::factory()->create(['role' => 'Kasir']);
        $this->actingAs($user);

        // Act: Akses halaman create menu
        $response = $this->get(route('menu.create'));

        // Assert: Periksa redirect dan pesan error
        $response->assertStatus(403);
    }

    /**
     * Test: Menampilkan detail menu berhasil
     */
    public function test_show_menu_details()
    {
        // Arrange: Membuat pengguna dan menu
        $user = User::factory()->create();
        $this->actingAs($user);

        $menu = Menu::factory()->create();

        // Act: Akses halaman detail menu
        $response = $this->get(route('menu.show', $menu->menu_id));

        // Assert: Periksa status dan data yang ditampilkan
        $response->assertStatus(200);
        $response->assertViewIs('menu.show');
        $response->assertViewHas('menu', $menu);
    }

    /**
     * Test: Menyimpan menu berhasil
     */
    public function test_store_menu_successfully()
    {
        // Arrange: Membuat pengguna admin dan login
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        Storage::fake('public');
        $file = UploadedFile::fake()->image('menu.jpg');

        $data = [
            'nama_menu' => 'Nasi Goreng',
            'harga' => 20000,
            'stok' => 10,
            'status' => 1,
            'foto' => $file,
            'deskripsi' => 'Makanan enak',
            'kategory_id' => Kategory::factory()->create()->id,
        ];

        // Act: Simpan menu baru
        $response = $this->post(route('menu.store'), $data);

        // Assert: Periksa redirect dan data tersimpan
        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseHas('menu', ['nama_menu' => 'Nasi Goreng']);
    }

    /**
     * Test: Halaman edit menu dapat diakses
     */
    public function test_edit_page_is_accessible()
    {
        // Arrange: Membuat pengguna admin dan menu
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $menu = Menu::factory()->create();

        // Act: Akses halaman edit menu
        $response = $this->get(route('menu.edit', $menu->menu_id));

        // Assert: Periksa status dan view
        $response->assertStatus(200);
        $response->assertViewIs('menu.edit');
        $response->assertViewHas('menu', $menu);
    }

    /**
     * Test: Update menu berhasil
     */
    public function test_update_menu_successfully()
    {
        // Arrange: Membuat pengguna admin dan menu
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $menu = Menu::factory()->create();
        $updatedData = [
            'nama_menu' => 'Nasi Uduk',
            'harga' => 15000,
            'stok' => 5,
            'status' => 1,
            'deskripsi' => 'Updated description',
        ];

        // Act: Update menu
        $response = $this->put(route('menu.update', $menu->menu_id), $updatedData);

        // Assert: Periksa redirect dan data terupdate
        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseHas('menu', ['nama_menu' => 'Nasi Uduk']);
    }

    /**
     * Test: Update menu gagal untuk non-admin
     */
    public function test_update_menu_fails_for_non_admin()
    {
        // Arrange: Membuat pengguna non-admin dan menu
        $user = User::factory()->create(['role' => 'Kasir']);
        $this->actingAs($user);

        $menu = Menu::factory()->create();
        $updatedData = [
            'nama_menu' => 'Nasi Uduk',
            'harga' => 15000,
            'stok' => 10,
            'status' => 1,
            'deskripsi' => 'Updated description',
        ];

        // Act: Update menu
        $response = $this->put(route('menu.update', $menu->menu_id), $updatedData);

        // Assert: Periksa pengalihan ke halaman yang sesuai
        $response->assertStatus(302);
        $response->assertRedirect(route('menu.index')); // Sesuaikan dengan pengalihan aktual
    }

    /**
     * Test: Hapus menu berhasil
     */
    public function test_destroy_menu_successfully()
    {
        // Arrange: Membuat pengguna admin dan menu
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $menu = Menu::factory()->create();

        // Act: Hapus menu
        $response = $this->delete(route('menu.destroy', $menu->menu_id));

        // Assert: Periksa redirect dan data terhapus
        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseMissing('menu', ['menu_id' => $menu->menu_id]);
    }

    /**
     * Test: Hapus menu gagal untuk non-admin
     */
    public function test_destroy_menu_fails_for_non_admin()
    {
        // Arrange: Membuat pengguna non-admin dan menu
        $user = User::factory()->create(['role' => 'Kasir']);
        $this->actingAs($user);

        $menu = Menu::factory()->create();

        // Act: Hapus menu
        $response = $this->delete(route('menu.destroy', $menu->menu_id));

        // Assert: Periksa pengalihan ke halaman yang sesuai
        $response->assertStatus(302);
        $response->assertRedirect(route('menu.index')); // Sesuaikan dengan pengalihan aktual
    }
}
