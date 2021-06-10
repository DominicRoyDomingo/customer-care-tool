<?php

use CRM\API\City\CityController;

Route::group(['prefix' => 'city'], function () {
    Route::get('/', [CityController::class, 'index']);
    Route::get('/all', [CityController::class, 'all']);
});
