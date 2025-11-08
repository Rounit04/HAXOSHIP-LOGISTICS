<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Currency;
use App\Helpers\CurrencyHelper;

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
        // Share currency globally with all views
        View::composer('*', function ($view) {
            $view->with('currentCurrency', CurrencyHelper::getDefault());
        });
    }
}
