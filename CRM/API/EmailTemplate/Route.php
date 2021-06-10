<?php

use CRM\API\EmailTemplate\EmailTemplateController;

Route::group(['prefix' => 'email_templates'], function () {

    Route::get('/', [EmailTemplateController::class, 'index']);
    Route::get('/{id}', [EmailTemplateController::class, 'show']);

   // for catgory
   Route::post('/', [EmailTemplateController::class, 'store']);
   Route::delete('/', [EmailTemplateController::class, 'destroy']);
});
