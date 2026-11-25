<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\MinisterController;



Route::prefix('ministers')->group(function () {
    Route::get('/', [MinisterController::class, 'ministers'])->name('ministers.list')->middleware('checkPermission:view');
    Route::get('/create', [MinisterController::class, 'createminister'])->name('ministers.create')->middleware('checkPermission:create');
    Route::post('/createbanner', [MinisterController::class, 'saveminister'])->name('ministers.createbanner')->middleware('checkPermission:create');
    Route::get('/{id}', [MinisterController::class, 'editminister'])->name('ministers.edit')->middleware('checkPermission:edit');
    Route::post('/updatebanner', [MinisterController::class, 'updateminister'])->name('ministers.updateminister')->middleware('checkPermission:edit');
    Route::post('/status', [MinisterController::class, 'ministerstatus'])->name('ministers.status')->middleware('checkPermission:edit');
    Route::post('/delete', [MinisterController::class, 'ministerdelete'])->name('ministers.delete')->middleware('checkPermission:delete');
});

