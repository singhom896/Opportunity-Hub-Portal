<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\PermissionController;

Route::prefix('permissions')->group(function () {
    Route::get('/', [PermissionController::class, 'permissionsList'])->name('permissions.list')->middleware('checkPermission:view');
    Route::post('/', [PermissionController::class, 'permissionsList'])->name('permissions.filter')->middleware('checkPermission:view');
    Route::get('/create', [PermissionController::class, 'createpermission'])->name('permissions.create')->middleware('checkPermission:create');
    Route::post('/save', [PermissionController::class, 'savepermissions'])->name('permissions.save')->middleware('checkPermission:create');
    Route::post('/status', [PermissionController::class, 'permissionstatus'])->name('permissions.status')->middleware('checkPermission:edit');
	Route::get('/{role_id}', [PermissionController::class, 'permissionsList'])->name('permissions.role')->middleware('checkPermission:view');
});

