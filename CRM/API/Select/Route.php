<?php

// Route::get('select/{type}', 'Select\SelectController')->name('select');
// Route::get('select/{type}/all', 'Select\SelectController@all')->name('select.all');

use CRM\API\Select\SelectController;


// Route::get('select/{type}', 'Select\SelectController')->name('select');

Route::get('select/{type}/all', [SelectController::class, 'all'])
    ->name('select.all');

Route::get('select/{type}/list', [SelectController::class, 'list'])
    ->name('select.list');

Route::get('select/{type}/list_job_descriptions', [SelectController::class, 'list_job_descriptions'])
    ->name('select.list_job_descriptions');

Route::get('select/{type}/list_course_types', [SelectController::class, 'list_course_types'])
    ->name('select.list_course_types');
