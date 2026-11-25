<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\CountrieController;

Route::prefix('countries')->name('cp.countrie.')->group(function () {
    Route::get('/', [CountrieController::class, 'list'])->name('list')->middleware('checkPermission:view');
    Route::post('/status', [CountrieController::class, 'statusChange'])->name('status')->middleware('checkPermission:edit');

});
