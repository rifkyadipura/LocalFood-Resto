<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    // **1. Test: Halaman home dapat diakses oleh pengguna yang telah login**
    public function test_home_page_is_accessible_to_authenticated_users()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get('/home');
        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    // **2. Test: Halaman home mengarahkan ke login jika pengguna tidak terautentikasi**
    public function test_home_page_redirects_when_not_authenticated()
    {
        $response = $this->get('/home');
        $response->assertRedirect('/login');
    }
}
