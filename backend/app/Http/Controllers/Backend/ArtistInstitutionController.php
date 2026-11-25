<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ArtistInstitutionController extends Controller
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

            $institutionTypes = DB::table('art_institution_types')
                ->leftJoin('countries', 'countries.id', '=', 'art_institution_types.country_id')
                ->select('art_institution_types.*', 'countries.name as country_name')
                ->where('art_institution_types.country_id', $authCountryId)
                ->orderByDesc('art_institution_types.id')
                ->get();
        } else {
            $selectedCountry = $request->input('country_id', $country_id);

            $institutionTypes = DB::table('art_institution_types')
                ->leftJoin('countries', 'countries.id', '=', 'art_institution_types.country_id')
                ->select('art_institution_types.*', 'countries.name as country_name')
                ->when($selectedCountry, fn ($q) => $q->where('art_institution_types.country_id', $selectedCountry))
                ->orderByDesc('art_institution_types.id')
                ->get();
        }

        return view('backend.artist-institution.list', compact(
            'institutionTypes',
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

        $institutionType = null;

        if ($id) {
            $institutionTypeId = decrypt($id);
            $institutionTypeQuery = DB::table('art_institution_types')
                ->where('id', $institutionTypeId);

            if ($authRoleId === 8) {
                abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
                $institutionTypeQuery->where('country_id', $authCountryId);
            }

            $institutionType = $institutionTypeQuery->first();
            abort_unless($institutionType, 404);
        }

        $countries = DB::table('countries')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('backend.artist-institution.createEdit', compact(
            'institutionType',
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
            'id' => ['nullable', 'integer', 'exists:art_institution_types,id'],
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
            'name.required' => 'Please enter institution type name.',
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
            $existingType = DB::table('art_institution_types')->where('id', $id);

            if ($authRoleId === 8) {
                $existingType->where('country_id', $authCountryId);
            }

            abort_unless($existingType->exists(), 404);
            $existingType->update($data);
            $message = 'Institution type updated successfully.';
        } else {
            $data['created_at'] = now();

            if (Schema::hasColumn('art_institution_types', 'updated_at')) {
                $data['updated_at'] = now();
            }

            DB::table('art_institution_types')->insert($data);
            $message = 'Institution type created successfully.';
        }

        return redirect()->route('cp.artist-institution.list')->with('success', $message);
    }

    public function statusChange(Request $request)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:art_institution_types,id'],
            'status' => ['required', 'in:0,1'],
        ]);

        $typeQuery = DB::table('art_institution_types')
            ->where('id', $validated['id']);

        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            $typeQuery->where('country_id', $authCountryId);
        }

        abort_unless($typeQuery->exists(), 404);

        $data = ['status' => (int) $validated['status']];

        if (Schema::hasColumn('art_institution_types', 'updated_at')) {
            $data['updated_at'] = now();
        }

        $typeQuery->update($data);

        return response()->json([
            'success' => true,
            'status' => (int) $validated['status'],
            'message' => $validated['status']
                ? 'Institution type activated successfully.'
                : 'Institution type deactivated successfully.',
        ]);
    }
}
