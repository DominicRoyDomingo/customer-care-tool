<?php

use CRM\API\Platform\PlatformItemController;
use CRM\API\Platform\PlatformTypeController;

Route::group(['prefix' => 'platform'], function () {
   Route::get('items', [PlatformItemController::class, 'index']);
   Route::post('items', [PlatformItemController::class, 'store']);
   Route::delete('items', [PlatformItemController::class, 'destroy']);
   Route::get('types', [PlatformTypeController::class, 'index']);
   Route::post('types', [PlatformTypeController::class, 'store']);
   Route::delete('types', [PlatformTypeController::class, 'destroy']);
});
