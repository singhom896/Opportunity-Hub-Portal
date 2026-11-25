<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\EventController;


Route::prefix('event')->name('event.')->group(function () {
    Route::get('/', [EventController::class, 'list'])->name('list')->middleware('checkPermission:view');
    Route::get('/create', [EventController::class, 'createEdit'])->name('create')->middleware('checkPermission:create');
    Route::get('/{id}/edit', [EventController::class, 'createEdit'])->name('edit')->middleware('checkPermission:edit');
    Route::post('/save-update', [EventController::class, 'saveUpdate'])->name('saveUpdate')->middleware('checkPermission:create|edit');
    Route::post('/status', [EventController::class, 'changeStatus'])->name('status')->middleware('checkPermission:edit');
    Route::post('/delete', [EventController::class, 'delete'])->name('delete')->middleware('checkPermission:delete');
    Route::get('/states', [EventController::class, 'states'])->name('states')->middleware('checkPermission:view');
    Route::get('/districts', [EventController::class, 'districts'])->name('districts')->middleware('checkPermission:view');
    Route::get('/{id}/preview', [EventController::class, 'preview'])->name('preview')->middleware('checkPermission:view');
    Route::post('/media/upload', [EventController::class, 'uploadMedia'])->name('media.upload')->middleware('checkPermission:edit');
    Route::post('/media/status', [EventController::class, 'mediaStatus'])->name('media.status')->middleware('checkPermission:edit');
    Route::post('/media/delete', [EventController::class, 'deleteMedia'])->name('media.delete')->middleware('checkPermission:delete');
});
