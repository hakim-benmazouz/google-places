<?php

namespace Wingly\GooglePlaces;

use Illuminate\Contracts\Cache\Repository;
use Wingly\GooglePlaces\Engines\Engine;

class Builder
{
    /** @var string */
    public $language;

    /** @var string */
    public $types;

    /** @var string */
    public $fields;

    /** @var string */
    public $country;

    /** @var string */
    public $query;

    /** @var \Wingly\GooglePlaces\Engines\Engine */
    public $engine;

    /** @var \Illuminate\Contracts\Cache\Repository */
    public $cache;

    public function __construct(string $query, Engine $engine, Repository $cache)
    {
        $this->query = $query;

        $this->engine = $engine;

        $this->cache = $cache;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function setTypes(string $types): self
    {
        $this->types = $types;

        return $this;
    }

    public function setFields(string $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function get(): mixed
    {
        $cacheKey = $this->engine->getHashedCacheKey($this);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $results = $this->engine->search($this);

        if (! count($results)) {
            return [];
        }

        return $this->cacheResponse($cacheKey, $results);
    }

    public function cacheResponse(string $key, $results): array
    {
        $this->cache->put($key, $results, config('google-places.cache_lifetime'));

        return $results;
    }
}
