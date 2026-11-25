<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\VideoController;


Route::prefix('video')->group(function () {
    Route::get('/', [VideoController::class, 'list'])->name('video.list')->middleware('checkPermission:view');
    Route::get('/create', [VideoController::class, 'createEdit'])->name('video.create')->middleware('checkPermission:create');
    Route::get('/{id}/edit', [VideoController::class, 'createEdit'])->name('video.edit')->middleware('checkPermission:edit');
    Route::post('/save-update', [VideoController::class, 'saveUpdate'])->name('video.saveUpdate')->middleware('checkPermission:create|edit');
    Route::post('/status', [VideoController::class, 'changeStatus'])->name('video.status')->middleware('checkPermission:edit');
    Route::post('/delete', [VideoController::class, 'delete'])->name('video.delete')->middleware('checkPermission:delete');
});
