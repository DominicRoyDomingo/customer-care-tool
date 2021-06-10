<?php

use CRM\API\ServicesExclusive\ServicesExclusiveController;

Route::group(['prefix' => 'services_exclusive'], function () {

    Route::get('/', [ServicesExclusiveController::class, 'index']);
    // Route::get('/{id}', [ServiceController::class, 'show']);

   // for catgory
   Route::post('/', [ServicesExclusiveController::class, 'store']);
   Route::delete('/', [ServicesExclusiveController::class, 'destroy']);
});
