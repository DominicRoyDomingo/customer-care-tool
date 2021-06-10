<?php

use CRM\API\MedicalStuff\Controllers\ArticleController;
use CRM\API\MedicalStuff\Controllers\DateTypeController;
use CRM\API\MedicalStuff\Controllers\HtmlTagController;
use CRM\API\MedicalStuff\Controllers\PreFilledSectionController;
use CRM\API\MedicalStuff\Controllers\SearchController;
use CRM\API\MedicalStuff\Controllers\TermController;
use CRM\API\MedicalStuff\Controllers\TermTypeController;
use CRM\API\MedicalStuff\Controllers\TermDescriptionController;

Route::group(['prefix' => 'term-types'], function () {
    Route::get('/', [TermTypeController::class, 'index']);
    Route::post('/', [TermTypeController::class, 'store']);
    Route::delete('/', [TermTypeController::class, 'destroy']);
    Route::get('/get-name/{id}/{lang}', [TermController::class, 'getNameMTT']);
});

Route::group(['prefix' => 'type-dates'], function () {

    // get all type of dates
    Route::get('/', [DateTypeController::class, 'index']);
    Route::post('/', [DateTypeController::class, 'store']);
    Route::delete('/', [DateTypeController::class, 'destroy']);
});

Route::group(['prefix' => 'html-tags'], function () {

    // get all html tags priority
    Route::get('/', [HtmlTagController::class, 'index']);
    Route::post('/', [HtmlTagController::class, 'store']);
    Route::delete('/', [HtmlTagController::class, 'destroy']);
});

Route::group(['prefix' => 'terms'], function () {

    Route::get('/', [TermController::class, 'index']);
    Route::get('/show', [TermController::class, 'show']);
    Route::post('/post', [TermController::class, 'post']);
    Route::delete('/delete', [TermController::class, 'destroy']);

    Route::get('/get-name/{id}/{lang}', [TermController::class, 'getNameMedicalTermsArray']);

    Route::get('/get-notes', [TermController::class, 'get_notes']);
    Route::post('/post-notes', [TermController::class, 'post_notes']);
    Route::post('/post-icon', [TermController::class, 'post_icon']);

    Route::group(['prefix' => 'descriptions'], function () {

        // all Terms Descriptions  Routes
        Route::get('/', [TermDescriptionController::class, 'index']);
        Route::post('/', [TermDescriptionController::class, 'store']);
        Route::delete('/', [TermDescriptionController::class, 'destroy']);

        // all Term Connections Descriptions  Routes
        Route::group(['prefix' => 'term-connections'], function () {
            Route::get('/', [TermDescriptionController::class, 'get_term_connection_descriptions']);
            Route::post('/', [TermDescriptionController::class, 'store_term_connection_description']);
            Route::delete('/', [TermDescriptionController::class, 'delete_term_descriptions']);
        });
    });

    Route::group(['prefix' => 'linked'], function () {
        // Route::get('/', [TermController::class, 'get_linked_terms_paginate']);
        Route::get('/article-terms', [ArticleController::class, 'get_article_terms_paginate']);
    });

    Route::group(['prefix' => 'term-icons'], function () {
        // Route::get('/', [TermController::class, 'get_linked_terms_paginate']);
        Route::get('/{icon}', [TermController::class, 'show_icons']);
        Route::delete('/', [TermController::class, 'destroy_icon']);
    });

    Route::get('/services/{id}', [TermController::class, 'get_services_by_category_service']);
    Route::get('/provider-type', [TermController::class, 'get_provider_types']);
    Route::get('/category-services', [TermController::class, 'get_category_services']);

    // Linked terms
    Route::get('/linked-id', [TermController::class, 'get_linked_term_id']);

    Route::post('/post-term-note', [TermController::class, 'store_term_note']);

    Route::get('/terms-by-type', [TermController::class, 'get_terms_by_type']);
    Route::get('/course-term-id', [TermController::class, 'get_course_term_id']);
    Route::post('/linke-course-term', [TermController::class, 'link_course_term']);
});

Route::group(['prefix' => 'advance-search'], function () {
    Route::get('/terms', [SearchController::class, 'advance_search_terms']);
    Route::get('/articles', [SearchController::class, 'advance_search_article']);

    // Route::get('articles', 'MedicalStuff\Controllers\SearchController@advance_search_article');
});

Route::group(['prefix' => 'links'], function () {
    Route::get('/terms', [TermController::class, 'get_linked_terms']);
    Route::get('/terms-actor', [TermController::class, 'get_linked_terms_actor']);
    Route::get('/terms-details', [TermController::class, 'get_linked_terms_details']);
    Route::get('/terms-providers', [TermController::class, 'get_linked_provider_terms']);
    Route::get('/terms-professional', [TermController::class, 'get_professional_terms']);
    Route::get('/terms-services', [TermController::class, 'get_services_terms']);
    Route::post('/term', [TermController::class, 'post_link_term']);
    Route::post('/article', [TermController::class, 'post_link_article']);
});

Route::group(['prefix' => 'articles'], function () {

    Route::get('/', [ArticleController::class, 'index']);
    Route::post('/', [ArticleController::class, 'store']);
    Route::get('/active-paginated', [ArticleController::class, 'get_active_paginated']);
    Route::get('/active-paginated-articles', [ArticleController::class, 'get_active_paginated_articles']);
    Route::get('/{id}', [ArticleController::class, 'show']);
    Route::delete('/', [ArticleController::class, 'destroy']);

    Route::post('/update-image', [ArticleController::class, 'update_image']);
    Route::delete('/delete-image', [ArticleController::class, 'destroy_image']);

    Route::post('/link-article-term', [ArticleController::class, 'link_article_term']);
    Route::post('/publish-content', [ArticleController::class, 'store_publish_content']);
    Route::post('/publish-content/autosave', [ArticleController::class, 'autosave_publish_content']);
});

Route::group(['prefix' => 'type-of-author'], function () {
    Route::get('/', [TermController::class, 'get_type_author']);
    Route::get('/get-author-type-name', [TermController::class, 'get_type_author_name']);
    Route::delete('/delete-type-author', [TermController::class, 'delete_type_author']);
    Route::post('/post-type-author', [TermController::class, 'postTypeAuthor']);
});

Route::post('link-to-brand', [TermController::class, 'link_to_brand']);


Route::group(['prefix' => 'prefilledsection'], function () {
    Route::get('/', [PreFilledSectionController::class, 'index']);
    Route::get('/retrieve/{id}', [PreFilledSectionController::class, 'retrieve']);
    Route::post('/store', [PreFilledSectionController::class, 'store']);
    Route::delete('/', [PreFilledSectionController::class, 'destroy']);
    Route::post('/update', [PreFilledSectionController::class, 'update']);
});
