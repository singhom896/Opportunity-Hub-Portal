<?php

use Illuminate\Support\Facades\Route;
use Mews\Captcha\Facades\Captcha;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

require base_path('routes/frontend/home.php');
require base_path('routes/frontend/auth.php');
require base_path('routes/frontend/artistregistration.php');
require base_path('routes/frontend/event.php');
require base_path('routes/frontend/master.php');
require base_path('routes/frontend/email_notification.php');



Route::prefix(config('app.backend_prefix'))->middleware('auth')->group(function () {
	//Route::middleware('auth')->group(function () {

	require base_path('routes/backend/dashboard.php');
	require base_path('routes/backend/artist.php');
	require base_path('routes/backend/banner.php');
	require base_path('routes/backend/document.php');
	require base_path('routes/backend/minister.php');
	require base_path('routes/backend/menu.php');
	require base_path('routes/backend/module.php');
	require base_path('routes/backend/user.php');
	require base_path('routes/backend/role.php');
	require base_path('routes/backend/permission.php');
	require base_path('routes/backend/log.php');
	require base_path('routes/backend/content.php');
    require base_path('routes/backend/gallery.php');
	require base_path('routes/backend/video.php');
	require base_path('routes/backend/testimonial.php');
	require base_path('routes/backend/faqs.php');
	require base_path('routes/backend/event.php');
	require base_path('routes/backend/region.php');
	require base_path('routes/backend/countrie.php');
	require base_path('routes/backend/language.php');
	require base_path('routes/backend/artistinstitution.php');
	require base_path('routes/backend/artist-group.php');
	require base_path('routes/backend/artist-award.php');
	require base_path('routes/backend/artfrom.php');
	

});



