<?php

use CRM\API\Provider\ProviderController;

Route::group(['prefix' => 'provider'], function () {

    Route::get('/', [ProviderController::class, 'index']);
    Route::get('/algolia-summary', [ProviderController::class, 'getAlgoliaSummary']);
    Route::get('/get-provider-name', [ProviderController::class, 'getProviderName']);
    Route::post('/link-brand', [ProviderController::class, 'linkBrand']);
    Route::post('/link-actor', [ProviderController::class, 'linkActor']);
    Route::post('/add-to-group', [ProviderController::class, 'addToGroup']);
    Route::post('/change-account-status', [ProviderController::class, 'changeAccountStatus']);
    Route::post('/sync-to-algolia', [ProviderController::class, 'syncToAlgolia']);
    Route::post('/add-to-sync-reference', [ProviderController::class, 'addToSyncReference']);
    Route::get('/get-provider', [ProviderController::class, 'getProvider']);
    Route::get('/add-provider-profile', [ProviderController::class, 'addProviderProfile']);
    Route::get('/get-next-or-previous', [ProviderController::class, 'getNextOrPrevious']);

   Route::post('/', [ProviderController::class, 'store']);
   Route::delete('/', [ProviderController::class, 'destroy']);
   Route::delete('/delete-image', [ProviderController::class, 'deleteImage']);
});
