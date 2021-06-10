<?php

use CRM\API\Geolocalization\GeolocalizationController;

Route::group(['prefix' => 'geolocalization'], function () {

    Route::get('/cities', [GeolocalizationController::class, 'getCities']);
    Route::get('/provinces', [GeolocalizationController::class, 'getProvinces']);
    Route::get('/regions', [GeolocalizationController::class, 'getRegions']);
    Route::get('/', [GeolocalizationController::class, 'show']);
    Route::get('/algolia-summary', [GeolocalizationController::class, 'getAlgoliaSummary']);
    Route::get('/get-geolocalization-name', [GeolocalizationController::class, 'getGeolocalizationName']);
    Route::post('/change-publish-status', [GeolocalizationController::class, 'changePublishStatus']);
    Route::post('/sync-to-algolia', [GeolocalizationController::class, 'syncToAlgolia']);
    Route::post('/', [GeolocalizationController::class, 'store']);
    Route::post('/create-images', [GeolocalizationController::class, 'storeImage']);
    Route::delete('/', [GeolocalizationController::class, 'destroy']);
    Route::delete('/delete-image', [GeolocalizationController::class, 'destroyImage']);
});
