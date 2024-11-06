<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $order;
    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->order = Order::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_can_get_all_orders()
    {
        Order::factory()->count(1)->create();

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
    }

    public function test_can_get_single_order()
    {
        $response = $this->getJson('/api/orders/' . $this->order->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->order->id,
                    'user_id' => $this->order->user_id,
                    'total_amount' => $this->order->total_amount,
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_order()
    {
        $nonExistentOrderId = 99999;

        $response = $this->getJson('/api/orders/' . $nonExistentOrderId);

        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'Order not found'
            ]);
    }

    public function test_can_update_order()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/orders/' . $this->order->id, [
            'total_amount' => 15000
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Order updated successfully',
                'data' => [
                    'id' => $this->order->id,
                    'user_id' => $this->order->user_id,
                    'order_number' => $this->order->order_number,
                    'total_amount' => 15000
                ]
            ]);
    }

    public function test_update_fails_with_invalid_data()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/orders/' . $this->order->id, [
            'total_amount' => 'not-a-number'
        ]);

        $response->assertStatus(400)
            ->assertJsonValidationErrors(['total_amount']);
    }

    public function test_can_delete_order()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/orders/' . $this->order->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order deleted successfully'
            ]);

        $this->assertDatabaseMissing('orders', [
            'id' => $this->order->id
        ]);
    }

    public function test_delete_fails_for_non_existent_order()
    {
        $nonExistentOrderId = 99999;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/orders/' . $nonExistentOrderId);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Order not found'
            ]);
    }
}
