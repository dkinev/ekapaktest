<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Customer;

class OrderApiTest extends TestCase
{
    public function test_create_order_success(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'price' => 100,
            'stock_quantity' => 10
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson(['data' =>
                [
                    'status' => 'new',
                    'total_amount' => 200,
                ],
            ]);
    }

    public function test_create_order_validation_error(): void
    {
        $response = $this->postJson('/api/v1/orders', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'error',
                'message',
                'details'
            ]);
    }

    public function test_create_order_not_enough_stock(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'stock_quantity' => 1
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5
                ]
            ]
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'domain_error'
            ]);
    }

    public function test_rate_limit_orders(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/v1/orders', [
                'customer_id' => $customer->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1]
                ]
            ]);
        }

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);

        $response->assertStatus(429);
    }

    public function test_order_confirm_success(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);

        $orderId = $response->json('data.id');

        $response = $this->patchJson("/api/v1/orders/{$orderId}/confirm");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'confirmed'
                ]
            ]);
    }

    public function test_invalid_status_transition(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $order = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ])->json();

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);

        $orderId = $response->json('data.id');

        // сначала подтверждаем
        $this->patchJson("/api/v1/orders/{$orderId}/confirm");

        // потом пытаемся отменить (нельзя)
        $response = $this->patchJson("/api/v1/orders/{$orderId}/cancel");

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'domain_error'
            ]);
    }

    public function test_get_order(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);

        $orderId = $response->json('data.id');

        $response = $this->getJson("/api/v1/orders/{$orderId}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'status',
                    'total_amount',
                    'items'
                ]
            ]);
    }
}
