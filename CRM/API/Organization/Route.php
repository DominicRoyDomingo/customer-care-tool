<?php

use CRM\API\Organization\OrganizationController;

Route::group(['prefix' => 'organization'], function () {

    Route::get('/', [OrganizationController::class, 'index']);
    Route::get('/get-organization-category-name', [OrganizationController::class, 'getOrganizationCategoryName']);
    Route::get('/{id}', [OrganizationController::class, 'show']);

   // for catgory
   Route::post('/', [OrganizationController::class, 'store']);
   Route::delete('/', [OrganizationController::class, 'destroy']);
});
