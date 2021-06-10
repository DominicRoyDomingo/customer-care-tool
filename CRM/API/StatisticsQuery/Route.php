<?php

use CRM\API\StatisticsQuery\StatisticsQueryController;

Route::group(['prefix' => 'statistics_queries'], function () {

    Route::get('/', [StatisticsQueryController::class, 'index']);
    Route::get('/tables', [StatisticsQueryController::class, 'tables']);
    Route::get('/fields', [StatisticsQueryController::class, 'fields']);

    Route::post('/fetch', [StatisticsQueryController::class, 'fetchData']);

    Route::post('/query', [StatisticsQueryController::class, 'loadQuery']);
    Route::get('/table', [StatisticsQueryController::class, 'fetchTableFields']);
    Route::get('/{id}', [StatisticsQueryController::class, 'show']);

   // for catgory
   Route::post('/', [StatisticsQueryController::class, 'store']);
   Route::delete('/', [StatisticsQueryController::class, 'destroy']);
});
