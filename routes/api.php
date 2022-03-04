<?php

use Illuminate\Support\Facades\Route;
use Wingly\GooglePlaces\Http\Controllers\AutocompleteController;
use Wingly\GooglePlaces\Http\Controllers\GeocodeController;

Route::get('autocomplete', AutocompleteController::class)->name('autocomplete');
Route::get('geocode', GeocodeController::class)->name('geocode');
