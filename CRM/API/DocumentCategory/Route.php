<?php

use CRM\API\DocumentCategory\DocumentCategoryController;

Route::group(['prefix' => 'document_categories'], function () {

    Route::get('/', [DocumentCategoryController::class, 'index']);
    Route::get('/{id}', [DocumentCategoryController::class, 'show']);

   // for catgory
   Route::post('/', [DocumentCategoryController::class, 'store']);
   Route::delete('/', [DocumentCategoryController::class, 'destroy']);
});
