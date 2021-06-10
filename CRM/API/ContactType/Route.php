<?php

use CRM\API\ContactType\ContactTypeController;

Route::group(['prefix' => 'contact_types'], function () {

    Route::get('/', [ContactTypeController::class, 'index']);
    Route::get('/getByLang/{lang?}', [ContactTypeController::class, 'getByLang']);

   // for catgory
   Route::post('/', [ContactTypeController::class, 'store']);
   Route::delete('/', [ContactTypeController::class, 'destroy']);
});
