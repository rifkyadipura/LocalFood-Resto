<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    // **1. Test: Halaman index dapat diakses oleh admin**
    public function test_index_page_is_accessible_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    // **2. Test: Halaman index ditolak untuk non-admin**
    public function test_index_page_is_denied_for_non_admin()
    {
        $pegawai = User::factory()->create(['role' => 'pegawai']);
        $response = $this->actingAs($pegawai)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertViewIs('errors.error');
    }

    // **3. Test untuk getData(): Mengembalikan JSON yang valid**
    public function test_get_data_returns_valid_json_response()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory(10)->create();

        $response = $this->actingAs($admin)->getJson(route('users.data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw', 'recordsTotal', 'recordsFiltered', 'data' => [
                '*' => ['id', 'name', 'email', 'role', 'created_at']
            ]
        ]);
    }

    // **4. Test untuk getData(): Mengembalikan data kosong jika tidak ada user**
    public function test_get_data_returns_empty_when_no_users_exist()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->getJson(route('users.data'));

        $response->assertStatus(200);
        $response->assertJson([
            'data' => []
        ]);
    }

    // **5. Test untuk getData(): Ditolak untuk non-admin**
    public function test_get_data_is_denied_for_non_admin()
    {
        $pegawai = User::factory()->create(['role' => 'pegawai']);
        $response = $this->actingAs($pegawai)->getJson(route('users.data'));

        $response->assertStatus(403); // Unauthorized
        $response->assertJson(['error' => 'Unauthorized']);
    }

    // **6. Test untuk edit(): Dapat diakses oleh admin**
    public function test_edit_page_is_accessible_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('users.edit', $user->id));
        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas('user', $user);
    }

    // **7. Test untuk edit(): Ditolak untuk non-admin**
    public function test_edit_page_is_denied_for_non_admin()
    {
        $pegawai = User::factory()->create(['role' => 'pegawai']);
        $user = User::factory()->create();

        $response = $this->actingAs($pegawai)->get(route('users.edit', $user->id));
        $response->assertStatus(200);
        $response->assertViewIs('errors.error');
    }

    // **8. Test untuk edit(): Menampilkan error jika user tidak ditemukan**
    public function test_edit_page_shows_error_if_user_not_found()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('users.edit', 999)); // ID yang tidak valid

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error', 'User tidak ditemukan!');
    }

    // **9. Test: User dapat diperbarui oleh admin**
    public function test_user_can_be_updated_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->put(route('users.update', $user->id), [
            'name' => 'Updated Name',
            'email' => 'updatedemail@example.com',
            'role' => 'pegawai'
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updatedemail@example.com',
            'role' => 'pegawai'
        ]);
    }

    // **10. Test: User dapat dihapus oleh admin**
    public function test_user_can_be_deleted_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->delete(route('users.destroy', $user->id));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
