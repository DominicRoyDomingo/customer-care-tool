<?php

    use App\Http\Controllers\Backend\PlatformTypeController;
    use App\Http\Controllers\Backend\PlatformItemController;
  
    // Routes for platform type
    Route::group(['namespace' => 'Platform-Type'], function () {
 
        Route::group(['prefix' => 'platform-type'], function () {
 
            Route::get('/', [PlatformTypeController::class, 'index'])->name('platform-type');
 
            Route::get('/getList', [PlatformTypeController::class, 'getList'])->name('platform-type.getList');
 
            Route::post('/postPlatform', [PlatformTypeController::class, 'postPlatform'])->name('platform-type.postPlatform');
 
            Route::post('/updatePlatform', [PlatformTypeController::class, 'updatePlatform'])->name('platform-type.updatePlatform');
 
            Route::get('/deletePlatform/{id}', [PlatformTypeController::class, 'deletePlatform'])->name('platform-type.deletePlatform');
 
            Route::get('/getPlatformName/{id}/{lang}', [PlatformTypeController::class, 'getPlatformName'])->name('platform-type.getPlatformName');
 
        });
 
    });


    // Routes for platform item
    Route::group(['namespace' => 'Platform-Item'], function () {
 
        Route::group(['prefix' => 'platform-item'], function () {
 
            Route::get('/', [PlatformItemController::class, 'index'])->name('platform-item');
 
            Route::get('/getList', [PlatformItemController::class, 'getList'])->name('platform-item.getList');
 
            Route::post('/postPlatform', [PlatformItemController::class, 'postPlatform'])->name('platform-item.postPlatform');
 
            Route::post('/updatePlatform', [PlatformItemController::class, 'updatePlatform'])->name('platform-item.updatePlatform');
 
            Route::get('/deletePlatform/{id}', [PlatformItemController::class, 'deletePlatform'])->name('platform-item.deletePlatform');
 
            Route::get('/getPlatformName/{id}/{lang}', [PlatformItemController::class, 'getPlatformName'])->name('platform-item.getPlatformName');
 
        });
 
    });