<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
Use Illuminate\Contracts\Foundation\Application;
use HTMLPurifier;
use HTMLPurifier_HTML5Config;

class HTMLPurifierServiceProvider extends ServiceProvider
{
    /**
     * Register the HTMLPurifier service.
     *
     * @return void
     */
    public function register()
    {
        // Bind HTMLPurifier as a singleton
        $this->app->singleton(HTMLPurifier::class, function (Application $app) {
            // Create the default configuration for HTMLPurifier
            $config = HTMLPurifier_HTML5Config::createDefault();

            // Additional configurations (e.g., allow YouTube embeds)
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^//www\.youtube\.com/embed/%');

            // Return an instance of HTMLPurifier
            return new HTMLPurifier($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Optionally: You could add any bootstrapping code here, if necessary.
    }
}
