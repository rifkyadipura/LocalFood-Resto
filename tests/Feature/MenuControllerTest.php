<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Menu;
use App\Models\Kategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    // **1. Test: Halaman index dapat diakses oleh admin**
    public function test_index_page_is_accessible_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('menu.index'));
        $response->assertStatus(200);
        $response->assertViewIs('menu.index');
    }

    // **2. Test: Halaman index dapat diakses oleh pegawai**
    public function test_index_page_is_accessible_by_pegawai()
    {
        $pegawai = User::factory()->create(['role' => 'pegawai']);
        $response = $this->actingAs($pegawai)->get(route('menu.index'));
        $response->assertStatus(200);
        $response->assertViewIs('menu.index');
    }

    // **3. Test: Admin dapat mengambil data menu (getData)**
    public function test_get_data_returns_valid_json_response()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kategory = Kategory::create(['name' => 'Minuman']);

        Menu::create([
            'name' => 'Es Teh_testing',
            'harga' => 5000,
            'stok' => 20,
            'kategory_id' => $kategory->id,
            'status' => 1,
            'foto' => 'uploads/menu/es_teh_testing.jpg',
            'deskripsi' => 'Minuman segar'
        ]);

        $response = $this->actingAs($admin)->getJson(route('menu.data'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw', 'recordsTotal', 'recordsFiltered', 'data' => [
                '*' => ['id', 'name', 'harga', 'stok', 'status', 'kategory_id']
            ]
        ]);
    }

    // **4. Test: Admin dapat mengakses halaman create**
    public function test_create_page_is_accessible_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('menu.create'));
        $response->assertStatus(200);
        $response->assertViewIs('menu.create');
    }

    public function test_create_page_is_inaccessible_by_pegawai()
    {
        $pegawai = User::factory()->create(['role' => 'pegawai']);
        $response = $this->actingAs($pegawai)->get(route('menu.create'));
        $response->assertStatus(403);
        $response->assertSee('Akses Ditolak');
        $response->assertSee('Anda tidak memiliki izin untuk mengakses halaman ini.');
    }

    // **5. Test: Admin dapat menampilkan detail menu (show)**
    public function test_show_page_displays_menu_details()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kategory = Kategory::create(['name' => 'Makanan']);

        $menu = Menu::create([
            'name' => 'Nasi Goreng_testing',
            'harga' => 15000,
            'stok' => 50,
            'kategory_id' => $kategory->id,
            'status' => 1,
            'foto' => 'uploads/menu/nasi_goreng_testing.jpg',
            'deskripsi' => 'Deskripsi nasi goreng_testing'
        ]);

        $response = $this->actingAs($admin)->get(route('menu.show', $menu->id));
        $response->assertStatus(200);
        $response->assertViewIs('menu.show');
        $response->assertSee('Nasi Goreng_testing');
        $response->assertSee('Deskripsi nasi goreng_testing');
    }

    // **6. Test: Admin dapat mengedit menu (edit)**
    public function test_edit_page_is_accessible_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kategory = Kategory::create(['name' => 'Makanan']);

        $menu = Menu::create([
            'name' => 'Nasi Goreng_testing',
            'harga' => 15000,
            'stok' => 50,
            'kategory_id' => $kategory->id,
            'status' => 1,
            'foto' => 'uploads/menu/nasi_goreng_testing.jpg',
            'deskripsi' => 'Deskripsi nasi goreng_testing'
        ]);

        $response = $this->actingAs($admin)->get(route('menu.edit', $menu->id));
        $response->assertStatus(200);
        $response->assertViewIs('menu.edit');
    }

    // **7. Test: Admin dapat memperbarui menu (update)**
    public function test_menu_can_be_updated_by_admin()
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $kategory = Kategory::create(['name' => 'Makanan']);

        $menu = Menu::create([
            'name' => 'Nasi Goreng_testing',
            'harga' => 15000,
            'stok' => 50,
            'kategory_id' => $kategory->id,
            'status' => 1,
            'foto' => 'uploads/menu/nasi_goreng_testing.jpg',
            'deskripsi' => 'Deskripsi nasi goreng_testing'
        ]);

        $file = UploadedFile::fake()->image('nasi_goreng_spesial_testing.jpg');
        $response = $this->actingAs($admin)->put(route('menu.update', $menu->id), [
            'name' => 'Nasi Goreng Spesial_testing',
            'harga' => 20000,
            'stok' => 25,
            'status' => 1,
            'foto' => $file,
            'deskripsi' => 'Deskripsi spesial_testing'
        ]);
        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseHas('menu', ['name' => 'Nasi Goreng Spesial_testing']);
    }

    // **8. Test: Admin dapat menghapus menu (destroy)**
    public function test_menu_can_be_deleted_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $menu = Menu::create([
            'name' => 'Nasi Goreng_testing',
            'harga' => 15000,
            'stok' => 50,
            'status' => 1,
            'foto' => 'uploads/menu/nasi_goreng_testing.jpg',
        ]);
        $response = $this->actingAs($admin)->delete(route('menu.destroy', $menu->id));
        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseMissing('menu', ['id' => $menu->id]);
    }

    public function test_store_menu_by_admin()
    {
        // **1. Buat folder uploads/menu jika belum ada**
        $uploadPath = public_path('uploads/menu');  // This points to the right directory
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true); // Berikan izin penuh
        }

        // **2. Buat admin dan kategori**
        $admin = User::factory()->create(['role' => 'admin']);
        $kategory = Kategory::create(['name' => 'Makanan']);

        // **3. Simulasi unggahan file gambar**
        $file = UploadedFile::fake()->image('nasi_goreng_testing.jpg')->size(1024); // Ukuran 1MB

        // **4. Kirim permintaan POST untuk menyimpan data**
        $response = $this->actingAs($admin)->post(route('menu.store'), [
            'name' => 'Nasi Goreng_testing',
            'harga' => 15000,
            'stok' => 50,
            'kategory_id' => $kategory->id,
            'status' => true, // Gunakan tipe boolean
            'foto' => $file,
            'deskripsi' => 'Deskripsi nasi goreng_testing'
        ]);

        // **5. Periksa redirect setelah penyimpanan berhasil**
        $response->assertRedirect(route('menu.index'));

        // **6. Verifikasi data di database**
        $this->assertDatabaseHas('menu', [
            'name' => 'Nasi Goreng_testing',
            'harga' => 15000,
            'stok' => 50,
            'status' => 1, // Sesuai dengan database (boolean true disimpan sebagai 1)
            'deskripsi' => 'Deskripsi nasi goreng_testing',
        ]);

        // **7. Pastikan file gambar tersimpan dengan benar di public/uploads/menu**
        $filePath = 'uploads/menu/nasi_goreng_testing.jpg';
        $this->assertTrue(file_exists(public_path($filePath)), 'File gambar tidak ditemukan di folder tujuan.');
    }

    // **Test: Admin memperbarui foto dan semua data**
    // public function test_admin_can_update_menu_with_new_image()
    // {
    //     // Membuat folder uploads/menu jika tidak ada
    //     $uploadPath = public_path('uploads/menu');
    //     if (!file_exists($uploadPath)) {
    //         mkdir($uploadPath, 0777, true);
    //     }

    //     // Pastikan folder bisa ditulis
    //     $this->assertTrue(is_writable($uploadPath), 'Folder tidak memiliki izin write.');

    //     // Membuat file dummy lama
    //     $filePathOld = $uploadPath . '/nasi_goreng_testing.jpg';
    //     file_put_contents($filePathOld, 'dummy content');
    //     $this->assertTrue(file_exists($filePathOld), 'File lama tidak ditemukan sebelum diuji.');

    //     // Membuat admin dan kategori
    //     $admin = User::factory()->create(['role' => 'admin']);
    //     $kategory = Kategory::create(['name' => 'Makanan']);

    //     // Membuat menu
    //     $menu = Menu::create([
    //         'name' => 'Nasi Goreng_testing',
    //         'harga' => 15000,
    //         'stok' => 50,
    //         'kategory_id' => $kategory->id,
    //         'status' => 1,
    //         'foto' => 'uploads/menu/nasi_goreng_testing.jpg',
    //         'deskripsi' => 'Deskripsi nasi goreng_testing'
    //     ]);

    //     // Simulasi unggahan file baru
    //     $fileNew = UploadedFile::fake()->image(uniqid() . '_nasi_goreng_spesial_testing.jpg');

    //     // Kirim permintaan update
    //     $response = $this->actingAs($admin)->put(route('menu.update', $menu->id), [
    //         'name' => 'Nasi Goreng Spesial_testing',
    //         'harga' => 20000,
    //         'stok' => 25,
    //         'status' => 1,
    //         'foto' => $fileNew,
    //         'deskripsi' => 'Deskripsi spesial_testing'
    //     ]);

    //     // Periksa respons HTTP
    //     $response->assertRedirect(route('menu.index'));

    //     // Periksa database
    //     $this->assertDatabaseHas('menus', [
    //         'name' => 'Nasi Goreng Spesial_testing',
    //         'harga' => 20000,
    //         'stok' => 25,
    //         'status' => 1,
    //         'deskripsi' => 'Deskripsi spesial_testing'
    //     ]);

    //     // Cek apakah file lama dihapus
    //     $this->assertFalse(file_exists($filePathOld), 'File lama tidak terhapus.');

    //     // Cek apakah file baru berhasil disimpan
    //     $filePathNew = $uploadPath . '/nasi_goreng_spesial_testing.jpg';
    //     $this->assertTrue(file_exists($filePathNew), 'File baru tidak ditemukan.');
    // }

    // **Test: Pegawai hanya memperbarui stok**
    public function test_pegawai_can_update_stock_only()
    {
        $pegawai = User::factory()->create(['role' => 'pegawai']);
        $kategory = Kategory::create(['name' => 'Makanan']);

        $menu = Menu::create([
            'name' => 'Nasi Goreng_testing',
            'harga' => 15000,
            'stok' => 50,
            'kategory_id' => $kategory->id,
            'status' => 1,
            'foto' => 'uploads/menu/nasi_goreng_testing.jpg',
            'deskripsi' => 'Deskripsi nasi goreng_testing'
        ]);

        $response = $this->actingAs($pegawai)->put(route('menu.update', $menu->id), [
            'stok' => 40,
        ]);

        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseHas('menu', ['stok' => 40]);
    }

    // **Test: User tanpa izin ditolak (403 Forbidden)**
    public function test_user_without_permission_cannot_update_menu()
    {
        $user = User::factory()->create(['role' => 'customer']); // Role lain
        $kategory = Kategory::create(['name' => 'Makanan']);

        $menu = Menu::create([
            'name' => 'Nasi Goreng_testing',
            'harga' => 15000,
            'stok' => 50,
            'kategory_id' => $kategory->id,
            'status' => 1,
            'foto' => 'uploads/menu/nasi_goreng_testing.jpg',
            'deskripsi' => 'Deskripsi nasi goreng_testing'
        ]);

        $response = $this->actingAs($user)->put(route('menu.update', $menu->id), [
            'name' => 'Unauthorized Update',
        ]);

        $response->assertStatus(403); // Forbidden
    }

    public function test_menu_can_be_deleted_by_admin_with_image()
    {
        // Pastikan folder tujuan ada
        $uploadPath = public_path('uploads/menu');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Setup admin dan kategori
        $admin = User::factory()->create(['role' => 'admin']);
        $kategory = Kategory::create(['name' => 'Makanan']);

        // Simulasi file
        $filePath = 'uploads/menu/nasi_goreng_testing.jpg';
        file_put_contents(public_path($filePath), 'dummy content'); // Buat file dummy

        $menu = Menu::create([
            'name' => 'Nasi Goreng_testing',
            'harga' => 15000,
            'stok' => 50,
            'kategory_id' => $kategory->id,
            'status' => 1,
            'foto' => $filePath,
        ]);

        // Kirim request DELETE
        $response = $this->actingAs($admin)->delete(route('menu.destroy', $menu->id));

        // Pastikan redirect berhasil
        $response->assertRedirect(route('menu.index'));

        // Pastikan data dihapus dari database
        $this->assertDatabaseMissing('menu', ['id' => $menu->id]);

        // Pastikan file gambar dihapus
        $this->assertFalse(file_exists(public_path($filePath)), 'File tidak dihapus.');
    }
}
