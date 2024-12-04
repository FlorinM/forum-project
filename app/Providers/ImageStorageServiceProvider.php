<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ImageStorageService;

class ImageStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     *
     * @return void
     */
    public function register()
    {
        // Bind the ImageStorageService into the service container
        $this->app->singleton(ImageStorageService::class, function ($app) {
            return new ImageStorageService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
