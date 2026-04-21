<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;

Route::prefix('v1')->group(function () {

    // PRODUCTS
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // CUSTOMERS
    Route::post('/customers', [CustomerController::class, 'store']);

    // ORDERS
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    Route::post('/orders', [OrderController::class, 'store'])
        ->middleware('throttle:orders');

    Route::patch('/orders/{id}/confirm', [OrderController::class, 'confirm']);
    Route::patch('/orders/{id}/cancel', [OrderController::class, 'cancel']);
});
