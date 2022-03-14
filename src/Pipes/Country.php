<?php

namespace Wingly\GooglePlaces\Pipes;

use Wingly\GooglePlaces\Builder;

class Country
{
    public function handle(Builder $builder, $next): mixed
    {
        if (request()->has('country')) {
            $builder->setCountry(request('country', ''));
        }

        return $next($builder);
    }
}
