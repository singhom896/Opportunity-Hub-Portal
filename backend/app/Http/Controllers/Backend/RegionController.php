<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    public function list(Request $request, $country_id = null)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $countries = DB::table('countries')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        // Role 8 is restricted to its own country; ignore any URL country filter.
        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');

            $selectedCountry = (int) $authCountryId;
            $regions = DB::table('art_regions')
                ->leftJoin('countries', 'countries.id', '=', 'art_regions.country_id')
                ->select('art_regions.*', 'countries.name as country_name')
                ->where('art_regions.country_id', $authCountryId)
                ->orderByDesc('art_regions.id')
                ->get();
        } else {
            // Search filter comes from POST form; route parameter remains supported if used by another link.
            $selectedCountry = $request->input('country_id', $country_id);
            $regions = DB::table('art_regions')
                ->leftJoin('countries', 'countries.id', '=', 'art_regions.country_id')
                ->select('art_regions.*', 'countries.name as country_name')
                ->when($selectedCountry, fn ($q) => $q->where('art_regions.country_id', $selectedCountry))
                ->orderByDesc('art_regions.id')
                ->get();
        }

        return view('backend.regions.list', compact('regions', 'countries', 'selectedCountry', 'authRoleId', 'authCountryId'));
    }

    public function createEditData($id = null)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $region = null;
        if ($id) {
            $regionId = decrypt($id);
            $regionQuery = DB::table('art_regions')->where('id', $regionId);

            if ($authRoleId === 8) {
                abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
                $regionQuery->where('country_id', $authCountryId);
            }

            $region = $regionQuery->first();
            abort_unless($region, 404);
        }

        $countries = DB::table('countries')->where('status', 1)->orderBy('name')->pluck('name', 'id');

        return view('backend.regions.createEdit', compact('region', 'countries', 'authRoleId', 'authCountryId'));
    }

    public function saveUpdateData(Request $request)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');
        $id = $request->input('id');

        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'exists:art_regions,id'],
            'country_id' => [$authRoleId === 8 ? 'nullable' : 'required', 'integer', Rule::exists('countries', 'id')->where('status', 1)],
            'name' => ['required', 'string', 'max:150'],
            'short_name' => ['nullable', 'string', 'max:150'],
            'region_code' => ['nullable', 'integer'],
            'region_type' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:0,1'],
        ], [
            'country_id.required' => 'Please select a country.',
            'name.required' => 'Please enter region/state/province name.',
            'status.required' => 'Please select status.',
        ]);

        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            $countryId = (int) $authCountryId;
        } else {
            $countryId = (int) $validated['country_id'];
        }

        $data = [
            'country_id' => $countryId,
            'name' => trim($validated['name']),
            'status' => (int) $validated['status'],
        ];

        foreach (['short_name', 'region_code', 'region_type'] as $column) {
            if (Schema::hasColumn('art_regions', $column)) {
                $value = $validated[$column] ?? null;
                $data[$column] = $column === 'short_name' || $column === 'region_type'
                    ? (is_string($value) ? trim($value) : $value)
                    : $value;
            }
        }

        if ($id) {
            $existingRegion = DB::table('art_regions')->where('id', $id);
            if ($authRoleId === 8) {
                $existingRegion->where('country_id', $authCountryId);
            }
            abort_unless($existingRegion->exists(), 404);
            $existingRegion->update($data);
            $message = 'Region updated successfully.';
        } else {
            $data['created_at'] = now();
            if (Schema::hasColumn('art_regions', 'updated_at')) $data['updated_at'] = now();
            DB::table('art_regions')->insert($data);
            $message = 'Region created successfully.';
        }

        return redirect()->route('cp.regions.list')->with('success', $message);
    }

    public function statusChange(Request $request)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:art_regions,id'],
            'status' => ['required', 'in:0,1'],
        ]);

        $regionQuery = DB::table('art_regions')->where('id', $validated['id']);
        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            $regionQuery->where('country_id', $authCountryId);
        }
        abort_unless($regionQuery->exists(), 404);

        $data = ['status' => (int) $validated['status']];
        if (Schema::hasColumn('art_regions', 'updated_at')) $data['updated_at'] = now();
        $regionQuery->update($data);

        return response()->json([
            'success' => true,
            'status' => (int) $validated['status'],
            'message' => $validated['status'] ? 'Region activated successfully.' : 'Region deactivated successfully.',
        ]);
    }
}
