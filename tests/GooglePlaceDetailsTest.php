<?php

namespace Wingly\GooglePlaces\Tests;

use Wingly\GooglePlaces\GooglePlaces;

class GooglePlaceDetailsTest extends TestCase
{
    public function test_it_can_get_the_place_details_from_an_id(): void
    {
        $result = GooglePlaces::details('ChIJN1t_tDeuEmsRUsoyG83frY4')
            ->setFields('formatted_address')
            ->get();

        $this->assertArrayHasKey('formatted_address', $result);
    }

    public function test_it_should_return_an_empty_response_when_called_with_empty_query(): void
    {
        $this->assertEquals([], GooglePlaces::details('')->get());
    }

    public function test_it_can_translate_the_data(): void
    {
        $result = GooglePlaces::details('ChIJu46S-ZZhLxMROG5lkwZ3D7k')
            ->setFields('formatted_address')
            ->get();

        $this->assertEquals('Rome, Metropolitan City of Rome, Italy', $result['formatted_address']);

        $result = GooglePlaces::details('ChIJu46S-ZZhLxMROG5lkwZ3D7k')
            ->setFields('formatted_address')
            ->setLanguage('it')
            ->get();

        $this->assertEquals('Roma RM, Italia', $result['formatted_address']);
    }

    public function test_it_can_specify_the_fields_to_be_included_in_the_response(): void
    {
        $result = GooglePlaces::details('ChIJN1t_tDeuEmsRUsoyG83frY4')
            ->setFields('website,opening_hours')
            ->get();

        $this->assertArrayHasKey('website', $result);
        $this->assertArrayHasKey('opening_hours', $result);
    }

    public function test_it_should_return_an_empty_response_when_using_a_non_existing_id(): void
    {
        $this->assertEquals([], GooglePlaces::details('ForceMeToFail')->get());
    }

    public function test_it_will_cache_a_request(): void
    {
        $result = GooglePlaces::details('ChIJu46S-ZZhLxMROG5lkwZ3D7k')
            ->get();

        $cachedResult = $this->app['cache']
            ->store(config('google-places.cache_store'))
            ->get('googledetails-'.md5(config('google-places.google_api_key').'-ChIJu46S-ZZhLxMROG5lkwZ3D7k'));

        $this->assertEquals($result, $cachedResult);
    }
}
