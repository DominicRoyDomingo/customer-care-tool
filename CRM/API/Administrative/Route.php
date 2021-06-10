<?php

use CRM\API\Administrative\AdministrativeController;

Route::group(['prefix' => 'actions'], function () {
   Route::get('administrative', [AdministrativeController::class, 'index']);
   Route::post('administrative/stored', [AdministrativeController::class, 'stored']);
   Route::delete('administrative/delete', [AdministrativeController::class, 'destroy']);
});
