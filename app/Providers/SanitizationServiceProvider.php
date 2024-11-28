<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Services\SanitizationService;
use HTMLPurifier;
use HTMLPurifier_HTML5Config;

class SanitizationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HTMLPurifier::class, function (Application $app) {
            // Create the default configuration for HTML5 with the necessary directives
            $config = HTMLPurifier_HTML5Config::createDefault();

            // You can adjust additional configurations if needed
            // Example: Allow YouTube embeds in iframe tags
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^//www\.youtube\.com/embed/%');

            return new HTMLPurifier($config);
        });

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
