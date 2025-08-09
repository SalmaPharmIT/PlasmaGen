<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViteOverrideServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Override @vite directive to use our helper function
        Blade::directive('vite', function ($expression) {
            return "<?php echo vite_assets($expression); ?>";
        });
    }
}
