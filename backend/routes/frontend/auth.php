<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('student.register');

Route::get('/activate-account/{email}', [AuthController::class, 'activate'])->name('activate.account');


Route::get('/captcha-refresh', function () {
    return response()->json([
        'captcha' => captcha_img(),
    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
      ->header('Pragma', 'no-cache');
})->name('captcha.refresh');



Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->middleware(['guest', 'throttle:50,1'])->name('frontend.password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->middleware(['guest', 'throttle:50,1'])->name('frontend.password.email');
 
//Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->middleware('guest')->name('frontend.password.reset');

// Laravel Password Broker requires this exact route name
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->middleware('guest')->name('password.reset');

 
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware(['guest', 'throttle:50,1'])->name('frontend.password.update');
