<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\TestimonialController;



Route::prefix('testimonial')->group(function () {
    Route::get('/', [TestimonialController::class, 'list'])
        ->name('testimonial.list')
        ->middleware('checkPermission:view');

    Route::get('/create', [TestimonialController::class, 'createEdit'])
        ->name('testimonial.create')
        ->middleware('checkPermission:create');

    Route::get('/{id}/edit', [TestimonialController::class, 'createEdit'])
        ->name('testimonial.edit')
        ->middleware('checkPermission:edit');

    Route::post('/save-update', [TestimonialController::class, 'saveUpdate'])
        ->name('testimonial.saveUpdate')
        ->middleware('checkPermission:create|edit');

    Route::post('/status', [TestimonialController::class, 'changeStatus'])
        ->name('testimonial.status')
        ->middleware('checkPermission:edit');

    Route::post('/delete', [TestimonialController::class, 'delete'])
        ->name('testimonial.delete')
        ->middleware('checkPermission:delete');
});
