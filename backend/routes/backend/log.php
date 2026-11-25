<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\LogController;


Route::prefix('logs')->group(function () {
    Route::get('/', [LogController::class, 'logs'])->name('logs.list')->middleware('checkPermission:view');
    
});