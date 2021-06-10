<?php

use CRM\Web\Questionnaire\Controller;

Route::group(['prefix' => 'questionnaires'], function () {

    Route::get('/', [Controller::class, 'questionnaire_view'])
        ->name('questionnaires.index');
});
