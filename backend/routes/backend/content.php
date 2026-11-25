<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ContentController;

Route::prefix('content')->group(function () {

    Route::get('/', [ContentController::class, 'list'])
        ->name('content.list')
        ->middleware('checkPermission:view');

    Route::get('/create', [ContentController::class, 'createEdit'])
        ->name('content.create')
        ->middleware('checkPermission:create');

    Route::get('/{id}/edit', [ContentController::class, 'createEdit'])
        ->name('content.edit')
        ->middleware('checkPermission:edit');

    Route::post('/save-update', [ContentController::class, 'saveUpdate'])
        ->name('content.saveUpdate')
        ->middleware('checkPermission:create|edit');

    Route::post('/check', [ContentController::class, 'checkContentById'])
        ->name('content.check')
        ->middleware('checkPermission:create|edit');

    Route::post('/status', [ContentController::class, 'changeStatus'])
        ->name('content.status')
        ->middleware('checkPermission:edit');
});
