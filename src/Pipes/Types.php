<?php

namespace Wingly\GooglePlaces\Pipes;

use Wingly\GooglePlaces\Builder;

class Types
{
    public function handle(Builder $builder, $next): mixed
    {
        if (request()->has('types')) {
            $builder->setTypes(request('types', ''));
        }

        return $next($builder);
    }
}
