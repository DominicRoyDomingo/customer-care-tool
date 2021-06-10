<?php

use CRM\API\Activity\ActivityController;

Route::group(['prefix' => 'actions'], function () {
    Route::get('activity', [ActivityController::class, 'index']);
    Route::post('activity/stored', [ActivityController::class, 'stored']);
    Route::delete('activity/delete', [ActivityController::class, 'destroy']);
});
