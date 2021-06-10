<?php

use CRM\API\ProviderGroup\ProviderGroupController;

Route::group(['prefix' => 'provider_group'], function () {

    Route::get('/', [ProviderGroupController::class, 'index']);
    Route::get('/all', [ProviderGroupController::class, 'all']);
    Route::get('/get-provider-group-name', [ProviderGroupController::class, 'getProviderGroupName']);
    Route::get('/get-provider-group', [ProviderGroupController::class, 'getProviderGroup']);
    // Route::get('/{id}', [ServiceController::class, 'show']);

   // for catgory
   Route::post('/', [ProviderGroupController::class, 'store']);
   Route::post('/add-to-provider', [ProviderGroupController::class, 'addToProvider']);
   Route::delete('/', [ProviderGroupController::class, 'destroy']);
});
