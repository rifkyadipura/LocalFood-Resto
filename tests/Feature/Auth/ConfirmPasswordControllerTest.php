<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ConfirmPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman konfirmasi password dapat diakses oleh pengguna yang diautentikasi
     */
    public function test_confirm_password_page_is_accessible_by_authenticated_user()
    {
        // Arrange: Membuat pengguna dan login
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Akses halaman konfirmasi password
        $response = $this->get(route('password.confirm'));

        // Assert: Periksa status dan view
        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.confirm');
    }

    /**
     * Test: Halaman konfirmasi password tidak dapat diakses oleh pengguna yang tidak diautentikasi
     */
    public function test_confirm_password_page_is_inaccessible_by_guest()
    {
        // Act: Akses halaman konfirmasi password tanpa login
        $response = $this->get(route('password.confirm'));

        // Assert: Periksa redirect ke halaman login
        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Konfirmasi password berhasil dengan kata sandi yang benar
     */
    public function test_confirm_password_succeeds_with_correct_password()
    {
        // Arrange: Membuat pengguna dan login
        $password = 'correct-password';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);
        $this->actingAs($user);

        // Act: Kirim permintaan konfirmasi password
        $response = $this->post(route('password.confirm'), [
            'password' => $password,
        ]);

        // Assert: Periksa redirect ke halaman tujuan setelah konfirmasi
        $response->assertRedirect(route('home'));
        $response->assertSessionHasNoErrors();
    }

    /**
     * Test: Konfirmasi password gagal dengan kata sandi yang salah
     */
    public function test_confirm_password_fails_with_incorrect_password()
    {
        // Arrange: Membuat pengguna dan login
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);
        $this->actingAs($user);

        // Act: Kirim permintaan konfirmasi password dengan password salah
        $response = $this->post(route('password.confirm'), [
            'password' => 'wrong-password',
        ]);

        // Assert: Periksa bahwa ada error dalam sesi
        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
    }
}
