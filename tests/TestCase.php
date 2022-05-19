<?php

namespace Wingly\GooglePlaces\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Wingly\GooglePlaces\GooglePlaces;
use Wingly\GooglePlaces\GooglePlacesServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        GooglePlaces::clearCache();
    }

    protected function getPackageProviders($app)
    {
        return [GooglePlacesServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('google-places.path', getenv('GOOGLE_PLACES_PATH', 'addresses'));
    }
}
