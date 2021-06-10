<?php

use CRM\API\ProviderType\ProviderTypeController;

Route::group(['prefix' => 'provider_type'], function () {

    Route::get('/', [ProviderTypeController::class, 'index']);
    Route::get('/all', [ProviderTypeController::class, 'all']);
    Route::get('/get-provider-type-name', [ProviderTypeController::class, 'getproviderTypeName']);
    // Route::get('/{id}', [ServiceController::class, 'show']);

   // for catgory
   Route::post('/', [ProviderTypeController::class, 'store']);
   Route::delete('/', [ProviderTypeController::class, 'destroy']);
});
