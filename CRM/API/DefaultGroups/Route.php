<?php

use CRM\API\DefaultGroups\DefaultGroupsController;
Route::group(['prefix' => 'default-groups'], function () {
    Route::get('/', [DefaultGroupsController::class, 'index']);
    Route::get('/all', [DefaultGroupsController::class, 'all']);
    Route::get('/get-default-groups-name', [DefaultGroupsController::class, 'getName']);
    Route::post('/', [DefaultGroupsController::class, 'store']);
    Route::delete('/', [DefaultGroupsController::class, 'destroy']);
    Route::post('/link-brand', [DefaultGroupsController::class, 'linkBrand']);
});
