<?php

use CRM\API\Service\ServiceController;

Route::group(['prefix' => 'service'], function () {

    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/get-service-name', [ServiceController::class, 'getServiceName']);
    Route::get('/check-medical-type', [ServiceController::class, 'checkMedicalType']);
    Route::get('/check-link-or-article', [ServiceController::class, 'checkIfHasLinkOrArticle']);
    // Route::get('/{id}', [ServiceController::class, 'show']);

   // for catgory
   Route::post('/', [ServiceController::class, 'store']);
   Route::post('/link-brand', [ServiceController::class, 'linkBrand']);
   Route::delete('/', [ServiceController::class, 'destroy']);
});
