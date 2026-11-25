<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ArtistAwardController extends Controller
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

            $awards = DB::table('art_award_masters')
                ->leftJoin('countries', 'countries.id', '=', 'art_award_masters.country_id')
                ->select('art_award_masters.*', 'countries.name as country_name')
                ->where('art_award_masters.country_id', $authCountryId)
                ->orderByDesc('art_award_masters.id')
                ->get();
        } else {
            $selectedCountry = $request->input('country_id', $country_id);

            $awards = DB::table('art_award_masters')
                ->leftJoin('countries', 'countries.id', '=', 'art_award_masters.country_id')
                ->select('art_award_masters.*', 'countries.name as country_name')
                ->when($selectedCountry, fn ($q) => $q->where('art_award_masters.country_id', $selectedCountry))
                ->orderByDesc('art_award_masters.id')
                ->get();
        }

        return view('backend.artist-awards.list', compact(
            'awards',
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

        $award = null;

        if ($id) {
            $awardId = decrypt($id);
            $awardQuery = DB::table('art_award_masters')
                ->where('id', $awardId);

            if ($authRoleId === 8) {
                abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
                $awardQuery->where('country_id', $authCountryId);
            }

            $award = $awardQuery->first();
            abort_unless($award, 404);
        }

        $countries = DB::table('countries')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('backend.artist-awards.createEdit', compact(
            'award',
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
            'id' => ['nullable', 'integer', 'exists:art_award_masters,id'],
            'country_id' => [
                $authRoleId === 8 ? 'nullable' : 'required',
                'integer',
                Rule::exists('countries', 'id')->where('status', 1),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:0,1'],
        ], [
            'country_id.required' => 'Please select a country.',
            'name.required' => 'Please enter award name.',
            'description.max' => 'Description cannot exceed 500 characters.',
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
            $existingAward = DB::table('art_award_masters')->where('id', $id);

            if ($authRoleId === 8) {
                $existingAward->where('country_id', $authCountryId);
            }

            abort_unless($existingAward->exists(), 404);
            $data['updated_at'] = now();
            $existingAward->update($data);
            $message = 'Award updated successfully.';
        } else {
            $data['created_at'] = now();

            if (Schema::hasColumn('art_award_masters', 'updated_at')) {
                $data['updated_at'] = now();
            }

            DB::table('art_award_masters')->insert($data);
            $message = 'Award created successfully.';
        }

        return redirect()->route('cp.artist-awards.list')->with('success', $message);
    }

    public function statusChange(Request $request)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:art_award_masters,id'],
            'status' => ['required', 'in:0,1'],
        ]);

        $awardQuery = DB::table('art_award_masters')
            ->where('id', $validated['id']);

        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            $awardQuery->where('country_id', $authCountryId);
        }

        abort_unless($awardQuery->exists(), 404);

        $data = ['status' => (int) $validated['status']];

        if (Schema::hasColumn('art_award_masters', 'updated_at')) {
            $data['updated_at'] = now();
        }

        $awardQuery->update($data);

        return response()->json([
            'success' => true,
            'status' => (int) $validated['status'],
            'message' => $validated['status']
                ? 'Award activated successfully.'
                : 'Award deactivated successfully.',
        ]);
    }
}
