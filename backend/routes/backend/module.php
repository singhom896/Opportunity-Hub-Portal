<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ModuleController;


/* Route::prefix('modules')->group(function () {
    Route::get('/', [ModuleController::class, 'modules'])->name('modules.list')->middleware('checkPermission:view');
    Route::get('/child/{id}', [ModuleController::class, 'childmodules'])->name('modules.child.list')->middleware('checkPermission:view');
    Route::get('/create', [ModuleController::class, 'createmodules'])->name('modules.create')->middleware('checkPermission:create');
    Route::post('/savemodules', [ModuleController::class, 'savemodules'])->name('modules.savemodules')->middleware('checkPermission:create');
    Route::get('/{id}', [ModuleController::class, 'editmodules'])->name('modules.edit')->middleware('checkPermission:edit');
    Route::post('/update', [ModuleController::class, 'updatemodule'])->name('modules.updatemodule')->middleware('checkPermission:edit');
    Route::post('/status', [ModuleController::class, 'modulesstatus'])->name('modules.status')->middleware('checkPermission:edit');
    Route::post('/delete', [ModuleController::class, 'modulesdelete'])->name('modules.delete')->middleware('checkPermission:delete');

	 Route::get('/sorting', [ModuleController::class, 'moduleSorting'])->name('modules.sorting')->middleware('checkPermission:edit');
    Route::post('/update-position', [ModuleController::class, 'updatePosition'])->name('modules.updatePosition')->middleware('checkPermission:edit');


}); */

/* Route::prefix('modules')->group(function () {
    Route::get('/', [ModuleController::class, 'modules'])->name('modules.list')->middleware('checkPermission:view');
    Route::get('/child/{id}', [ModuleController::class, 'childmodules'])->name('modules.child.list')->middleware('checkPermission:view');
    Route::get('/create', [ModuleController::class, 'createmodules'])->name('modules.create')->middleware('checkPermission:create');

    Route::get('/sorting', [ModuleController::class, 'moduleSorting'])->name('modules.sorting')->middleware('checkPermission:edit');
    Route::post('/update-position', [ModuleController::class, 'updatePosition'])->name('modules.updatePosition')->middleware('checkPermission:edit');

    Route::post('/savemodules', [ModuleController::class, 'savemodules'])->name('modules.savemodules')->middleware('checkPermission:create');
    Route::get('/{id}', [ModuleController::class, 'editmodules'])->name('modules.edit')->middleware('checkPermission:edit');
    Route::post('/update', [ModuleController::class, 'updatemodule'])->name('modules.updatemodule')->middleware('checkPermission:edit');
    Route::post('/status', [ModuleController::class, 'modulesstatus'])->name('modules.status')->middleware('checkPermission:edit');
    Route::post('/delete', [ModuleController::class, 'modulesdelete'])->name('modules.delete')->middleware('checkPermission:delete');
}); */

Route::prefix('modules')->group(function () {
    Route::get('/', [ModuleController::class, 'modules'])->name('modules.list')->middleware('checkPermission:view');

    Route::get('/child/{id}', [ModuleController::class, 'childmodules'])->name('modules.child.list')->middleware('checkPermission:view');

    Route::get('/create', [ModuleController::class, 'createmodules'])->name('modules.create')->middleware('checkPermission:create');

    Route::get('/sorting', [ModuleController::class, 'moduleSorting'])->name('modules.sorting')->middleware('checkPermission:edit');

    Route::post('/update-position', [ModuleController::class, 'updatePosition'])->name('modules.updatePosition')->middleware('checkPermission:edit');

    Route::post('/save-update', [ModuleController::class, 'saveUpdateModule'])->name('modules.saveUpdate')->middleware('checkPermission:create');

    Route::get('/{id}', [ModuleController::class, 'editmodules'])->name('modules.edit')->middleware('checkPermission:edit');

    Route::post('/status', [ModuleController::class, 'modulesstatus'])->name('modules.status')->middleware('checkPermission:edit');

    Route::post('/delete', [ModuleController::class, 'modulesdelete'])->name('modules.delete')->middleware('checkPermission:delete');
});
