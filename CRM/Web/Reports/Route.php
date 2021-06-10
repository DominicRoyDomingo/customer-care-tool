<?php

use CRM\Web\Reports\Controller;

Route::group(['prefix' => 'reports'], function () {
    Route::get('insights', [Controller::class, 'insight_view'])
        ->name('reports.insights');

    Route::get('statistics', [Controller::class, 'statistic_view'])
        ->name('reports.statistics');
    Route::get('summaries', [Controller::class, 'summary_view'])
        ->name('reports.summaries');
});
