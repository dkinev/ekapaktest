<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Domains\Product\Repositories\ProductRepositoryInterface;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepositoryInterface $products
    ) {
        //
    }

    public function index()
    {
        return ProductResource::collection(
            $this->products->getAll()
        );
    }

    public function show(int $id)
    {
        $product = $this->products->find($id);

        if (!$product) {
            abort(404, 'Product not found');
        }

        return new ProductResource($product);
    }
}
