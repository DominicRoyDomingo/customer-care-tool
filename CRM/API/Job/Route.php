<?php

use CRM\API\Job\JobController;

Route::group(['prefix' => 'jobs'], function () {

   Route::get('/', [JobController::class, '_index']);
   Route::get('/get_filtered', [JobController::class, '_get_filtered']);
   Route::get('/get_categories', [JobController::class, '_get_categories']);
   Route::get('/get_filtered_job_professions', [JobController::class, '_get_filtered_job_professions']);

   // for catgory
   Route::post('post_job_category', [JobController::class, '_store_category']);
   Route::delete('delete_job_category', [JobController::class, '_destroy_category']);

   // for Profession
   Route::post('post_job_profession', [JobController::class, '_store_profession']);
   Route::delete('delete_job_profession', [JobController::class, '_destroy_profession']);

   // for description
   Route::post('post_job_description', [JobController::class, '_store_description']);
   Route::delete('delete_job_description', [JobController::class, '_destroy_description']);
});
