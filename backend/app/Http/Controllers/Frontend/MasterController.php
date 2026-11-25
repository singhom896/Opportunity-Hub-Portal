<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{

    public function getDistrictsByState(Request $request)
    {
        $districts = getDistrictsByStateId($request->state_code);

        return response()->json($districts);
    }
	
	/** Return active regions/provinces for the selected country. */
    public function regionsByCountry($country)
    {
        $regions = DB::table('art_regions')
            ->where('country_id', $country)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($regions);
    }
}
