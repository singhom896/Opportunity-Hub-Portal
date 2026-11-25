<?php
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\State;






if (php_sapi_name() !== 'cli') {
	/* $allowed_hosts = array('10.246.21.219', 'localhost','127.0.0.1','scw.dosje.gov.in','164.100.77.235','10.22.13.213');
	if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
		header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
		// abort(400, 'Invalid request.');
		exit;
	}  */
}



if (!function_exists('artistProfileLabels')) {
    function artistProfileLabels(): array
    {
        return [
            'individual' => [
                'title'    => 'Artist Profile',
                'role'     => 'Artist',
                'about'    => 'About the Artist',
                'verified' => 'Verified Artist',
                'aboutSub' => "Discover the artist's background and creative journey.",
            ],
            'group' => [
                'title'    => 'Group Profile',
                'role'     => 'Artist Group',
                'about'    => 'About the Group',
                'verified' => 'Verified Group',
                'aboutSub' => "Discover the group's background and creative journey.",
            ],
            'institution' => [
                'title'    => 'Institution Profile',
                'role'     => 'Institute / Organization ',
                'about'    => 'About the Institution',
                'verified' => 'Verified Institution',
                'aboutSub' => "Discover the institution's background and activities.",
            ],
        ];
    }
}

if (!function_exists('artistCategoryOptions')) {
    function artistCategoryOptions(): array
    {
        return [
            '5' => 'Artist',
            '6' => 'Artist Group',
            '7' => 'Institute / Organization',
        ];
    }
}


