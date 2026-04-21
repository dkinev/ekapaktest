<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Domains\Product\Repositories\ProductRepository;
use Domains\Product\Repositories\ProductRepositoryInterface;
use Domains\Order\Repositories\OrderRepository;
use Domains\Order\Repositories\OrderRepositoryInterface;
use Domains\Customer\Repositories\CustomerRepository;
use Domains\Customer\Repositories\CustomerRepositoryInterface;
use Domains\Order\Services\OrderStateMachine;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->singleton(OrderStateMachine::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('orders', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
