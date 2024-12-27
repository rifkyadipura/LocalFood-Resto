<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Endpoint getData mengarahkan ke login untuk pengguna yang tidak login
     */
    public function test_get_data_redirects_to_login_for_guests()
    {
        // Act: Akses endpoint getData tanpa login
        $response = $this->get(route('users.data'));

        // Assert: Periksa pengalihan ke halaman login
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Halaman index users dapat diakses oleh admin atau kepala staf
     */
    public function test_index_page_accessible_by_admin_or_kepala_staf()
    {
        // Arrange: Membuat pengguna dengan role admin
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Act: Akses halaman index users
        $response = $this->get(route('users.index'));

        // Assert: Periksa status dan view
        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    /**
     * Test: Halaman index users menampilkan error untuk non-admin dan non-kepala staf
     */
    public function test_index_page_shows_error_for_non_admin_and_non_kepala_staf()
    {
        // Arrange: Membuat pengguna dengan role selain admin/kepala staf
        $user = User::factory()->create(['role' => 'Kasir']);
        $this->actingAs($user);

        // Act: Akses halaman index users
        $response = $this->get(route('users.index'));

        // Assert: Periksa status dan view error
        $response->assertStatus(200);
        $response->assertViewIs('errors.error');
    }

    /**
     * Test: Endpoint getData mengembalikan data users
     */
    public function test_get_data_returns_users_data()
    {
        // Arrange: Membuat pengguna dan beberapa data user lainnya
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);
        User::factory(10)->create();

        // Act: Akses endpoint getData
        $response = $this->get(route('users.data'));

        // Assert: Periksa respons JSON
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Test: Endpoint getData memfilter role admin
     */
    public function test_get_data_excludes_admin_role()
    {
        // Arrange: Membuat pengguna kepala staf dan data users
        $user = User::factory()->create(['role' => 'Kepala Staf']);
        $this->actingAs($user);

        $adminUser = User::factory()->create(['role' => 'admin']);
        $staffUser = User::factory()->create(['role' => 'Kasir']);

        // Act: Akses endpoint getData
        $response = $this->get(route('users.data'));

        // Assert: Pastikan admin tidak ada di data
        $response->assertStatus(200);
        $this->assertStringNotContainsString($adminUser->email, $response->getContent());
        $this->assertStringContainsString($staffUser->email, $response->getContent());
    }

    /**
     * Test: Endpoint getData unauthorized untuk pengguna tidak login
     */
    public function test_get_data_unauthorized_for_guests()
    {
        // Act: Akses endpoint getData tanpa login
        $response = $this->get(route('users.data'));

        // Assert: Periksa pengalihan ke halaman login
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Halaman edit users dapat diakses
     */
    public function test_edit_page_accessible()
    {
        // Arrange: Membuat pengguna dengan role admin
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $targetUser = User::factory()->create();

        // Act: Akses halaman edit
        $response = $this->get(route('users.edit', $targetUser->users_id));

        // Assert: Periksa status dan view
        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas('user', $targetUser);
    }

    /**
     * Test: Halaman edit users menampilkan error jika user tidak ditemukan
     */
    public function test_edit_page_shows_error_if_user_not_found()
    {
        // Arrange: Membuat pengguna admin
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Act: Akses halaman edit dengan ID yang tidak ada
        $response = $this->get(route('users.edit', 9999));

        // Assert: Periksa redirect dan pesan error
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error', 'User tidak ditemukan!');
    }

    /**
     * Test: Halaman edit users menampilkan akses ditolak untuk non-admin
     */
    public function test_edit_page_shows_access_denied_for_non_admin()
    {
        // Arrange: Membuat pengguna non-admin
        $user = User::factory()->create(['role' => 'Kasir']);
        $this->actingAs($user);

        // Act: Akses halaman edit
        $response = $this->get(route('users.edit', 1));

        // Assert: Periksa status dan view error
        $response->assertStatus(200);
        $response->assertViewIs('errors.error');
        $response->assertViewHas(['title', 'message', 'redirectUrl']);
    }

    /**
     * Test: Update user berhasil
     */
    public function test_update_user_successfully()
    {
        // Arrange: Membuat pengguna admin dan pengguna yang akan diupdate
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $targetUser = User::factory()->create();
        $updatedData = [
            'nama_lengkap' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'Kasir',
        ];

        // Act: Akses endpoint update
        $response = $this->put(route('users.update', $targetUser->users_id), $updatedData);

        // Assert: Periksa redirect dan data terupdate
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'users_id' => $targetUser->users_id,
            'nama_lengkap' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'Kasir',
        ]);
    }

    /**
     * Test: Hapus user berhasil
     */
    public function test_destroy_user_successfully()
    {
        // Arrange: Membuat pengguna admin dan pengguna yang akan dihapus
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $targetUser = User::factory()->create();

        // Act: Akses endpoint destroy
        $response = $this->delete(route('users.destroy', $targetUser->users_id));

        // Assert: Periksa redirect dan data terhapus
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['users_id' => $targetUser->users_id]);
    }
}
