<?php

namespace App\Providers;

use App\Services\StockService;
use Illuminate\Support\ServiceProvider;

class StockServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(StockService::class, function ($app) {
            return new StockService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
