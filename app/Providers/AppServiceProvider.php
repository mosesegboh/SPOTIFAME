<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
            /*
            error_reporting(E_ALL);
            ini_set('error_reporting', E_ALL);
            ini_set("display_errors", 1);
            */
            ini_set('error_reporting', E_ALL & ~E_NOTICE);
    }
}
