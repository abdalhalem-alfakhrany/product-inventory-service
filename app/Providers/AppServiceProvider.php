<?php

namespace App\Providers;

use App\Service\ProductService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use \App\Repositories\ProductRepository;
use \App\Repositories\ProductRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductService::class, fn(Application $app) => new ProductService($app->make(ProductRepositoryInterface::class)));
    }
}
