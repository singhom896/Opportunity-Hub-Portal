<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ContentController;
use App\Http\Controllers\Frontend\DocumentController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/districts/{state}', [HomeController::class, 'getDistricts']);

Route::get('/sitemap', [HomeController::class, 'sitemap'])->name('sitemap');

Route::get('content/{slug}', [HomeController::class, 'content']);

Route::get('/document', [HomeController::class, 'document'])->name('document');

Route::get('/about-us', [HomeController::class, 'aboutbrics'])->name('aboutbrics');

Route::match(['get', 'post'], '/art-forms', [HomeController::class, 'artforms'])->name('artforms');

Route::get('/bulletin', [HomeController::class, 'bulletin'])->name('bulletin');

Route::get('/countries', [HomeController::class, 'countries'])->name('countries');

Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');

Route::get('/contact-us', [HomeController::class, 'contactus'])->name('contact-us');

Route::get('/clearit', [HomeController::class, 'clearit'])->name('clearit');

Route::get('/clearitall', [HomeController::class, 'clearitAll'])->name('clearitAll');





Route::get('/scheme-guideline', [DocumentController::class, 'whatsnew'])->name('guideline');
Route::get('/apply', [DocumentController::class, 'whatsnew'])->name('apply');
Route::get('/faq', [DocumentController::class, 'whatsnew'])->name('faq');
Route::get('/results', [DocumentController::class, 'whatsnew'])->name('results');
Route::get('/forms', [DocumentController::class, 'whatsnew'])->name('forms');
Route::get('/whatsnew', [DocumentController::class, 'whatsnew'])->name('whatsnew');
Route::get('/yajraData', [DocumentController::class, 'yajraData'])->name('yajraData');



