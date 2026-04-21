<?php

declare(strict_types=1);

namespace Domains\Product\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function find(int $id): ?Product;

    public function getAll(): iterable;
}
