<?php

use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\OrganizationCategoryController;
use App\Http\Controllers\Frontend\User\AccountController;
use App\Http\Controllers\Frontend\User\DashboardController;
use App\Http\Controllers\Frontend\User\ProfileController;
use App\Http\Controllers\Frontend\LocationListController;
use Illuminate\Support\Facades\Route;

/*
 * Frontend Controllers
 * All route names are prefixed with 'frontend.'.
 */

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('contact', [ContactController::class, 'index'])->name('contact');
Route::get('/organizationCategories', [OrganizationCategoryController::class, 'organizationCategories'])->name('organizationCategories');
Route::put('/organization/{organization}', [OrganizationCategoryController::class, 'update'])->name('organization.update');
Route::get('/organizations', [OrganizationCategoryController::class, 'organizations'])->name('organizations');
Route::post('contact/send', [ContactController::class, 'send'])->name('contact.send');

Route::get('/location/countries', [LocationListController::class, 'countries'])->name('location.countries');
Route::get('/location/divisions', [LocationListController::class, 'divisions'])->name('location.divisions');
Route::get('/location/cities', [LocationListController::class, 'cities'])->name('location.cities');
/*
 * These frontend controllers require the user to be logged in
 * All route names are prefixed with 'frontend.'
 * These routes can not be hit if the password is expired
 */
Route::group(['middleware' => ['auth', 'password_expires']], function () {
    Route::group(['namespace' => 'User', 'as' => 'user.'], function () {
        // User Dashboard Specific
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // User Account Specific
        Route::get('account', [AccountController::class, 'index'])->name('account');

        // User Profile Specific
        Route::patch('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });
});
