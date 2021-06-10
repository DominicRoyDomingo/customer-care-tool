<?php

use CRM\API\Result\ResultController;


Route::group(['prefix' => 'result'], function () {

	Route::get('/', [ResultController::class, 'index']);
	Route::post('add', [ResultController::class, 'add_result']);
	Route::get('single-result', [ResultController::class, 'single_result']);
	Route::post('update', [ResultController::class, 'update_result']);
	Route::post('delete', [ResultController::class, 'delete_result']);
	Route::post('search', [ResultController::class, 'search_result']);
	Route::post('brands', [ResultController::class, 'get_brands']);
	Route::post('update-brand', [ResultController::class, 'update_brand']);
	Route::get('result-brand/{result_id}', [ResultController::class, 'get_result_brand']);
	Route::post('orphan-filter', [ResultController::class, 'orphan_filter']);
});