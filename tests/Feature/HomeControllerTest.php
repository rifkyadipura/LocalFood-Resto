<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman home dapat diakses oleh pengguna yang telah login
     */
    public function test_home_page_is_accessible_to_authenticated_users()
    {
        // Arrange: Membuat pengguna dan login
        $user = User::factory()->create([
            'nama_lengkap' => 'Test User',
        ]);
        $this->actingAs($user);

        // Act: Akses halaman home
        $response = $this->get('/home');

        // Assert: Periksa status dan view
        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /**
     * Test: Halaman home mengarahkan ke login jika pengguna tidak terautentikasi
     */
    public function test_home_page_redirects_when_not_authenticated()
    {
        // Act: Akses halaman home tanpa login
        $response = $this->get('/home');

        // Assert: Periksa pengalihan ke halaman login
        $response->assertRedirect('/login');
    }

    /**
     * Test: Akses fungsi index langsung melalui HTTP
     */
    public function test_index_function_returns_home_view_via_http()
    {
        // Arrange: Membuat pengguna dan login
        $user = User::factory()->create([
            'nama_lengkap' => 'Test User',
        ]);
        $this->actingAs($user);

        // Act: Akses halaman home
        $response = $this->get('/home');

        // Assert: Pastikan view yang dikembalikan adalah 'home'
        $response->assertViewIs('home');
    }
}
