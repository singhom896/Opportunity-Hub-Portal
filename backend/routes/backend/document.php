<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DocumentController;

// Start Documents Route
Route::prefix('documents')->group(function () {

    Route::get('/', [DocumentController::class, 'documents'])
        ->name('documents.list')
        ->middleware('checkPermission:view');

    Route::get('/create', [DocumentController::class, 'createEdit'])
        ->name('documents.create')
        ->middleware('checkPermission:create');

    Route::get('/{id}/edit', [DocumentController::class, 'createEdit'])
        ->name('documents.edit')
        ->middleware('checkPermission:edit');

    Route::post('/save-update', [DocumentController::class, 'saveUpdate'])
        ->name('documents.saveUpdate')
        ->middleware('checkPermission:create|edit');

    Route::post('/status', [DocumentController::class, 'documentstatus'])
        ->name('documents.status')
        ->middleware('checkPermission:edit');

    Route::post('/delete', [DocumentController::class, 'documentsdelete'])
        ->name('documents.delete')
        ->middleware('checkPermission:delete');
});

// End Documents Route
