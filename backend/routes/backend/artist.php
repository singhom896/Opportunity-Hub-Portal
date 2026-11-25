<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ArtistRegistrationBackendController;
use App\Http\Controllers\Backend\ArtistImportController;


//Route::prefix('artist')->name('artist.')->group(function () {
	
	Route::prefix('artist')->name('cp.artist.')->group(function () {

    Route::match(['get', 'post'], '/', [ArtistRegistrationBackendController::class, 'list'])->name('list')->middleware('checkPermission:view');
    Route::get('/create', [ArtistRegistrationBackendController::class, 'createEdit'])->name('create')->middleware('checkPermission:create');
    Route::get('/{id}/edit', [ArtistRegistrationBackendController::class, 'createEdit'])->name('edit')->middleware('checkPermission:edit');
    Route::post('/save-update', [ArtistRegistrationBackendController::class, 'saveUpdate'])->name('saveUpdate')->middleware('checkPermission:create|edit');
    Route::post('/status', [ArtistRegistrationBackendController::class, 'changeStatus'])->name('status')->middleware('checkPermission:edit');
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [ArtistImportController::class, 'index'])->name('index')->middleware('checkPermission:create');
        Route::get('/template/{format}', [ArtistImportController::class, 'template'])->name('template')->middleware('checkPermission:create');
        Route::post('/preview', [ArtistImportController::class, 'preview'])->name('preview')->middleware('checkPermission:create');
        Route::get('/{token}', [ArtistImportController::class, 'show'])->name('show')->whereUuid('token')->middleware('checkPermission:create');
        Route::post('/{token}/process', [ArtistImportController::class, 'process'])->name('process')->whereUuid('token')->middleware('checkPermission:create');
        Route::get('/{token}/report', [ArtistImportController::class, 'report'])->name('report')->whereUuid('token')->middleware('checkPermission:create');
    });
    Route::get('/{id}/preview', [ArtistRegistrationBackendController::class, 'preview'])->name('preview')->middleware('checkPermission:view');

	
});
