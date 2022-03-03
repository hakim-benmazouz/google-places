<?php

namespace Wingly\GooglePlaces\Tests;

use Wingly\GooglePlaces\GooglePlaces;

class GoogleAutocompleteTest extends TestCase
{
    public function test_it_can_get_predictions_for_an_input(): void
    {
        $results = GooglePlaces::autocomplete('par')->get();

        $this->assertIsArray($results);
        $this->assertIsArray($results[0]);

        $result = $results[0];

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('place_id', $result);
        $this->assertArrayHasKey('reference', $result);
    }

    public function test_it_should_return_an_empty_response_when_called_with_empty_query()
    {
        $this->assertEquals([], GooglePlaces::autocomplete('')->get());
    }

    public function test_it_can_translate_the_data(): void
    {
        $results = GooglePlaces::autocomplete('paris')->get();

        $this->assertEquals('Paris, France', $results[0]['name']);

        $results = GooglePlaces::autocomplete('paris')
            ->setLanguage('it')
            ->get();

        $this->assertEquals('Paris, Francia', $results[0]['name']);
    }

    public function test_it_will_cache_a_request(): void
    {
        $results = GooglePlaces::autocomplete('paris')->get();

        $cachedResults = $this->app['cache']
            ->store(config('google-places.cache_store'))
            ->get('googleautocomplete-'.md5(config('google-places.google_api_key').'-paris'));

        $this->assertEquals($results, $cachedResults);
    }
}
