<?php

namespace Wingly\GooglePlaces\Tests;

class AutocompleteControllerTest extends TestCase
{
    public function test_it_returns_predictions_for_an_input()
    {
        $response = $this->getJson('/autocomplete?input=paris');

        $response->assertOk();
        $this->assertIsArray($response->json());

        $prediction = $response->json()[0];

        $this->assertArrayHasKey('name', $prediction);
    }

    public function test_it_returns_empty_array_for_an_empty_input()
    {
        $response = $this->getJson('/autocomplete?input=');

        $response->assertOk();

        $this->assertEquals([], $response->json());
    }

    public function test_it_returns_empty_array_when_called_without_input()
    {
        $response = $this->getJson('/autocomplete');

        $response->assertOk();

        $this->assertEquals([], $response->json());
    }
}
