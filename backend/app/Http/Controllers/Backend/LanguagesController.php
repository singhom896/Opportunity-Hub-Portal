<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class LanguagesController extends Controller
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

            $languages = DB::table('art_languages')
                ->leftJoin('countries', 'countries.id', '=', 'art_languages.country_id')
                ->select('art_languages.*', 'countries.name as country_name')
                ->where('art_languages.country_id', $authCountryId)
                ->orderByDesc('art_languages.id')
                ->get();
        } else {
            $selectedCountry = $request->input('country_id', $country_id);

            $languages = DB::table('art_languages')
                ->leftJoin('countries', 'countries.id', '=', 'art_languages.country_id')
                ->select('art_languages.*', 'countries.name as country_name')
                ->when($selectedCountry, fn ($q) => $q->where('art_languages.country_id', $selectedCountry))
                ->orderByDesc('art_languages.id')
                ->get();
        }

        return view('backend.languages.list', compact(
            'languages',
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

        $language = null;

        if ($id) {
            $languageId = decrypt($id);
            $languageQuery = DB::table('art_languages')->where('id', $languageId);

            if ($authRoleId === 8) {
                abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
                $languageQuery->where('country_id', $authCountryId);
            }

            $language = $languageQuery->first();
            abort_unless($language, 404);
        }

        $countries = DB::table('countries')
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('backend.languages.createEdit', compact(
            'language',
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
            'id' => ['nullable', 'integer', 'exists:art_languages,id'],
            'country_id' => [
                $authRoleId === 8 ? 'nullable' : 'required',
                'integer',
                Rule::exists('countries', 'id')->where('status', 1),
            ],
            'name' => ['required', 'string', 'max:100'],
            'iso_code' => ['required', 'string', 'max:10'],
            'status' => ['required', 'in:0,1'],
        ], [
            'country_id.required' => 'Please select a country.',
            'name.required' => 'Please enter language name.',
            'iso_code.required' => 'Please enter ISO code.',
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
            'iso_code' => trim($validated['iso_code']),
            'status' => (int) $validated['status'],
        ];

        if ($id) {
            $existingLanguage = DB::table('art_languages')->where('id', $id);

            if ($authRoleId === 8) {
                $existingLanguage->where('country_id', $authCountryId);
            }

            abort_unless($existingLanguage->exists(), 404);
            $existingLanguage->update($data);
            $message = 'Language updated successfully.';
        } else {
            $data['created_at'] = now();

            if (Schema::hasColumn('art_languages', 'updated_at')) {
                $data['updated_at'] = now();
            }

            DB::table('art_languages')->insert($data);
            $message = 'Language created successfully.';
        }

        return redirect()->route('cp.languages.list')->with('success', $message);
    }

    public function statusChange(Request $request)
    {
        $authRoleId = (int) authUserData('role_id');
        $authCountryId = authUserData('country_id');

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:art_languages,id'],
            'status' => ['required', 'in:0,1'],
        ]);

        $languageQuery = DB::table('art_languages')->where('id', $validated['id']);

        if ($authRoleId === 8) {
            abort_if(empty($authCountryId), 403, 'Your account is not linked to a country.');
            $languageQuery->where('country_id', $authCountryId);
        }

        abort_unless($languageQuery->exists(), 404);

        $data = ['status' => (int) $validated['status']];

        if (Schema::hasColumn('art_languages', 'updated_at')) {
            $data['updated_at'] = now();
        }

        $languageQuery->update($data);

        return response()->json([
            'success' => true,
            'status' => (int) $validated['status'],
            'message' => $validated['status']
                ? 'Language activated successfully.'
                : 'Language deactivated successfully.',
        ]);
    }
}
