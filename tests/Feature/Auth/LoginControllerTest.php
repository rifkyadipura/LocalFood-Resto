<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman login dapat diakses oleh pengguna tamu
     */
    public function test_login_page_is_accessible_by_guest()
    {
        // Act: Akses halaman login
        $response = $this->get('/login');

        // Assert: Halaman dapat diakses dan menggunakan view yang benar
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Test: Login berhasil dengan kredensial yang benar
     */
    public function test_login_succeeds_with_valid_credentials()
    {
        // Arrange: Membuat pengguna
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Act: Kirim permintaan login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // Assert: Periksa pengalihan setelah login
        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Login gagal dengan kredensial yang salah
     */
    public function test_login_fails_with_invalid_credentials()
    {
        // Arrange: Data login yang salah
        $data = [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ];

        // Act: Kirim permintaan POST ke rute login
        $response = $this->post(route('login'), $data);

        // Assert: Periksa bahwa pengguna diarahkan kembali ke halaman login
        $response->assertStatus(302); // Redirect status
        $response->assertRedirect('/'); // Arahkan ke root atau halaman yang sesuai
        $response->assertSessionHasErrors('email', 'These credentials do not match our records.');
    }
    /**
     * Test: Logout berhasil
     */
    public function test_logout_succeeds()
    {
        // Arrange: Membuat dan login pengguna
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Kirim permintaan logout
        $response = $this->post('/logout');

        // Assert: Periksa pengalihan setelah logout dan pengguna tidak lagi terautentikasi
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * Test: Logout gagal untuk pengguna tamu
     */
    public function test_logout_fails_for_guest()
    {
        // Act: Kirim permintaan logout tanpa login
        $response = $this->post('/logout');

        // Assert: Periksa pengalihan kembali ke halaman login dan tetap tamu
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
