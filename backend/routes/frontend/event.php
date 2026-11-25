<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\EventController;

Route::get('/events', [EventController::class, 'events'])->name('events');
Route::post('/events', [EventController::class, 'events'])->name('events.search');
Route::get('/event-detail/{id}', [EventController::class, 'eventDetail'])->name('event-detail');
