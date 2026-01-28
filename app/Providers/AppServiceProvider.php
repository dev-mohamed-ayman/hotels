<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrap();

        // Custom directive to format numbers without trailing zeros
        \Blade::directive('formatNumber', function ($expression) {
            return "<?php echo preg_replace('/\.0+$|\.00+$/', '', number_format($expression, 2)); ?>";
        });
    }
}
