# GooglePlaces

[![tests](https://github.com/Wingly-Company/google-places/actions/workflows/tests.yml/badge.svg)](https://github.com/Wingly-Company/google-places/actions/workflows/tests.yml)
[![code style](https://github.com/Wingly-Company/google-places/actions/workflows/code-style.yml/badge.svg)](https://github.com/Wingly-Company/google-places/actions/workflows/code-style.yml)

## Introduction 

This package provides a server side integration of Google Places services.
The following place requests are available: 
- [Autocomplete](https://developers.google.com/maps/documentation/places/web-service/autocomplete) 
- [Geocode](https://developers.google.com/maps/documentation/geocoding/start) 
- [Details](https://developers.google.com/maps/documentation/places/web-service/details)

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

    /**
     * This is the base URI path where Google places routes will be available from.
     */
    'path' => env('GOOGLE_PLACES_PATH', 'addresses'),
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

Here's how you can use the Google place details.

```php 
public function index(Request $request)
{
    $results = GooglePlaces::details($request->input('place_id'))->get();

    return response()->json($results);
}
```

You can optionally configure your search parameters. 

### Language 
To get the results in a specific language: 

```php 
GooglePlace::autocomplete('par')->setLanguage('fr')->get();
GooglePlace::geocode('Paris, France')->setLanguage('fr')->get();
GooglePlace::details('ChIJN1t_tDeuEmsRUsoyG83frY4')->setLanguage('fr')->get();
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

### Fields 
To specify a list of place data types to be included in the response (https://developers.google.com/maps/documentation/places/web-service/details#fields): 

```php 
$result = GooglePlaces::details('ChIJN1t_tDeuEmsRUsoyG83frY4')
    ->setFields('website,opening_hours')
    ->get();
```

This option is available only for place details.

## Addresses autocompletion and geocoding routes

The package exposes two routes that you can use for addresses autocompletion and geocoding under the following URL paths `/addresses/autocomplete`, `/addresses/geocode`. 

You can streamline your search by adding the following query params to your url: 
- `input`: The user entered input for the autocomplete or a full address for geocoding. 
- `language`: To get the results in a specific language. Defaults to the application current locale. 
- `country`: To get the results for a specific country. 
- `types`: To get the results for a specific type. Available only for autocomplete requests.

If you like to prevent the publishing of those routes completely, you can use the `ignoreRoutes` method provided by GooglePlaces.  
Typically this method should be called in the register method of your `AppServiceProvider`: 

```php 
use Wingly\GooglePlaces\GooglePlaces;
 
/**
 * Register any application services.
 *
 * @return void
 */
public function register()
{
    GooglePlaces::ignoreRoutes();
}
```

