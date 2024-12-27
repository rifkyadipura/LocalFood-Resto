<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman registrasi hanya dapat diakses oleh admin atau kepala staf.
     */
    public function test_registration_page_is_accessible_by_admin_or_kepala_staf()
    {
        $roles = ['admin', 'Kepala Staf'];
        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user);

            $response = $this->get('/register'); // Sesuai dengan Auth::routes()
            $response->assertStatus(200);
            $response->assertViewIs('auth.register');
        }
    }

    /**
     * Test: Halaman registrasi tidak dapat diakses oleh peran lain.
     */
    public function test_registration_page_is_inaccessible_by_other_roles()
    {
        $user = User::factory()->create(['role' => 'Kasir']);
        $this->actingAs($user);

        $response = $this->get('/register'); // Sesuai dengan Auth::routes()
        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test: Admin atau Kepala Staf berhasil membuat akun untuk Kasir.
     */
    public function test_admin_or_kepala_staf_can_register_kasir()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $userData = [
            'nama_lengkap' => 'Kasir Baru',
            'email' => 'kasirbaru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Kasir',
        ];

        $response = $this->post('/register', $userData); // Sesuai dengan Auth::routes()
        $response->assertRedirect('/users'); // Pastikan route ini benar dalam aplikasi Anda
        $this->assertDatabaseHas('users', [
            'email' => 'kasirbaru@example.com',
        ]);
    }

    /**
     * Test: Non-admin atau non-kepala staf tidak dapat membuat akun.
     */
    public function test_non_admin_or_non_kepala_staf_cannot_register_users()
    {
        $kasir = User::factory()->create(['role' => 'Kasir']);
        $this->actingAs($kasir);

        $userData = [
            'nama_lengkap' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Kasir',
        ];

        $response = $this->post('/register', $userData); // Sesuai dengan Auth::routes()
        $response->assertStatus(403);
    }

    /**
     * Test: Pengguna yang tidak terautentikasi tidak dapat mengakses registrasi.
     */
    public function test_unauthenticated_users_redirect_to_login()
    {
        $response = $this->get(route('register'));

        // Arahkan ke login jika tidak terautentikasi
        $response->assertRedirect('/login');
    }
}
