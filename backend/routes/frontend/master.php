<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\MasterController;


Route::get('/districts-by-state', [MasterController::class, 'getDistrictsByState'])->name('common.districts-by-state');
Route::get('/districts-by-state', [MasterController::class, 'getDistrictsByState'])->name('common.districts-by-state');


Route::prefix('master')->name('master')->group(function () {

Route::get('/regions-by-country/{country}', [ MasterController::class,'regionsByCountry'])->whereNumber('country')->name('regionsByCountry');


});

