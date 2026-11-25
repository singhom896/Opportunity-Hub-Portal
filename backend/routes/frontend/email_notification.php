<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\EmailNotificationController;

// Public email OTP routes. Keep these outside admin/auth-only route groups.
Route::prefix('email/otp')->name('email.otp.')->group(function () {
    Route::post('/send', [EmailNotificationController::class, 'sendOtp'])
        ->middleware('throttle:50,1')
        ->name('send');

    Route::post('/resend', [EmailNotificationController::class, 'resendOtp'])
        ->middleware('throttle:30,1')
        ->name('resend');

    Route::post('/verify', [EmailNotificationController::class, 'verifyOtp'])
        ->middleware('throttle:100,1')
        ->name('verify');
});
