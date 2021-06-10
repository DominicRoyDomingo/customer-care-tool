<?php

use CRM\API\PhysicalCodeType\PhysicalCodeTypeController;

Route::group(['prefix' => 'physical_code_type'], function () {

    Route::get('/', [PhysicalCodeTypeController::class, 'index']);
    Route::get('/all', [PhysicalCodeTypeController::class, 'all']);
    Route::get('/get-physical-code-type-name', [PhysicalCodeTypeController::class, 'getPhysicalCodeTypeName']);
    // Route::get('/{id}', [ServiceController::class, 'show']);

   // for catgory
   Route::post('/', [PhysicalCodeTypeController::class, 'store']);
   Route::delete('/', [PhysicalCodeTypeController::class, 'destroy']);
});
