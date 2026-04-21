<?php

declare(strict_types=1);

namespace Domains\Product\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    public function find(int $id): ?Product
    {
        return Cache::remember(
            "products.$id",
            now()->addMinutes(10),
            fn() => Product::find($id)
        );
    }

    public function getAll(): iterable
    {
        return Cache::remember(
            'products.all',
            now()->addMinutes(10),
            fn() => Product::all()
        );
    }
}
