<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\UserController;


/* Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'users'])->name('users.list')->middleware('checkPermission:view');
    Route::get('/{role_id}', [UserController::class, 'users'])->name('users.role')->middleware('checkPermission:view');
    // Route::get('/{id}', [UserController::class, 'usersByRole'])->name('users.roletype')->middleware('checkPermission:view');
    Route::get('/create', [UserController::class, 'createuser'])->name('users.create')->middleware('checkPermission:create');
    Route::post('/saveuser', [UserController::class, 'saveuser'])->name('users.save')->middleware('checkPermission:create');
    Route::post('/status', [UserController::class, 'userstatus'])->name('users.status')->middleware('checkPermission:edit');
});
 */

Route::prefix('users')->group(function () {

    Route::get('/', [UserController::class, 'users'])->name('users.list')->middleware('checkPermission:view');

    Route::get('/create', [UserController::class, 'createEditUser'])->name('users.create')->middleware('checkPermission:create');

    Route::get('/{id}/edit', [UserController::class, 'createEditUser'])->name('users.edit')->middleware('checkPermission:edit');

    Route::post('/save-update', [UserController::class, 'saveUpdateUser'])->name('users.saveUpdate')->middleware('checkPermission:create');

    Route::post('/status', [UserController::class, 'userstatus'])->name('users.status')->middleware('checkPermission:edit');

    Route::get('/{role_id}', [UserController::class, 'users'])->name('users.role')->middleware('checkPermission:view');
});


Route::get('/regions-by-country/{country}', [ UserController::class,'regionsByCountry'])->whereNumber('country')->name('users.regionsByCountry');

Route::get('/profile', [UserController::class, 'editProfile'])->name('profile.edit');
Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
Route::get('/change-password', [UserController::class, 'editPassword'])->name('password.edit');
Route::put('/change-password', [UserController::class, 'updatePassword'])->name('password.update');
