<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ArtistAwardController;

Route::prefix('artist-awards')->name('cp.artist-awards.')->group(function () {
    Route::get('/', [ArtistAwardController::class, 'list'])->name('list')->middleware('checkPermission:view');
    Route::get('/create', [ArtistAwardController::class, 'createEditData'])->name('create')->middleware('checkPermission:create');
    Route::post('/save-update', [ArtistAwardController::class, 'saveUpdateData'])->name('saveUpdate')->middleware('checkPermission:create');
    Route::post('/status', [ArtistAwardController::class, 'statusChange'])->name('status')->middleware('checkPermission:edit');
    Route::get('/{id}/edit', [ArtistAwardController::class, 'createEditData'])->name('edit')->middleware('checkPermission:edit');
    Route::post('/country-search', [ArtistAwardController::class, 'list'])->name('countrySearch')->middleware('checkPermission:view');
});
