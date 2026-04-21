<?php

declare(strict_types=1);

namespace Domains\Order\Services;

use Domains\Order\Enums\OrderStatus;

class OrderStateMachine
{
    private array $transitions = [
        'new' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing'],
        'processing' => ['shipped'],
        'shipped' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function canTransition(string $from, string $to): bool
    {
        return in_array($to, $this->transitions[$from] ?? [], true);
    }

    public function assertTransition(string $from, string $to): void
    {
        if (!$this->canTransition($from, $to)) {
            throw new \DomainException("Invalid status transition: $from → $to");
        }
    }
}
