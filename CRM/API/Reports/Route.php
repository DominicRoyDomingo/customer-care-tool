<?php

use CRM\API\Reports\ReportController;

Route::group(['prefix' => 'reports'], function () {

    Route::get('/insights', [ReportController::class, 'get_insight']);

    Route::get('/terms', [ReportController::class, 'get_terms']);
});
