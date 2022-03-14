<?php

namespace Wingly\GooglePlaces\Engines;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Wingly\GooglePlaces\Builder;

class DetailsEngine implements Engine
{
    private string $endpoint = 'https://maps.googleapis.com/maps/api/place/details/json';

    public function __construct(
        private string $apiKey,
        private Client $client
    ) {
    }

    public function search(Builder $builder): mixed
    {
        $payload = $this->getRequestPayload($builder);

        $response = $this->client->request('GET', $this->endpoint, $payload);

        $response = json_decode($response->getBody());

        return match ($response->status) {
            'ZERO_RESULTS', 'INVALID_REQUEST', 'OVER_QUERY_LIMIT', 'REQUEST_DENIED', 'UNKNOWN_ERROR' => [],
            default => $this->formatResponse($response->result),
        };
    }

    protected function formatResponse($result): array
    {
        $formatted = [];

        if (isset($result->geometry)) {
            $formatted['lat'] = $result->geometry->location->lat;
            $formatted['lng'] = $result->geometry->location->lng;
        }

        if (isset($result->formatted_address)) {
            $formatted['formatted_address'] = $result->formatted_address;
        }

        if (isset($result->formatted_phone_number)) {
            $formatted['formatted_phone_number'] = $result->formatted_phone_number;
        }

        if (isset($result->website)) {
            $formatted['website'] = $result->website;
        }

        if (isset($result->opening_hours)) {
            $formatted['opening_hours'] = array_map(function ($period) {
                return [
                    'open' => get_object_vars($period->open),
                    'close' => get_object_vars($period->close),
                ];
            }, $result->opening_hours->periods);
        }

        return $formatted;
    }

    protected function getRequestPayload(Builder $builder): array
    {
        $parameters = [
            'key' => $this->apiKey,
            'place_id' => $builder->query,
            'language' => $builder->language,
            'fields' => $builder->fields,
        ];

        return ['query' => array_filter($parameters)];
    }

    public function getHashedCacheKey(Builder $builder): string
    {
        $payload = $this->getRequestPayload($builder);

        $parts = array_values($payload['query']);

        $hash = Str::of(collect($parts)->implode('-'))
            ->pipe('md5')
            ->prepend('googledetails-');

        return (string) $hash;
    }
}
