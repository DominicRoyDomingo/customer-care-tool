<?php

use CRM\API\InformationType\InformationTypeController;

Route::group(['prefix' => 'information_type'], function () {

    Route::get('/', [InformationTypeController::class, 'index']);
    Route::get('/all', [InformationTypeController::class, 'all']);
    Route::get('/get-information-type-name', [InformationTypeController::class, 'getInformationTypeName']);
    // Route::get('/{id}', [ServiceController::class, 'show']);

   // for catgory
   Route::post('/', [InformationTypeController::class, 'store']);
   Route::delete('/', [InformationTypeController::class, 'destroy']);
});
