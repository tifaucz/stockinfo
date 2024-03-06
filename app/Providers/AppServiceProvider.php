<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Http\Livewire\StockTable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StockPriceService::class, function ($app) {
            return new StockPriceService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('stock-table', StockTable::class);
        //
    }
}
