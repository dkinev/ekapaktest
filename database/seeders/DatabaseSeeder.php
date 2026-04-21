<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // продукты
        $products = Product::factory()->count(20)->create();

        // клиенты
        $customers = Customer::factory()->count(10)->create();

        // заказы
        foreach ($customers as $customer) {
            $orders = Order::factory()
                ->count(rand(1, 3))
                ->create(['customer_id' => $customer->id]);

            foreach ($orders as $order) {
                $items = OrderItem::factory()
                    ->count(rand(1, 5))
                    ->make([
                        'order_id' => $order->id,
                        'product_id' => $products->random()->id
                    ]);

                $total = 0;

                foreach ($items as $item) {
                    $item->save();
                    $total += $item->total_price;
                }

                $order->update([
                    'total_amount' => $total
                ]);
            }
        }
    }
}
