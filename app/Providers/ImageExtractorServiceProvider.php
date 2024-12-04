<?php

namespace App\Providers;

use App\Services\ImageExtractorService;
use App\Services\ImageStorageService;
use Illuminate\Support\ServiceProvider;

class ImageExtractorServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ImageExtractorService::class, function ($app) {
            return new ImageExtractorService($app->make(ImageStorageService::class));
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        // Additional setup if necessary.
    }
}

