<?php

declare(strict_types=1);

namespace Domains\Order\Services;

use App\Jobs\ProcessOrderShipment;
use App\Models\OrderItem;
use Domains\Order\Repositories\OrderRepositoryInterface;
use Domains\Product\Repositories\ProductRepositoryInterface;
use Domains\Customer\Repositories\CustomerRepositoryInterface;
use Domains\Order\DTO\OrderDTO;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private ProductRepositoryInterface $products,
        private CustomerRepositoryInterface $customers,
        private OrderStateMachine $stateMachine
    ) {
        //
    }

    public function create(array $data): OrderDTO
    {
        return DB::transaction(function () use ($data) {

            $customer = $this->customers->find($data['customer_id']);

            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            $order = $this->orders->create([
                'customer_id' => $customer->id,
                'status' => 'new',
                'total_amount' => 0
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $product = $this->products->find($item['product_id']);

                if (!$product) {
                    throw new \DomainException('Product not found');
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \DomainException('Not enough stock');
                }

                $lineTotal = $product->price * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $lineTotal
                ]);

                $total += $lineTotal;

                // уменьшаем остаток
                $product->decrement('stock_quantity', $item['quantity']);
            }

            $this->orders->update($order, [
                'total_amount' => $total
            ]);

            Cache::forget("orders.$order->id");

            return OrderDTO::fromModel(
                $this->orders->find($order->id)
            );
        });
    }

    public function confirm(int $orderId): OrderDTO
    {
        $dto = $this->changeStatus($orderId, 'confirmed', function ($order) {
            $order->confirmed_at = now();
        });

        // отправляем в очередь
        ProcessOrderShipment::dispatch($dto->id);

        return $dto;
    }

    public function cancel(int $orderId): OrderDTO
    {
        return $this->changeStatus($orderId, 'cancelled');
    }

    private function changeStatus(int $orderId, string $newStatus, callable $callback = null): OrderDTO
    {
        return DB::transaction(function () use ($orderId, $newStatus, $callback) {

            $order = $this->orders->find($orderId);

            if (!$order) {
                throw new \Exception('Order not found');
            }

            $this->stateMachine->assertTransition(
                $order->status,
                $newStatus
            );

            if ($callback) {
                $callback($order);
            }

            $this->orders->update($order, [
                'status' => $newStatus,
                'confirmed_at' => $order->confirmed_at ?? null,
                'shipped_at' => $order->shipped_at ?? null,
            ]);

            Cache::forget("orders.$order->id");

            return OrderDTO::fromModel(
                $this->orders->find($order->id)
            );
        });
    }
}
