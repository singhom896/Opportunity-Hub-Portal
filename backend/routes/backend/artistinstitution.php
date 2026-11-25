<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ArtistInstitutionController;

Route::prefix('artist-institution')->name('cp.artist-institution.')->group(function () {
    Route::get('/', [ArtistInstitutionController::class, 'list'])->name('list')->middleware('checkPermission:view');
    Route::get('/create', [ArtistInstitutionController::class, 'createEditData'])->name('create')->middleware('checkPermission:create');
    Route::post('/save-update', [ArtistInstitutionController::class, 'saveUpdateData'])->name('saveUpdate')->middleware('checkPermission:create');
    Route::post('/status', [ArtistInstitutionController::class, 'statusChange'])->name('status')->middleware('checkPermission:edit');
    Route::get('/{id}/edit', [ArtistInstitutionController::class, 'createEditData'])->name('edit')->middleware('checkPermission:edit');
    Route::post('/country-search', [ArtistInstitutionController::class, 'list'])->name('countrySearch')->middleware('checkPermission:view');
});
