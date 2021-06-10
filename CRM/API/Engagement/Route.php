<?php

use CRM\API\Engagement\EngagementController;

Route::group(['prefix' => 'actions'], function () {
   Route::get('engagements', [EngagementController::class, 'index']);
   Route::post('engagements/stored', [EngagementController::class, 'stored']);
   Route::delete('engagements/delete', [EngagementController::class, 'destroy']);
});
