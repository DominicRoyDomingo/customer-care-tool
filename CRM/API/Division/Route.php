<?php

use CRM\API\Division\DivisionController;

Route::group(['prefix' => 'division'], function () {
    Route::get('/', [DivisionController::class, 'index']);
    Route::get('/all', [DivisionController::class, 'all']);
});
