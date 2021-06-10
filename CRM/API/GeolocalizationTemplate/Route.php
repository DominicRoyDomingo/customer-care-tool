<?php

use CRM\API\GeolocalizationTemplate\GeolocalizationTemplateController;

Route::group(['prefix' => 'geolocalization_template'], function () {

    Route::get('/', [GeolocalizationTemplateController::class, 'index']);
    // Route::get('/{id}', [GeolocalizationTemplateController::class, 'show']);
    Route::get('/template', [GeolocalizationTemplateController::class, 'getTemplate']);
    
    Route::get('/get-geolocalization-name', [GeolocalizationTemplateController::class, 'getGeolocalizationName']);
    // Route::get('/{id}', [ServiceController::class, 'show']);

   // for catgory
    Route::post('/', [GeolocalizationTemplateController::class, 'store']);
    Route::delete('/', [GeolocalizationTemplateController::class, 'destroy']);
});
