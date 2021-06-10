<?php

use CRM\API\CalendarNotes\CalendarNotesController;
Route::group(['prefix' => 'calendar_notes'], function () {
    Route::get('/', [CalendarNotesController::class, 'index']);
    Route::get('/all', [CalendarNotesController::class, 'all']);
    Route::get('/get-calendar-notes-name', [CalendarNotesController::class, 'getName']);
    Route::post('/', [CalendarNotesController::class, 'store']);
    Route::delete('/', [CalendarNotesController::class, 'destroy']);
    Route::post('/link-brand', [CalendarNotesController::class, 'linkBrand']);
});
