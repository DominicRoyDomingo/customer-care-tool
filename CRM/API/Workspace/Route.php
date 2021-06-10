<?php

use CRM\API\Workspace\WorkspaceController;

Route::group(['prefix' => 'workspace'], function () {
    Route::get('/', [WorkspaceController::class, 'index']);
    Route::get('/all', [WorkspaceController::class, 'all']);
    
    Route::delete('/', [WorkspaceController::class, 'destroy']);
});
