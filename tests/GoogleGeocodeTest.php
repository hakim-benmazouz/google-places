<?php

namespace Wingly\GooglePlaces\Tests;

use Wingly\GooglePlaces\GooglePlaces;

class GoogleGeocodeTest extends TestCase
{
    public function test_it_can_geocode_a_city(): void
    {
        $result = GooglePlaces::geocode('Antwerp')->get();

        $this->assertArrayHasKey('lat', $result);
        $this->assertArrayHasKey('lng', $result);
        $this->assertArrayHasKey('accuracy', $result);
        $this->assertArrayHasKey('formatted_address', $result);
        $this->assertArrayHasKey('viewport', $result);
    }

    public function test_it_should_return_an_empty_response_when_called_with_empty_query(): void
    {
        $this->assertEquals([], GooglePlaces::geocode('')->get());
    }

    public function test_it_should_return_an_empty_response_when_using_a_non_existing_city(): void
    {
        $this->assertEquals([], GooglePlaces::geocode('ForceMeToFail')->get());
    }

    public function test_it_can_translate_the_data(): void
    {
        $result = GooglePlaces::geocode('Roma, Italy')->get();

        $this->assertEquals('Rome, Metropolitan City of Rome, Italy', $result['formatted_address']);

        $result = GooglePlaces::geocode('Roma, Italy')
            ->setLanguage('it')
            ->get();

        $this->assertEquals('Roma RM, Italia', $result['formatted_address']);
    }

    public function test_it_can_include_the_address_components_in_a_response(): void
    {
        $result = GooglePlaces::geocode('Infinite Loop 1, Cupertino')->get();

        $this->assertArrayHasKey('address_components', $result);
    }

    public function test_it_includes_the_place_id_in_a_response(): void
    {
        $result = GooglePlaces::geocode('Infinite Loop 1, Cupertino')->get();

        $this->assertArrayHasKey('place_id', $result);
    }

    public function test_it_will_cache_a_request(): void
    {
        $results = GooglePlaces::geocode('Infinite Loop 1, Cupertino')->get();

        $cachedResults = $this->app['cache']
            ->store(config('google-places.cache_store'))
            ->get('googlegeocode-'.md5(config('google-places.google_api_key').'-Infinite Loop 1, Cupertino'));

        $this->assertEquals($results, $cachedResults);
    }
}
