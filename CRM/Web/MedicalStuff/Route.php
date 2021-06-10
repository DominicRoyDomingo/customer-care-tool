<?php

use CRM\Web\MedicalStuff\Controller;

Route::group(['prefix' => 'categorized-terms'], function () {

    Route::get('term-type', [Controller::class, 'term_type'])
        ->name('categorized-terms.term-type');

    Route::get('type-dates', [Controller::class, 'type_date'])
        ->name('categorized-terms.type-date');

    //  Terminologies Routes
    Route::group(['prefix' => 'terminologies'], function () {

        Route::get('/', [Controller::class, 'terms'])
            ->name('categorized-terms.terminologies');

        Route::get('/descriptions', [Controller::class, 'term_description'])
            ->name('categorized-terms.term-descriptions');

        Route::get('/{id}', [Controller::class, 'term_show'])
            ->name('categorized-terms.terminology-show');
    });

    //  HTML Tags Priority Routes
    Route::group(['prefix' => 'html-tags-priority'], function () {
        Route::get('/', [Controller::class, 'html_tags_priority'])
            ->name('categorized-terms.html_tags_priority');
    });

    //  TYPE OF AUTHORS
    Route::group(['prefix' => 'type-of-authors'], function () {
        Route::get('/', [Controller::class, 'type_authors'])
            ->name('categorized-terms.type-of-authors');
    });
    // Route::get('specializations', [Controller::class, 'specializations'])
    //     ->name('categorized-terms.specializations');
});


Route::group(['prefix' => 'software-publishing'], function () {

    Route::group(['prefix' => 'items'], function () {
        Route::get('/', [Controller::class, 'item_index'])
            ->name('software-publishing.publishing-items');

        Route::get('/{id}', [Controller::class, 'item_show'])
            ->name('software-publishing.item-show');
    });

    Route::group(['prefix' => 'item-types'], function () {
        Route::get('/manage', [Controller::class, 'publishing_manage'])
            ->name('software-publishing.manage');
    });

    Route::group(['prefix' => 'pre-filled-sections'], function () {
        Route::get('/manage', [Controller::class, 'prefilledsection_manage'])
            ->name('software-publishing.pre-filled-section.manage');
    });
});
