<?php

namespace App\Providers;

use fk\reference\IdeReferenceServiceProvider;
use fk\utility\Auth\Session\SessionGuardServiceProvider;
use fk\utility\Database\Console\Migrations\MigrateMakeServiceProvider;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->environment('production')) {
            // There is a death loop inside if put it in method `register`
            $this->app->register(IdeReferenceServiceProvider::class);
        }
        $this->app->register(SessionServiceProvider::class);
        $this->app->register(SessionGuardServiceProvider::class);

        // Alias makes all Request::class uses a App::$instances['request'] as instance
        // Alias is used for checking in Container::make()
        $this->app->alias('request', Request::class);
    }
}
