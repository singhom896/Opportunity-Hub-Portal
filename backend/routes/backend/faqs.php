<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\FaqsController;


Route::prefix('faqs')->group(function () {
    Route::get('/', [FaqsController::class, 'list'])->name('faqs.list')->middleware('checkPermission:view');
    Route::get('/create', [FaqsController::class, 'createEdit'])->name('faqs.create')->middleware('checkPermission:create');
    Route::get('/{id}/edit', [FaqsController::class, 'createEdit'])->name('faqs.edit')->middleware('checkPermission:edit');
    Route::post('/save-update', [FaqsController::class, 'saveUpdate'])->name('faqs.saveUpdate')->middleware('checkPermission:create');
    Route::post('/status', [FaqsController::class, 'changeStatus'])->name('faqs.status')->middleware('checkPermission:edit');

});