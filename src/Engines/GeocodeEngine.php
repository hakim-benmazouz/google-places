<?php

namespace Wingly\GooglePlaces\Engines;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Wingly\GooglePlaces\Builder;

class GeocodeEngine implements Engine
{
    private string $endpoint = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct(
        private string $apiKey,
        private Client $client
    ) {
    }

    public function search(Builder $builder): mixed
    {
        $payload = $this->getRequestPayload($builder);

        if (empty($builder->query)) {
            return [];
        }

        $response = $this->client->request('GET', $this->endpoint, $payload);

        $response = json_decode($response->getBody());

        if (! empty($response->error_message)) {
            return [];
        }

        if (! count($response->results)) {
            return [];
        }

        $results = $this->formatResponse($response);

        return $results[0];
    }

    protected function formatResponse($response): array
    {
        $results = array_map(function ($result) {
            return [
                'lat' => $result->geometry->location->lat,
                'lng' => $result->geometry->location->lng,
                'accuracy' => $result->geometry->location_type,
                'formatted_address' => $result->formatted_address,
                'viewport' => $result->geometry->viewport,
                'address_components' => $result->address_components,
                'place_id' => $result->place_id,
            ];
        }, $response->results);

        return $results;
    }

    protected function getRequestPayload(Builder $builder): array
    {
        $parameters = [
            'key' => $this->apiKey,
            'address' => $builder->query,
            'language' => $builder->language,
        ];

        if ($builder->country) {
            $parameters = array_merge(
                $parameters,
                ['components' => 'country:'.$builder->country]
            );
        }

        return ['query' => array_filter($parameters)];
    }

    public function getHashedCacheKey(Builder $builder): string
    {
        $payload = $this->getRequestPayload($builder);

        $parts = array_values($payload['query']);

        $hash = Str::of(collect($parts)->implode('-'))
            ->pipe('md5')
            ->prepend('googlegeocode-');

        return (string) $hash;
    }
}
