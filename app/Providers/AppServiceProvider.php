<?php

namespace App\Providers;

use App\Models\Dapur;
use App\Observers\DapurObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\View\Composers\KepalaDapurComposer;
use App\View\Composers\AdminGudangComposer;

require_once app_path('Helpers/helpers.php');

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Dapur::observe(DapurObserver::class);

        Blade::directive('formatNumber', function ($expression) {
            return "<?php echo formatIndonesianNumber($expression); ?>";
        });

        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . formatIndonesianNumber($expression); ?>";
        });

        View::composer('template_kepala_dapur.sidebar', KepalaDapurComposer::class);
        View::composer('template_admin_gudang.sidebar', AdminGudangComposer::class);
    }
}
