<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\MenuController;



/* Route::prefix('menus')->group(function () {
    Route::get('/', [MenuController::class, 'menus'])->name('menus.list')->middleware('checkPermission:view');
    Route::get('/create', [MenuController::class, 'createmenus'])->name('menus.create')->middleware('checkPermission:create');
    Route::post('/savemenu', [MenuController::class, 'savemenu'])->name('menus.savemenu')->middleware('checkPermission:create');
    Route::get('/{id}', [MenuController::class, 'editmenu'])->name('menus.edit')->middleware('checkPermission:edit');
    Route::post('/update', [MenuController::class, 'updatemenu'])->name('menus.updateMenu')->middleware('checkPermission:edit');
    Route::post('/status', [MenuController::class, 'menustatus'])->name('menus.status')->middleware('checkPermission:edit');
    Route::post('/delete', [MenuController::class, 'menudelete'])->name('menus.delete')->middleware('checkPermission:delete');
    Route::get('/child/{id}', [MenuController::class, 'childmenus'])->name('menus.child.list')->middleware('checkPermission:view');
});
 */

/*  Route::prefix('menus')->group(function () {

    Route::get('/', [MenuController::class, 'menus'])->name('menus.list')->middleware('checkPermission:view');

    Route::get('/create', [MenuController::class, 'createmenus'])->name('menus.create')->middleware('checkPermission:create');

    Route::get('/{id}', [MenuController::class, 'editmenu'])->name('menus.edit')->middleware('checkPermission:edit');

    Route::post('/save-update', [MenuController::class, 'saveUpdateMenu'])->name('menus.saveUpdate')->middleware('checkPermission:create');
	
    Route::post('/status', [MenuController::class, 'menustatus'])->name('menus.status')->middleware('checkPermission:edit');

    Route::post('/delete', [MenuController::class, 'menudelete'])->name('menus.delete')->middleware('checkPermission:delete');

    Route::get('/child/{id}', [MenuController::class, 'childmenus'])->name('menus.child.list')->middleware('checkPermission:view');
}); */

Route::prefix('menus')->group(function () {

    Route::get('/', [MenuController::class, 'menus'])->name('menus.list')->middleware('checkPermission:view');

    Route::get('/create', [MenuController::class, 'createmenus'])->name('menus.create')->middleware('checkPermission:create');

    Route::get('/sorting', [MenuController::class, 'menuSorting'])->name('menus.sorting')->middleware('checkPermission:edit');

    Route::post('/update-position', [MenuController::class, 'updatePosition'])->name('menus.updatePosition')->middleware('checkPermission:edit');

    Route::get('/child/{id}', [MenuController::class, 'childmenus'])->name('menus.child.list')->middleware('checkPermission:view');

    Route::get('/{id}', [MenuController::class, 'editmenu'])->name('menus.edit')->middleware('checkPermission:edit');

    Route::post('/save-update', [MenuController::class, 'saveUpdateMenu'])->name('menus.saveUpdate')->middleware('checkPermission:create');

    Route::post('/status', [MenuController::class, 'menustatus'])->name('menus.status')->middleware('checkPermission:edit');

    Route::post('/delete', [MenuController::class, 'menudelete'])->name('menus.delete')->middleware('checkPermission:delete');
});


