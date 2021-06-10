<?php

use CRM\API\RequestType\RequestTypeController;
Route::group(['prefix' => 'request_type'], function () {
    Route::get('/', [RequestTypeController::class, 'index']);
    Route::get('/all', [RequestTypeController::class, 'all']);
    Route::get('/get-request-type-name', [RequestTypeController::class, 'getName']);
    Route::post('/', [RequestTypeController::class, 'store']);
    Route::delete('/', [RequestTypeController::class, 'destroy']);
    Route::post('/link-brand', [RequestTypeController::class, 'linkBrand']);
});
