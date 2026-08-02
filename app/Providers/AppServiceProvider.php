<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\WalletTransaction;
use App\Observers\WalletTransactionObserver;

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
        WalletTransaction::observe(WalletTransactionObserver::class);

        Paginator::useBootstrap();

        // Custom directive to format numbers without trailing zeros.
        // Decimal places come from config/numbers.php; an explicit count can be
        // passed as a second argument: @formatNumber($value, 2)
        Blade::directive('formatNumber', function ($expression) {
            return "<?php echo \App\Helpers\NumberHelper::format($expression); ?>";
        });
    }
}
