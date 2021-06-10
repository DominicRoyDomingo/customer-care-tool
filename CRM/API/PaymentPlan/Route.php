<?php

use CRM\API\PaymentPlan\PaymentPlanController;

Route::group(['prefix' => 'payment-plans'], function () {
    Route::get('/', [PaymentPlanController::class, 'index']);
    Route::post('/', [PaymentPlanController::class, 'store']);
    Route::delete('/', [PaymentPlanController::class, 'destroy']);
});
