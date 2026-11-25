<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ArtistGroupController;

Route::prefix('artist-groups')->name('cp.artist-groups.')->group(function () {
    Route::get('/', [ArtistGroupController::class, 'list'])->name('list')->middleware('checkPermission:view');
    Route::get('/create', [ArtistGroupController::class, 'createEditData'])->name('create')->middleware('checkPermission:create');
    Route::post('/save-update', [ArtistGroupController::class, 'saveUpdateData'])->name('saveUpdate')->middleware('checkPermission:create');
    Route::post('/status', [ArtistGroupController::class, 'statusChange'])->name('status')->middleware('checkPermission:edit');
    Route::get('/{id}/edit', [ArtistGroupController::class, 'createEditData'])->name('edit')->middleware('checkPermission:edit');
    Route::post('/country-search', [ArtistGroupController::class, 'list'])->name('countrySearch')->middleware('checkPermission:view');
});
