<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\RegionController;

Route::prefix('regions')->name('cp.regions.')->group(function () {
    Route::get('/', [RegionController::class, 'list'])->name('list')->middleware('checkPermission:view');
    Route::get('/create', [RegionController::class, 'createEditData'])->name('create')->middleware('checkPermission:create');
    Route::post('/save-update', [RegionController::class, 'saveUpdateData'])->name('saveUpdate')->middleware('checkPermission:create');
    Route::post('/status', [RegionController::class, 'statusChange'])->name('status')->middleware('checkPermission:edit');
    Route::get('/{id}/edit', [RegionController::class, 'createEditData'])->name('edit')->middleware('checkPermission:edit');
    //Route::get('/country/{country_id}', [RegionController::class, 'list'])->name('countryId')->middleware('checkPermission:view');
	
	// Country filter search (POST)
	Route::post('/country-search', [RegionController::class, 'list'])->name('countrySearch')->middleware('checkPermission:view');




});
