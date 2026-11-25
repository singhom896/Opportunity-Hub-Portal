<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ArtistGroupController extends Controller
{
    public function list(Request $request, $country_id = null)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $countries = DB::table('countries')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');

            $selectedCountry = (int) $authCountryId;

            $groupTypes = DB::table('art_group_types')
                ->leftJoin('countries', 'countries.id', '=', 'art_group_types.country_id')
                ->select('art_group_types.*', 'countries.name as country_name')
                ->where('art_group_types.country_id', $authCountryId)
                ->orderByDesc('art_group_types.id')
                ->get();
        } else {
            $selectedCountry = $request->input('country_id', $country_id);

            $groupTypes = DB::table('art_group_types')
                ->leftJoin('countries', 'countries.id', '=', 'art_group_types.country_id')
                ->select('art_group_types.*', 'countries.name as country_name')
                ->when($selectedCountry, fn ($q) => $q->where('art_group_types.country_id', $selectedCountry))
                ->orderByDesc('art_group_types.id')
                ->get();
        }

        return view('backend.artist-groups.list', compact(
            'groupTypes',
            'countries',
            'selectedCountry',
            'authRoleId',
            'authCountryId'
        ));
    }

    public function createEditData($id = null)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $groupType = null;

        if ($id) {
            $groupTypeId = decrypt($id);
            $groupTypeQuery = DB::table('art_group_types')
                ->where('id', $groupTypeId);

            if ($authRoleId === 8) {
                abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
                $groupTypeQuery->where('country_id', $authCountryId);
            }

            $groupType = $groupTypeQuery->first();
            abort_unless($groupType, 404);
        }

        $countries = DB::table('countries')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('backend.artist-groups.createEdit', compact(
            'groupType',
            'countries',
            'authRoleId',
            'authCountryId'
        ));
    }

    public function saveUpdateData(Request $request)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');
        $id = $request->input('id');

        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'exists:art_group_types,id'],
            'country_id' => [
                $authRoleId === 8 ? 'nullable' : 'required',
                'integer',
                Rule::exists('countries', 'id')->where('status', 1),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:0,1'],
        ], [
            'country_id.required' => 'Please select a country.',
            'name.required' => 'Please enter artist group type name.',
            'description.max' => 'Description cannot exceed 255 characters.',
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
            'description' => isset($validated['description'])
                ? trim($validated['description'])
                : null,
            'status' => (int) $validated['status'],
        ];

        if ($id) {
            $existingType = DB::table('art_group_types')->where('id', $id);

            if ($authRoleId === 8) {
                $existingType->where('country_id', $authCountryId);
            }

            abort_unless($existingType->exists(), 404);
            $existingType->update($data);
            $message = 'Artist Group type updated successfully.';
        } else {
            $data['created_at'] = now();

            if (Schema::hasColumn('art_group_types', 'updated_at')) {
                $data['updated_at'] = now();
            }

            DB::table('art_group_types')->insert($data);
            $message = 'Artist Group type created successfully.';
        }

        return redirect()->route('cp.artist-groups.list')->with('success', $message);
    }

    public function statusChange(Request $request)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:art_group_types,id'],
            'status' => ['required', 'in:0,1'],
        ]);

        $typeQuery = DB::table('art_group_types')
            ->where('id', $validated['id']);

        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            $typeQuery->where('country_id', $authCountryId);
        }

        abort_unless($typeQuery->exists(), 404);

        $data = ['status' => (int) $validated['status']];

        if (Schema::hasColumn('art_group_types', 'updated_at')) {
            $data['updated_at'] = now();
        }

        $typeQuery->update($data);

        return response()->json([
            'success' => true,
            'status' => (int) $validated['status'],
            'message' => $validated['status']
                ? 'Artist Group type activated successfully.'
                : 'Artist Group type deactivated successfully.',
        ]);
    }
}
