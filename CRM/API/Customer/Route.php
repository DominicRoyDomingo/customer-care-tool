<?php

use CRM\API\Customer\CustomerController;

Route::group(['prefix' => 'customers'], function () {

    Route::get('/', [CustomerController::class, 'index']);
    Route::get('/{id}', [CustomerController::class, 'show']);

   // for catgory
   Route::post('/', [CustomerController::class, 'store']);
   Route::delete('/', [CustomerController::class, 'destroy']);
});
