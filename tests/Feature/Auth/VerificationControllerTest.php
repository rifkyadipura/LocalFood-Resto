<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cannot_access_verification_routes()
    {
        $response = $this->get(route('verification.notice'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_users_can_see_verification_notice()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertStatus(200)
            ->assertViewIs('auth.verify');
    }
}
