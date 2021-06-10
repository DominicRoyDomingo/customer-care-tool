<?php

use App\Helpers\Algolia;
use CRM\API\Algolia\AlgoliaPermissionController;

Route::group(['prefix' => 'algolia'], function () {
    Route::get('/summary', [Algolia::class, 'loadAlgoliaSummary']);
    Route::get('/check-permission', [AlgoliaPermissionController::class, 'checkPermission']);
});
