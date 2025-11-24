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
        // Try to increase PHP limits as early as possible
        // Note: upload_max_filesize and post_max_size require php.ini changes
        @ini_set('memory_limit', '1024M');
        @ini_set('max_execution_time', '1800');
        @ini_set('max_input_time', '1800');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share currency globally with all views
        View::composer('*', function ($view) {
            try {
                $view->with('currentCurrency', CurrencyHelper::getDefault());
            } catch (\Exception $e) {
                // If currency helper fails (e.g., database connection issue), set to null
                // This prevents the app from crashing when database credentials are wrong
                $view->with('currentCurrency', null);
            }
        });
    }
}
