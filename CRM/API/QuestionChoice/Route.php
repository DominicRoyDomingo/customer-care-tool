<?php

use CRM\API\QuestionChoice\QuestionChoiceController;

Route::group(['prefix' => 'questions/choices'], function () {
    Route::get('/', [QuestionChoiceController::class, 'index']);
    Route::post('/', [QuestionChoiceController::class, 'store']);
    Route::delete('/', [QuestionChoiceController::class, 'destroy']);
});
