<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\RoleController;


/* Route::prefix('role')->group(function () {
    Route::get('/', [RoleController::class, 'role'])->name('role.list')->middleware('checkPermission:view');
    Route::get('/create', [RoleController::class, 'createrole'])->name('role.create')->middleware('checkPermission:create');
    //Route::get('/{id}', [RoleController::class, 'usersByRole'])->name('role.roletype')->middleware('checkPermission:view');
    Route::post('/save', [RoleController::class, 'saverole'])->name('role.save')->middleware('checkPermission:create');
    Route::post('/status', [RoleController::class, 'rolestatus'])->name('role.status')->middleware('checkPermission:edit');
});
 */

Route::prefix('role')->group(function () {

    Route::get('/', [RoleController::class, 'role'])->name('role.list')->middleware('checkPermission:view');

    Route::get('/create', [RoleController::class, 'createrole'])->name('role.create')->middleware('checkPermission:create');

    Route::get('/{id}', [RoleController::class, 'editrole'])->name('role.edit')->middleware('checkPermission:edit');

    //Route::post('/save-update', [RoleController::class, 'saveUpdateRole'])->name('role.saveUpdate');

	Route::post('/save-update', [RoleController::class, 'saveUpdateRole'])->name('role.saveUpdate')->middleware('checkPermission:create|edit');

    Route::post('/status', [RoleController::class, 'rolestatus'])->name('role.status')->middleware('checkPermission:edit');

	//Route::get('/{id}', [RoleController::class, 'usersByRole'])->name('role.roletype')->middleware('checkPermission:view');

});
