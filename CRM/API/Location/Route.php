<?php

use CRM\API\Location\LocationController;

Route::group(['prefix' => 'location'], function () {
    Route::get('countries', [LocationController::class, 'get_countries'])->name('locations.get_countries');

    Route::group(['prefix' => 'provinces'], function () {
        Route::post('/', [LocationController::class, 'add_province'])->name('locations.add_province');
        Route::get('/{id}', [LocationController::class, 'get_provinces'])->name('locations.get_provinces');
        Route::put('/{id}', [LocationController::class, 'update_province'])->name('locations.update_province');
        Route::delete('/{id}', [LocationController::class, 'delete_province'])->name('locations.delete_province');
    });

    Route::group(['prefix' => 'cities'], function () {
        Route::post('/', [LocationController::class, 'add_city'])->name('locations.add_city');
        Route::get('/{id}', [LocationController::class, 'get_cities'])->name('locations.get_cities');
        Route::put('/{id}', [LocationController::class, 'update_city'])->name('locations.update_city');
        Route::delete('/{id}', [LocationController::class, 'delete_city'])->name('locations.delete_city');
    });

    Route::group(['prefix' => 'regions'], function () {
        Route::post('/', [LocationController::class, 'add_region'])->name('locations.add_region');
        Route::get('/{id}', [LocationController::class, 'get_regions'])->name('locations.get_regions');
        Route::put('/{id}', [LocationController::class, 'update_region'])->name('locations.update_region');
        Route::delete('/{id}', [LocationController::class, 'delete_region'])->name('locations.delete_region');
    });

    Route::group(['prefix' => 'filter'], function () {
        Route::get('/cities', [LocationController::class, 'filter_cities'])->name('locations.filter.cities');
        Route::get('/provinces', [LocationController::class, 'filter_provinces'])->name('locations.filter.provinces');
        Route::get('/regions', [LocationController::class, 'filter_regions'])->name('locations.filter.regions');
    });

    Route::group(['prefix' => 'fetch'], function () {
        Route::get('/city/{id}', [LocationController::class, 'fetch_city'])->name('locations.fetch.city');
        Route::get('/province/{id}', [LocationController::class, 'fetch_province'])->name('locations.fetch.province');
        Route::get('/region/{id}', [LocationController::class, 'fetch_region'])->name('locations.fetch.region');
    });
});
