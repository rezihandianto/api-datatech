<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->json('POST', '/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'age' => 1,
        ]);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at'
                ]
            ]);
    }

    public function test_register_fails_with_invalid_data()
    {
        // Test dengan email yang sudah ada
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response = $this->json('POST', '/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',

        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['email']);

        // Test dengan password yang tidak sesuai
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',

        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['password']);

        // Test dengan data yang kosong
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'age']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),

        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'token'
            ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        // Test dengan email yang tidak terdaftar
        $response = $this->json('POST', '/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid email or password.'
            ]);

        // Test dengan password yang salah
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid email or password.'
            ]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create([]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User logged out successfully'
            ]);
    }
}
