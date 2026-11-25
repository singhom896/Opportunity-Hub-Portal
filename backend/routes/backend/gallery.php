<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\GalleryController;


Route::prefix('gallery')->group(function () {
    Route::get('/', [GalleryController::class, 'list'])->name('gallery.list')->middleware('checkPermission:view');
    Route::get('/create', [GalleryController::class, 'createEdit'])->name('gallery.create')->middleware('checkPermission:create');
    Route::get('/{id}/edit', [GalleryController::class, 'createEdit'])->name('gallery.edit')->middleware('checkPermission:edit');
    Route::post('/save-update', [GalleryController::class, 'saveUpdate'])->name('gallery.saveUpdate')->middleware('checkPermission:create|edit');
    Route::post('/status', [GalleryController::class, 'changeStatus'])->name('gallery.status')->middleware('checkPermission:edit');
    Route::post('/delete', [GalleryController::class, 'delete'])->name('gallery.delete')->middleware('checkPermission:delete');
});
