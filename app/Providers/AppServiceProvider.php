<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Routing\Route;
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
        // Route model binding untuk model User menggunakan id_user
        // Route::model('user', User::class, function ($value) {
        //     return User::where('id_user', $value)->firstOrFail();
        // });
    }
}
