<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_succeeds_with_valid_data()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'email' => 'user@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'token' => Password::createToken($user),
        ]);

        $response->assertRedirect('/login');
    }

    public function test_password_reset_fails_with_invalid_data()
    {
        $response = $this->post(route('password.update'), [
            'email' => 'user@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'wrongconfirmation',
            'token' => 'invalidtoken',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}
