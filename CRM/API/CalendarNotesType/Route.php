<?php

use CRM\API\CalendarNotesType\CalendarNotesTypeController;
Route::group(['prefix' => 'calendar_notes_type'], function () {
    Route::get('/', [CalendarNotesTypeController::class, 'index']);
    Route::get('/all', [CalendarNotesTypeController::class, 'all']);
    Route::get('/get-calendar-notes-type-name', [CalendarNotesTypeController::class, 'getName']);
    Route::post('/', [CalendarNotesTypeController::class, 'store']);
    Route::delete('/', [CalendarNotesTypeController::class, 'destroy']);
    Route::post('/link-brand', [CalendarNotesTypeController::class, 'linkBrand']);
});
