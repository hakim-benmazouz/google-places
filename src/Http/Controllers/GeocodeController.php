<?php

namespace Wingly\GooglePlaces\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Controller;
use Wingly\GooglePlaces\GooglePlaces;
use Wingly\GooglePlaces\Pipes\Country;
use Wingly\GooglePlaces\Pipes\Language;

class GeocodeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $results = app(Pipeline::class)
            ->send(GooglePlaces::geocode($request->query('input', '')))
            ->through([Language::class, Country::class])
            ->thenReturn()
            ->get();

        return response()->json($results);
    }
}
