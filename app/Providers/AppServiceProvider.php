<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // Set timezone ke Indonesia (WIB)
        config(['app.timezone' => 'Asia/Jakarta']);
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
    }
}
