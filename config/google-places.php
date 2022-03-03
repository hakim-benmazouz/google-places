<?php

return [
    /*
     * The api key used when sending requests to Google.
     */
    'google_api_key' => env('GOOGLE_PLACES_API_KEY'),

    /*
     * Here you may define the cache store that should be used to store
     * requests. This can be the name of any store that is
     * configured in app/config/cache.php
     */
    'cache_store' => env('GOOGLE_PLACES_DRIVER', 'file'),

    /*
     * This setting controls the default number of seconds responses must be cached.
     */
    'cache_lifetime' => env('GOOGLE_PLACES_CACHE_LIFETIME', 60 * 60 * 24 * 7),
];
