<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CountrieController extends Controller
{
    public function list(Request $request)
    {
        $countries = DB::table('countries')
			->where('brics_flag', 1)
            ->orderBy('name', 'ASC')
            ->get();

        return view('backend.countries.list', compact('countries'));
    }

		 public function statusChange(Request $request)
		{
			$authRoleId = (int) authUserData('role_id');
			$authCountryId = authUserData('country_id');

			$validated = $request->validate([
				'id' => ['required', 'integer', 'exists:countries,id'],
				'status' => ['required', 'in:0,1'],
			]);

			// Update country status
			$countryQuery = DB::table('countries')
				->where('id', $validated['id']);

			// Role 8 can update only its own country
			if ($authRoleId === 8) {
				abort_if(
					empty($authCountryId),
					403,
					'Your account is not linked to a country.'
				);

				$countryQuery->where('id', $authCountryId);
			}

			abort_unless($countryQuery->exists(), 404, 'Country not found.');

			$data = [
				'status' => (int) $validated['status'],
			];

			if (Schema::hasColumn('countries', 'updated_at')) {
				$data['updated_at'] = now();
			}

			$countryQuery->update($data);

			return response()->json([
				'success' => true,
				'status' => (int) $validated['status'],
				'message' => $validated['status']
					? 'Country activated successfully.'
					: 'Country deactivated successfully.',
			]);
		}
}
