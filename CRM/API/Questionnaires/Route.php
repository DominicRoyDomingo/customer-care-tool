<?php

use CRM\API\Questionnaires\QuestionnaireController;

Route::group(['prefix' => 'questionnaires'], function () {
    Route::get('/', [QuestionnaireController::class, 'index']);
    Route::post('/', [QuestionnaireController::class, 'store']);
    Route::delete('/', [QuestionnaireController::class, 'destroy']);

    Route::group(['prefix' => 'organizes'], function () {
        Route::post('/', [QuestionnaireController::class, 'store_question_sequence']);
        Route::get('/sequences', [QuestionnaireController::class, 'get_sequences']);
        Route::delete('/', [QuestionnaireController::class, 'delete_sequences']);
        Route::post('/sort', [QuestionnaireController::class, 'sort_sequences']);
    });
});
