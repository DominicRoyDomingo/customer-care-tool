<?php

use CRM\API\DocumentType\DocumentTypeController;

Route::group(['prefix' => 'document_types'], function () {

    Route::get('/', [DocumentTypeController::class, 'index']);
    Route::get('/{id}', [DocumentTypeController::class, 'show']);

   // for catgory
   Route::post('/', [DocumentTypeController::class, 'store']);
   Route::delete('/', [DocumentTypeController::class, 'destroy']);
});
