<?php

declare(strict_types=1);

namespace Domains\Order\DTO;

class OrderDTO
{
    public function __construct(
        public int $id,
        public string $status,
        public float $totalAmount,
        public array $items
    ) {
        //
    }

    public static function fromModel($order): self
    {
        return new self(
            id: $order->id,
            status: $order->status,
            totalAmount: $order->total_amount,
            items: $order->items->map(fn($i) => [
                'product_id' => $i->product_id,
                'quantity' => $i->quantity,
                'price' => $i->unit_price,
            ])->toArray()
        );
    }
}
