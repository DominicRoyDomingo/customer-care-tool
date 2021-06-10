<?php

use CRM\API\OrganizationCategory\OrganizationCategoryController;

Route::group(['prefix' => 'organization_categories'], function () {

    Route::get('/', [OrganizationCategoryController::class, 'index']);
    Route::get('/get-organization-category-name', [OrganizationCategoryController::class, 'getOrganizationCategoryName']);
    Route::get('/{id}', [OrganizationCategoryController::class, 'show']);

   // for catgory
   Route::post('/', [OrganizationCategoryController::class, 'store']);
   Route::delete('/', [OrganizationCategoryController::class, 'destroy']);
});
