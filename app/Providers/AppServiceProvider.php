<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

// Require the global helper file
require_once app_path('Helpers/helpers.php');

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        
    }

    public function boot(): void
    {
        // Simple and lightweight blade directives for Indonesian number formatting
        Blade::directive('formatNumber', function ($expression) {
            return "<?php echo formatIndonesianNumber($expression); ?>";
        });

        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . formatIndonesianNumber($expression); ?>";
        });
    }
}

