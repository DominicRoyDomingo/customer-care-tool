<?php

use CRM\API\Parameter\ParameterController;

Route::group(['prefix' => 'parameter'], function () {

    Route::get('/', [ParameterController::class, 'index']);
    // Route::get('/{id}', [PlaceController::class, 'show']);

   // for catgory
   Route::post('/', [ParameterController::class, 'store']);
   Route::delete('/', [ParameterController::class, 'destroy']);
});
