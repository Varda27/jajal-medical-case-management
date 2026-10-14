<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature Test Suite for Web and API User Authentication.
 * Tests:
 * 1. Valid login succeeds
 * 2. Invalid login is rejected
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Valid login succeeds and authenticates into web session.
     */
    public function test_valid_login_succeeds(): void
    {
        $user = User::factory()->create([
            'email' => 'admin.test@jajalmedical.com',
            'password' => Hash::make('Secret@12345'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin.test@jajalmedical.com',
            'password' => 'Secret@12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test 2: Invalid login is rejected with clear session errors.
     */
    public function test_invalid_login_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'admin.test@jajalmedical.com',
            'password' => Hash::make('Secret@12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'admin.test@jajalmedical.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test Sanctum API Login returns access token on valid credentials.
     */
    public function test_api_login_returns_sanctum_token(): void
    {
        $user = User::factory()->create([
            'email' => 'api.user@jajalmedical.com',
            'password' => Hash::make('ValidApiPass123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'api.user@jajalmedical.com',
            'password' => 'ValidApiPass123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Authenticated successfully.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);
    }

    /**
     * Test Sanctum API Login rejects invalid credentials with 401.
     */
    public function test_api_login_with_invalid_credentials_returns_401(): void
    {
        $user = User::factory()->create([
            'email' => 'api.user@jajalmedical.com',
            'password' => Hash::make('ValidApiPass123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'api.user@jajalmedical.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'data' => null,
            ]);
    }
}
