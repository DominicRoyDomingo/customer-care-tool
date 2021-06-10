<?php

use CRM\API\AlgoliaPermission\AlgoliaPermissionController;

Route::group(['prefix' => 'algolia-permission'], function () {
    Route::get('/', [AlgoliaPermissionController::class, 'index']);
    Route::get('/check-permission', [AlgoliaPermissionController::class, 'checkPermission']);

    Route::post('/', [AlgoliaPermissionController::class, 'store']);
    Route::delete('/', [AlgoliaPermissionController::class, 'destroy']);
});
