<?php

use CRM\API\ServiceType\ServiceTypeController;

Route::group(['prefix' => 'service_type'], function () {

    Route::get('/', [ServiceTypeController::class, 'index']);
    Route::get('/all', [ServiceTypeController::class, 'all']);
    Route::get('/get-service-type-name', [ServiceTypeController::class, 'getServiceTypeName']);
    // Route::get('/{id}', [ServiceTypeController::class, 'show']);

   // for catgory
   Route::post('/', [ServiceTypeController::class, 'store']);
   Route::delete('/', [ServiceTypeController::class, 'destroy']);
});
