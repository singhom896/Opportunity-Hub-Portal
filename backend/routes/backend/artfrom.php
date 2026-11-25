<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ArtFromController;

Route::prefix('art-forms')->name('cp.art-forms.')->group(function () {
    Route::get('/', [ArtFromController::class, 'list'])->name('list')->middleware('checkPermission:view');
    Route::get('/create', [ArtFromController::class, 'createEditData'])->name('create')->middleware('checkPermission:create');
    Route::post('/save-update', [ArtFromController::class, 'saveUpdateData'])->name('saveUpdate')->middleware('checkPermission:create');
    Route::post('/status', [ArtFromController::class, 'statusChange'])->name('status')->middleware('checkPermission:edit');
    Route::get('/{id}/edit', [ArtFromController::class, 'createEditData'])->name('edit')->middleware('checkPermission:edit');
    Route::post('/country-search', [ArtFromController::class, 'list'])->name('countrySearch')->middleware('checkPermission:view');
    Route::get('/parent-categories/{countryId}', [ArtFromController::class, 'parentCategories'])->name('parentCategories')->middleware('checkPermission:view');
});
