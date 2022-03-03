<?php

namespace Wingly\GooglePlaces\Engines;

use Wingly\GooglePlaces\Builder;

interface Engine
{
    public function search(Builder $builder): mixed;

    public function getHashedCacheKey(Builder $builder): string;
}
