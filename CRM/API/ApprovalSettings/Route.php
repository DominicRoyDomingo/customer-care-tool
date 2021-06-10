<?php

use CRM\API\ApprovalSettings\ApprovalSettingsController;
Route::group(['prefix' => 'approval-settings'], function () {
    Route::get('/', [ApprovalSettingsController::class, 'index']);
    Route::get('/all', [ApprovalSettingsController::class, 'all']);
    Route::get('/get-approval-settings-name', [ApprovalSettingsController::class, 'getName']);
    Route::post('/', [ApprovalSettingsController::class, 'store']);
    Route::delete('/', [ApprovalSettingsController::class, 'destroy']);
    Route::post('/link-brand', [ApprovalSettingsController::class, 'linkBrand']);
});
