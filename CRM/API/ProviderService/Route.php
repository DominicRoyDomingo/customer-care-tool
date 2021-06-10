<?php

use CRM\API\ProviderService\ProviderServiceController;

Route::group(['prefix' => 'provider_service'], function () {

    Route::get('/', [ProviderServiceController::class, 'index']);
    Route::get('/get-provider-service-items', [ProviderServiceController::class, 'getProviderServiceItems']);
    Route::get('/get-all-provider-services', [ProviderServiceController::class, 'getAll']);
    // Route::get('/{id}', [PlaceController::class, 'show']);

   // for catgory
   Route::post('/', [ProviderServiceController::class, 'store']);
   Route::delete('/', [ProviderServiceController::class, 'destroy']);
});
