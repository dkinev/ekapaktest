<?php

declare(strict_types=1);

namespace Domains\Order\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function find(int $id): ?Order;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;
}
