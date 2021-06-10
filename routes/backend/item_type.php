<?php

    use App\Http\Controllers\Backend\ItemTypeController;

    // Routes for item type
    Route::group(['namespace' => 'Item Type'], function () {

        Route::group(['prefix' => 'item-type'], function () {

            Route::get('/', [ItemTypeController::class, 'index'])->name('item-type');

            Route::get('/getList', [ItemTypeController::class, 'getList'])->name('item-type.getList');

            Route::post('/postItemType', [ItemTypeController::class, 'postItemType'])->name('item-type.postItemType');

            Route::post('/searchItemType', [ItemTypeController::class, 'searchItemType'])->name('item-type.searchItemType');

            Route::post('/sortItemType', [ItemTypeController::class, 'sortItemType'])->name('item-type.sortItemType');
            
            Route::post('/updateItemType', [ItemTypeController::class, 'updateItemType'])->name('item-type.updateItemType');

            Route::get('/deleteItemType/{id}', [ItemTypeController::class, 'deleteItemType'])->name('item-type.deleteItemType');

            Route::get('/getItemTypeName/{id}/{lang}', [ItemTypeController::class, 'getItemTypeName'])->name('item-type.getItemTypeName');

        });

    });
