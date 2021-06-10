<?php

use CRM\API\Actor\ActorController;

Route::group(['prefix' => 'actor'], function () {

    Route::get('/', [ActorController::class, 'index']);
    Route::get('/all', [ActorController::class, 'all']);
    Route::post('/', [ActorController::class, 'store']);
    Route::post('/link-brand', [ActorController::class, 'linkBrand']);
    Route::post('/link-actor', [ActorController::class, 'linkActor']);
    Route::post('/link-term', [ActorController::class, 'linkTerm']);
    Route::post('/update-information-list', [ActorController::class, 'updateInformationList']);
    Route::delete('/', [ActorController::class, 'destroy']);
});
