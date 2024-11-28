<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Services\SanitizationService;
use HTMLPurifier;

class SanitizationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind the SanitizationService to the container
        $this->app->singleton(SanitizationService::class, function (Application $app) {
            return new SanitizationService($app->make(HTMLPurifier::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Any bootstrapping code if needed, such as publishing config files or migrations.
    }
}
