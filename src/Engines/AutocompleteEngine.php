<?php

namespace Wingly\GooglePlaces\Engines;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Wingly\GooglePlaces\Builder;

class AutocompleteEngine implements Engine
{
    private string $endpoint = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';

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

        if (! count($response->predictions)) {
            return [];
        }

        return $this->formatResponse($response);
    }

    public function getHashedCacheKey(Builder $builder): string
    {
        $payload = $this->getRequestPayload($builder);

        $parts = array_values($payload['query']);

        $hash = Str::of(collect($parts)->implode('-'))
            ->pipe('md5')
            ->prepend('googleautocomplete-');

        return (string) $hash;
    }

    protected function formatResponse($response): array
    {
        $results = array_map(function ($result) {
            return [
                'name' => $result->description,
                'place_id' => $result->place_id,
                'reference' => $result->reference,
                'matched_substrings' => $result->matched_substrings,
            ];
        }, $response->predictions);

        return $results;
    }

    protected function getRequestPayload(Builder $builder): array
    {
        $parameters = [
            'key' => $this->apiKey,
            'input' => $builder->query,
            'language' => $builder->language,
            'types' => $builder->types,
        ];

        if ($builder->country) {
            $parameters = array_merge(
                $parameters,
                ['components' => 'country:'.$builder->country]
            );
        }

        return ['query' => array_filter($parameters)];
    }
}
