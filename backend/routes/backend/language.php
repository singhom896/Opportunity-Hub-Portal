<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\LanguagesController;

Route::prefix('/artist-languages')->name('cp.languages.')->group(function () {
    Route::get('/', [LanguagesController::class, 'list'])->name('list')->middleware('checkPermission:view');
    Route::get('/create', [LanguagesController::class, 'createEditData'])->name('create')->middleware('checkPermission:create');
    Route::post('/save-update', [LanguagesController::class, 'saveUpdateData'])->name('saveUpdate')->middleware('checkPermission:create');
    Route::post('/status', [LanguagesController::class, 'statusChange'])->name('status')->middleware('checkPermission:edit');
    Route::get('/{id}/edit', [LanguagesController::class, 'createEditData'])->name('edit')->middleware('checkPermission:edit');
    Route::post('/country-search', [LanguagesController::class, 'list'])->name('countrySearch')->middleware('checkPermission:view');
});
