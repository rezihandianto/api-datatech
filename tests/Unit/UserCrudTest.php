<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;
    protected $token;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_can_get_all_users()
    {
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
    }

    public function test_can_get_single_user()
    {
        $response = $this->getJson('/api/users/' . $this->user->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_user()
    {
        $nonExistentUserId = 99999;

        $response = $this->getJson('/api/users/' . $nonExistentUserId);

        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'User not found'
            ]);
    }

    public function test_can_update_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/users/' . $this->user->id, [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User updated successfully',
                'user' => [
                    'name' => 'Updated Name',
                    'email' => 'updated@example.com'
                ]
            ]);
    }

    public function test_update_fails_with_invalid_data()
    {
        // Test dengan email yang sudah ada
        $otherUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/users/' . $this->user->id, [
            'email' => 'existing@example.com'
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['email']);

        // Test dengan data yang tidak valid
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/users/' . $this->user->id, [
            'email' => 'not-an-email'
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_can_delete_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/users/' . $this->user->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User deleted successfully'
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id
        ]);
    }

    public function test_delete_fails_for_non_existent_user()
    {
        $nonExistentUserId = 99999;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/users/' . $nonExistentUserId);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'User not found'
            ]);
    }
}
