# GooglePlaces

[![tests](https://github.com/Wingly-Company/google-places/actions/workflows/tests.yml/badge.svg)](https://github.com/Wingly-Company/google-places/actions/workflows/tests.yml)
[![code style](https://github.com/Wingly-Company/google-places/actions/workflows/code-style.yml/badge.svg)](https://github.com/Wingly-Company/google-places/actions/workflows/code-style.yml)

## Introduction 

This package provides a server side integration of the [Google autocomplete service](https://developers.google.com/maps/documentation/places/web-service/autocomplete) and the [Google geocoding service](https://developers.google.com/maps/documentation/geocoding/start). 
It takes care of caching the results for both autocompletion requests and geocoding.   

## Installation 

First make sure to configure the repository in your composer.json by running:

```bash
composer config repositories.google-places vcs https://github.com/Wingly-Company/google-places
```

Then install the package by running:

```bash
composer require wingly/google-places
```

## Configuration 

This is the content of the config file:

```php
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
```

## Usage 

Here's how you can use the Google autocomplete.

```php 
public function index(Request $request)
{
    $results = GooglePlaces::autocomplete($request->input('query'))->get();

    return response()->json($results);
}
```

Here's how you can use the Google geocoding.

```php 
public function index(Request $request)
{
    $results = GooglePlaces::geocode($request->input('query'))->get();

    return response()->json($results);
}
```

You can optionally configure your search parameters for both autocompletion and geocoding.   

### Language 
To get the results in a specific language: 

```php 
GooglePlace::autocomplete('par')->setLanguage('fr')->get();
GooglePlace::geocode('Paris, France')->setLanguage('fr')->get();
```

### Country 
To limit the results in a specific country: 

```php 
GooglePlace::autocomplete('par')->setCountry('FR')->get();
GooglePlace::geocode('Paris, France')->setCountry('FR')->get();
```

### Types 

To restrict results for specific types (https://developers.google.com/maps/documentation/places/web-service/supported_types#table3): 

```php 
GooglePlace::autocomplete('par')->setTypes('(cities)')->get();
```

This option is available only for autocompletion.
