<?php

namespace Kodooy\StringGenerator;

use Illuminate\Support\ServiceProvider;

class StringGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/string-generator.php',
            'string-generator'
        );

        $this->app->singleton('string-generator', function ($app) {
            return new StringGenerator();
        });

        $this->app->alias('string-generator', StringGenerator::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/string-generator.php' => config_path('string-generator.php'),
            ], 'string-generator-config');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['string-generator', StringGenerator::class];
    }
}
