<?php

use CRM\API\AlgoliaClass\AlgoliaClassController;

Route::group(['prefix' => 'algolia-class'], function () {
    Route::get('/', [AlgoliaClassController::class, 'index']);
    Route::get('/all', [AlgoliaClassController::class, 'all']);
    Route::post('/', [AlgoliaClassController::class, 'store']);
    Route::delete('/', [AlgoliaClassController::class, 'destroy']);
});
