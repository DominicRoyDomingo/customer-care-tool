<?php

use CRM\API\Profile\ProfileController;

Route::group(['prefix' => 'profile'], function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::get('/find_match', [ProfileController::class, 'find_match'])->name('find_match');
    Route::get('/migrate_customers', [ProfileController::class, 'migrate_customers'])->name('migrate_customers');
    
    //New API 
    Route::get('/load_profiles_under_brand', [ProfileController::class, 'load_profiles_under_brand']);  //Initial load for index
    Route::get('/load_filtered_profiles', [ProfileController::class, 'load_filtered_profiles']);  //Filter load for index
    
    Route::get('/{id}', [ProfileController::class, 'show']);
    Route::post('/', [ProfileController::class, 'store']);
    Route::delete('/', [ProfileController::class, 'destroy']);

    Route::post('/link_to_brands', [ProfileController::class, 'link_to_brands']);

    Route::post('/add_contacts', [ProfileController::class, 'add_contacts']);
    Route::post('/update_contact', [ProfileController::class, 'update_contact']);
    Route::post('/delete_contact', [ProfileController::class, 'delete_contact']);

    Route::post('/add_client_engagements', [ProfileController::class, 'add_client_engagements']);
    Route::post('/update_client_engagement', [ProfileController::class, 'update_client_engagement']);
    Route::post('/delete_client_engagement', [ProfileController::class, 'delete_client_engagement']);
    
    Route::post('/add_notes', [ProfileController::class, 'add_notes']);
    Route::post('/update_note', [ProfileController::class, 'update_note']);
    Route::post('/delete_note', [ProfileController::class, 'delete_note']);

    
    Route::post('/add_attachments', [ProfileController::class, 'add_attachments']);
    Route::post('/update_attachment', [ProfileController::class, 'update_attachment']);
    Route::post('/delete_attachment', [ProfileController::class, 'delete_attachment']);

});