<?php

namespace Wingly\GooglePlaces;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Wingly\GooglePlaces\Engines\AutocompleteEngine;
use Wingly\GooglePlaces\Engines\GeocodeEngine;

class GooglePlacesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerPublishing();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/google-places.php', 'google-places');

        $this->app->bind(AutocompleteEngine::class, function ($app) {
            $client = app(Client::class);

            return new AutocompleteEngine(
                config('google-places.google_api_key'),
                $client,
            );
        });

        $this->app->bind(GeocodeEngine::class, function ($app) {
            $client = app(Client::class);

            return new GeocodeEngine(
                config('google-places.google_api_key'),
                $client,
            );
        });
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/google-places.php' => $this->app->configPath('google-places.php'),
            ], 'google-places-config');
        }
    }

    protected function registerRoutes(): void
    {
        if (GooglePlaces::$registersRoutes) {
            Route::group([
                'prefix' => config('google-places.path'),
                'as' => 'google.',
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            });
        }
    }
}
