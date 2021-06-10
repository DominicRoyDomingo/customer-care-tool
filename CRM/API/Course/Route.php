<?php

use CRM\API\Course\CourseController;
use CRM\API\Course\CourseInfoController;
use CRM\API\Course\CourseTypeController;

Route::group(['prefix' => 'course'], function () {
    Route::get('get-types', [CourseTypeController::class, 'get_course_type']);
    Route::post('add-type', [CourseTypeController::class, 'add_course_type']);
    Route::get('get-edit-type/{id}/{lang}', [CourseTypeController::class, 'getCourseTypeName']);
    Route::post('update-type', [CourseTypeController::class, 'updateCourseType']);
    Route::put('delete-type/{id}', [CourseTypeController::class, 'deleteCourseType']);
    Route::post('search-type', [CourseTypeController::class, 'searchCourseType']);
    Route::post('sort-type', [CourseTypeController::class, 'sortCourseType']);

    Route::post('sync', [CourseController::class, 'syncToAlgolia']);

    // API routes for course info
    Route::group(['prefix' => 'info'], function () {
        Route::post('/', [CourseInfoController::class, 'store']);
    });
});
