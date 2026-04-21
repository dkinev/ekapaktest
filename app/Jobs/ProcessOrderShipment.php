<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrderShipment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(): void
    {
        $order = Order::find($this->orderId);

        if (!$order) {
            Log::error("Order not found: {$this->orderId}");
            return;
        }

        // имитация обработки
        sleep(2);

        $order->update([
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);
    }
}
