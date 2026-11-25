<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\BannerController;


// Start Banner Route
Route::prefix('banners')->group(function () {
    Route::get('/', [BannerController::class, 'banners'])->name('banners.list')->middleware('checkPermission:view');
    Route::get('/create', [BannerController::class, 'create'])->name('banners.create')->middleware('checkPermission:create');
    Route::post('/createbanner', [BannerController::class, 'createbanner'])->name('banners.createbanner')->middleware('checkPermission:create');
    Route::get('/{id}', [BannerController::class, 'editbanner'])->name('banners.edit')->middleware('checkPermission:edit');
    Route::post('/updatebanner', [BannerController::class, 'updatebanner'])->name('banners.updatebanner')->middleware('checkPermission:edit');
    Route::post('/bannerstatus', [BannerController::class, 'bannerstatus'])->name('banners.status')->middleware('checkPermission:edit');
    Route::post('/delete', [BannerController::class, 'bannerdelete'])->name('banners.delete')->middleware('checkPermission:delete');
});
// End Banner Route