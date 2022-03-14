<?php

namespace Wingly\GooglePlaces\Pipes;

use Wingly\GooglePlaces\Builder;

class Language
{
    public function handle(Builder $builder, $next): mixed
    {
        if (request()->has('language')) {
            $builder->setLanguage(request('language', ''));
        }

        return $next($builder);
    }
}
