<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ArtistController;
use App\Http\Controllers\Frontend\ArtistRegistrationController;
use App\Http\Controllers\Frontend\ArtistRegistrationCorrectionController;

Route::match(['get', 'post'], '/artist-list', [ArtistController::class, 'artistslist'])->name('artist.list');

Route::get('/artist-list/{id}', [ArtistController::class, 'artistslist'])->where('id', '.*')->name('artist.list.country');

Route::post('/artist/art-forms', [ArtistController::class, 'artFormsByCountry'])->name('artist.art-forms');


Route::get('/artists-detail/{id}', [ArtistController::class, 'artistsdetail'])->where('id', '.*')->name('artist.detail');


Route::get('/artists-detail', fn() => redirect()->route('artist.list'));


Route::get('/artists-detail/{artist}/photo', [ArtistController::class, 'profilePhoto'])->whereNumber('artist')->name('artist.photo');


Route::get('/artist-registration', [ArtistRegistrationController::class, 'artistregistration'])->name('artist.registration');

Route::post('/artist-registration', [ArtistRegistrationController::class, 'store'])->name('artistregistration.store');

Route::get('/artist-registration/countries/{country}/regions', [ArtistRegistrationController::class, 'regions'])->whereNumber('country')->name('artist-registration.regions');

Route::get('/artist-registration/countries/{country}/art-forms', [ArtistRegistrationController::class, 'artForms'])->whereNumber('country')->name('artist-registration.art-forms');

Route::get('/artist-registration/countries/{country}/art-forms/{artForm}/subgenres', [ArtistRegistrationController::class, 'subgenres'])->whereNumber('country')->whereNumber('artForm')->name('artist-registration.subgenres');

Route::get('/artist-account/password-setup/{token}', [ArtistRegistrationController::class, 'showPasswordSetup'])->name('artist-account.password.edit');
	
Route::post('/artist-account/password-setup/{token}', [ArtistRegistrationController::class, 'setPassword'])->name('artist-account.password.update');

Route::middleware('auth')->group(function () {
	Route::get('/artist-registration/{registration}/edit', [ArtistRegistrationCorrectionController::class, 'edit'])->whereNumber('registration')->name('artist-registration.correction.edit');
	Route::post('/artist-registration/{registration}/resubmit', [ArtistRegistrationCorrectionController::class, 'resubmit'])->whereNumber('registration')->name('artist-registration.correction.resubmit');
});
