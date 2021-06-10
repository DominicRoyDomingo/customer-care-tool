<?php

use CRM\API\Questions\QuestionController;
use CRM\API\Questions\QuestionTypeController;


Route::group(['prefix' => 'questions'], function () {
	Route::group(['prefix' => 'question-types'], function () {
		Route::get('/', [QuestionTypeController::class, 'index']);
		Route::post('/', [QuestionTypeController::class, 'store']);
		// put method is not working so I'm using post method for update
		Route::post('/{id}/update', [QuestionTypeController::class, 'update']);
		Route::get('/{id}', [QuestionTypeController::class, 'show']);
		Route::delete('/{id}/delete', [QuestionTypeController::class, 'destroy']);
	});

	Route::group(['prefix' => 'questions'], function () {
		Route::get('/', [QuestionController::class, 'index']);
		Route::post('/', [QuestionController::class, 'store']);
		// put method is not working so I'm using post method for update
		Route::post('/{id}/update', [QuestionController::class, 'update']);
		Route::get('/{id}', [QuestionController::class, 'show']);
		Route::delete('/{id}/delete', [QuestionController::class, 'destroy']);
	});

	Route::group(['prefix' => 'questions/{id}'], function () {
		Route::post('brands', [QuestionController::class, 'storeQuestionBrand']);
	});



	Route::group(['prefix' => 'terms'], function () {
		Route::post('/', [QuestionController::class, 'get_all_terms']);
		Route::post('/update-question-terms', [QuestionController::class, 'updateQuestionTerms']);
		Route::post('/search', [QuestionController::class, 'search_terms']);
		Route::get('/single-question-terms/{question_id}', [QuestionController::class, 'getQuestionTerms']);
	});

	// route for questionnaire page
	Route::get('/', [QuestionController::class, 'get_questions']);
});
