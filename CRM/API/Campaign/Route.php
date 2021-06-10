<?php

use CRM\API\Campaign\CampaignController;

Route::group(['prefix' => 'campaigns'], function () {
   Route::get('/', [CampaignController::class, 'index']);
   Route::get('/{id}', [CampaignController::class, 'show']);

   Route::post('post_campaign', [CampaignController::class, 'post_campaign']);
   Route::post('post', [CampaignController::class, 'store']);
   Route::delete('delete', [CampaignController::class, 'destroy']);

   Route::post('sendEmail', [CampaignController::class, 'sendEmail']);

   Route::post('sendCampaignEmail', [CampaignController::class, 'sendCampaignEmail']);

   Route::delete('remove_recipient', [CampaignController::class, 'remove_recipient']);


   Route::group(['prefix' => 'checkform'], function () {
      Route::post('campaign_email', [CampaignController::class, 'checkCampaignEmailForm']);
   });
});

Route::group(['prefix' => 'campaign_emails'], function () {
   Route::get('/{id}', [CampaignController::class, 'show_email']);
});