<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Domains\Order\DTO\OrderDTO;
use Domains\Order\Services\OrderService;
use Domains\Order\Repositories\OrderRepositoryInterface;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $service,
        private OrderRepositoryInterface $orders
    ) {
        //
    }

    public function store(CreateOrderRequest $request)
    {
        $dto = $this->service->create(
            $request->validated()
        );

        return (new OrderResource($dto))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id)
    {
        // Уходим от проблемы N+1 в репозитории
        $order = $this->orders->find($id);

        if (!$order) {
            abort(404, 'Order not found');
        }

        return new OrderResource(
            OrderDTO::fromModel($order)
        );
    }

    public function confirm(int $id)
    {
        $dto = $this->service->confirm($id);

        return new OrderResource($dto);
    }

    public function cancel(int $id)
    {
        $dto = $this->service->cancel($id);

        return new OrderResource($dto);
    }
}
