<?php

use CRM\API\Reasons\ReasonsController;
Route::group(['prefix' => 'reasons'], function () {
    Route::get('/', [ReasonsController::class, 'index']);
    Route::get('/all', [ReasonsController::class, 'all']);
    Route::get('/get-reasons-name', [ReasonsController::class, 'getName']);
    Route::post('/', [ReasonsController::class, 'store']);
    Route::delete('/', [ReasonsController::class, 'destroy']);
    Route::post('/link-brand', [ReasonsController::class, 'linkBrand']);
});
