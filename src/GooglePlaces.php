<?php

namespace Wingly\GooglePlaces;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Repository;
use Wingly\GooglePlaces\Engines\AutocompleteEngine;
use Wingly\GooglePlaces\Engines\GeocodeEngine;

class GooglePlaces
{
    public static bool $registersRoutes = true;

    public static function autocomplete(string $query): Builder
    {
        return app(Builder::class, [
            'query' => $query,
            'engine' => app(AutocompleteEngine::class),
            'cache' => static::store(),
        ]);
    }

    public static function geocode(string $address): Builder
    {
        return app(Builder::class, [
            'query' => $address,
            'engine' => app(GeocodeEngine::class),
            'cache' => static::store(),
        ]);
    }

    public static function clearCache(): void
    {
        static::store()->clear();
    }

    public static function ignoreRoutes(): static
    {
        static::$registersRoutes = false;

        return new static();
    }

    protected static function store(): Repository
    {
        return Cache::store(config('google-places.cache_store'));
    }
}
