<?php

declare(strict_types=1);

namespace Domains\Order\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderRepository implements OrderRepositoryInterface
{
    public function find(int $id): ?Order
    {
        return Cache::remember(
            "orders.$id",
            now()->addMinutes(5),
            fn() => Order::with('customer', 'items')->find($id)
        );
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order;
    }
}
