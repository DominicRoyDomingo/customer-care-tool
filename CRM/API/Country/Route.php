<?php

use CRM\API\Country\CountryController;

Route::group(['prefix' => 'country'], function () {
    Route::get('/', [CountryController::class, 'index']);
    Route::get('/places', [CountryController::class, 'getPlaces']);
    Route::get('/cities', [CountryController::class, 'getCities']);
    Route::get('/provinces', [CountryController::class, 'getProvinces']);
    Route::get('/regions', [CountryController::class, 'getRegions']);
});
