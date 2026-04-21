<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $price,
            'total_price' => $quantity * $price
        ];
    }
}
