<?php

use CRM\API\Brand\BrandController;

Route::group(['prefix' => 'brands'], function () {

    Route::get('/', [BrandController::class, 'index']);
    Route::get('/{id}', [BrandController::class, 'show']);
    Route::get('/category/categories', [BrandController::class, 'getCategories']);
    Route::post('/category/categories', [BrandController::class, 'storeCategories']);

   // for catgory
   Route::post('/', [BrandController::class, 'store']);
   Route::delete('/', [BrandController::class, 'destroy']);
   Route::post('/postNew', [BrandController::class, 'saveNewBrand']);
});
