<?php

use CRM\API\ColorCoding\ColorCodingController;
Route::group(['prefix' => 'color-coding'], function () {
    Route::get('/', [ColorCodingController::class, 'index']);
    Route::get('/all', [ColorCodingController::class, 'all']);
    Route::get('/get-color-coding-name', [ColorCodingController::class, 'getName']);
    Route::post('/', [ColorCodingController::class, 'store']);
    Route::delete('/', [ColorCodingController::class, 'destroy']);
    Route::post('/link-brand', [ColorCodingController::class, 'linkBrand']);
    Route::get('/check_slot_item', [ColorCodingController::class, 'checkSlotItem']);

    
});
